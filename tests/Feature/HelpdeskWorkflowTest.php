<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HelpdeskWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private TicketCategory $category;

    private User $admin;

    private User $technician;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = TicketCategory::create(['name' => 'Hardware']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->technician = User::factory()->create(['role' => 'technician']);
        $this->user = User::factory()->create(['role' => 'user']);
    }

    private function ticket(array $attributes = []): Ticket
    {
        return Ticket::create(array_merge([
            'ticket_number' => 'TKT-'.now()->format('Ym').'-0001',
            'user_id' => $this->user->id,
            'ticket_category_id' => $this->category->id,
            'title' => 'Printer tidak mencetak',
            'description' => 'Printer terdeteksi tetapi dokumen tidak keluar.',
            'location' => 'Lantai 2',
            'priority' => 'sedang',
            'status' => 'baru',
        ], $attributes));
    }

    public function test_user_can_create_ticket_with_unique_number_and_history(): void
    {
        $response = $this->actingAs($this->user)->post(route('tickets.store'), [
            'ticket_category_id' => $this->category->id,
            'title' => 'Wi-Fi terputus',
            'description' => 'Tidak dapat tersambung sejak pagi.',
            'location' => 'Ruang Keuangan',
            'priority' => 'tinggi',
        ]);
        $ticket = Ticket::first();
        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertMatchesRegularExpression('/^TKT-\d{6}-0001$/', $ticket->ticket_number);
        $this->assertDatabaseHas('ticket_histories', ['ticket_id' => $ticket->id, 'action' => 'Tiket dibuat']);
    }

    public function test_user_cannot_view_another_users_ticket(): void
    {
        $ticket = $this->ticket(['user_id' => User::factory()->create()->id]);
        $this->actingAs($this->user)->get(route('tickets.show', $ticket))->assertForbidden();
    }

    public function test_admin_assigns_technician_and_priority(): void
    {
        $ticket = $this->ticket();
        $this->actingAs($this->admin)->put(route('tickets.assign', $ticket), ['technician_id' => $this->technician->id, 'priority' => 'kritis'])->assertRedirect();
        $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'technician_id' => $this->technician->id, 'priority' => 'kritis', 'status' => 'ditugaskan']);
        $this->assertDatabaseHas('ticket_histories', ['ticket_id' => $ticket->id, 'action' => 'Teknisi ditugaskan']);
    }

    public function test_technician_cannot_change_another_technicians_ticket(): void
    {
        $ticket = $this->ticket(['technician_id' => User::factory()->create(['role' => 'technician'])->id, 'status' => 'ditugaskan']);
        $this->actingAs($this->technician)->put(route('tickets.status', $ticket), ['status' => 'diproses'])->assertForbidden();
    }

    public function test_complete_workflow_and_reopen_are_recorded(): void
    {
        $ticket = $this->ticket(['technician_id' => $this->technician->id, 'status' => 'ditugaskan']);
        $this->actingAs($this->technician)->put(route('tickets.status', $ticket), ['status' => 'diproses'])->assertRedirect();
        $this->actingAs($this->technician)->put(route('tickets.status', $ticket), ['status' => 'menunggu_konfirmasi', 'diagnosis' => 'Driver bermasalah', 'solution' => 'Driver dipasang ulang'])->assertRedirect();
        $this->actingAs($this->user)->put(route('tickets.confirm', $ticket))->assertRedirect();
        $this->assertSame('selesai', $ticket->refresh()->status);
        $this->assertNotNull($ticket->confirmed_at);
        $this->actingAs($this->user)->put(route('tickets.reopen', $ticket), ['note' => 'Masalah kembali terjadi'])->assertRedirect();
        $this->assertSame('diproses', $ticket->refresh()->status);
        $this->assertGreaterThanOrEqual(4, $ticket->histories()->count());
    }

    public function test_ticket_cannot_be_sent_for_confirmation_without_solution(): void
    {
        $ticket = $this->ticket(['technician_id' => $this->technician->id, 'status' => 'diproses']);
        $this->actingAs($this->technician)->from(route('tickets.show', $ticket))->put(route('tickets.status', $ticket), ['status' => 'menunggu_konfirmasi', 'diagnosis' => 'Ditemukan masalah'])->assertSessionHasErrors('solution');
        $this->assertSame('diproses', $ticket->refresh()->status);
    }

    public function test_internal_comment_is_hidden_from_user(): void
    {
        $ticket = $this->ticket(['technician_id' => $this->technician->id]);
        TicketComment::create(['ticket_id' => $ticket->id, 'user_id' => $this->technician->id, 'comment' => 'RAHASIA INTERNAL', 'is_internal' => true]);
        $this->actingAs($this->user)->get(route('tickets.show', $ticket))->assertOk()->assertDontSee('RAHASIA INTERNAL');
    }

    public function test_invalid_upload_is_rejected_and_valid_upload_is_private(): void
    {
        Storage::fake('local');
        $ticket = $this->ticket();
        $this->actingAs($this->user)->post(route('tickets.comments.store', $ticket), ['comment' => 'Lampiran log', 'attachments' => [UploadedFile::fake()->create('malware.exe', 10)]])->assertSessionHasErrors('attachments.0');
        $this->actingAs($this->user)->post(route('tickets.comments.store', $ticket), ['comment' => 'Lampiran bukti', 'attachments' => [UploadedFile::fake()->image('bukti.jpg')]])->assertRedirect();
        $attachment = $ticket->attachments()->first();
        Storage::disk('local')->assertExists($attachment->file_path);
        $outsider = User::factory()->create();
        $this->actingAs($outsider)->get(route('attachments.download', $attachment))->assertForbidden();
    }
}
