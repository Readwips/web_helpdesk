<?php

namespace Tests\Feature;

use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnowledgeReportAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $technician;

    private User $user;

    private KnowledgeCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->technician = User::factory()->create(['role' => 'technician']);
        $this->user = User::factory()->create(['role' => 'user']);
        $this->category = KnowledgeCategory::create(['name' => 'Windows', 'slug' => 'windows']);
    }

    private function article(string $status = 'published', array $overrides = []): KnowledgeArticle
    {
        return KnowledgeArticle::create(array_merge(['knowledge_category_id' => $this->category->id, 'author_id' => $this->admin->id, 'title' => 'Windows berjalan lambat '.$status, 'slug' => 'windows-lambat-'.$status, 'summary' => 'Panduan singkat', 'cause' => 'Temporary files menumpuk', 'solution_steps' => 'Bersihkan temporary files', 'status' => $status, 'published_at' => $status === 'published' ? now() : null], $overrides));
    }

    public function test_published_article_is_visible_and_counter_increases(): void
    {
        $article = $this->article();
        $this->actingAs($this->user)->get(route('knowledge.show', $article))->assertOk()->assertSee($article->title);
        $this->assertSame(1, $article->refresh()->view_count);
    }

    public function test_draft_is_hidden_from_regular_user_but_visible_to_author(): void
    {
        $article = $this->article('draft', ['author_id' => $this->technician->id]);
        $this->actingAs($this->user)->get(route('knowledge.show', $article))->assertForbidden();
        $this->actingAs($this->technician)->get(route('knowledge.show', $article))->assertOk();
    }

    public function test_technician_can_create_article_but_cannot_archive_it(): void
    {
        $this->actingAs($this->technician)->post(route('knowledge.store'), ['knowledge_category_id' => $this->category->id, 'title' => 'Perbaikan aplikasi', 'summary' => 'Ringkas', 'cause' => 'Konfigurasi', 'solution_steps' => 'Atur ulang konfigurasi', 'status' => 'archived'])->assertRedirect();
        $this->assertDatabaseHas('knowledge_articles', ['title' => 'Perbaikan aplikasi', 'author_id' => $this->technician->id, 'status' => 'draft']);
    }

    public function test_regular_user_cannot_open_admin_or_report_pages(): void
    {
        $this->actingAs($this->user)->get(route('users.index'))->assertForbidden();
        $this->actingAs($this->technician)->get(route('users.index'))->assertForbidden();
        $this->actingAs($this->user)->get(route('reports.index'))->assertForbidden();
    }

    public function test_report_filter_and_exports_use_real_data(): void
    {
        $ticketCategory = TicketCategory::create(['name' => 'Hardware']);
        Ticket::create(['ticket_number' => 'TKT-202607-0001', 'user_id' => $this->user->id, 'ticket_category_id' => $ticketCategory->id, 'title' => 'TIKET KRITIS TERLIHAT', 'description' => 'Masalah', 'location' => 'Lantai 1', 'priority' => 'kritis', 'status' => 'baru']);
        Ticket::create(['ticket_number' => 'TKT-202607-0002', 'user_id' => $this->user->id, 'ticket_category_id' => $ticketCategory->id, 'title' => 'TIKET SELESAI TERSEMBUNYI', 'description' => 'Masalah', 'location' => 'Lantai 1', 'priority' => 'rendah', 'status' => 'selesai', 'solution' => 'Selesai']);
        $this->actingAs($this->admin)->get(route('reports.index', ['type' => 'tickets', 'status' => 'baru']))->assertOk()->assertSee('TIKET KRITIS TERLIHAT')->assertDontSee('TIKET SELESAI TERSEMBUNYI');
        $this->actingAs($this->admin)->get(route('reports.excel', ['type' => 'tickets', 'status' => 'baru']))->assertOk()->assertHeader('content-disposition');
        $this->actingAs($this->admin)->get(route('reports.pdf', ['type' => 'tickets', 'status' => 'baru']))->assertOk()->assertHeader('content-type', 'application/pdf');
    }

    public function test_inactive_account_cannot_log_in_or_use_system(): void
    {
        $inactive = User::factory()->create(['status' => 'inactive', 'password' => 'password']);
        $this->post(route('login'), ['email' => $inactive->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->actingAs($inactive)->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_all_active_roles_reach_scoped_dashboard(): void
    {
        foreach ([$this->admin, $this->technician, $this->user] as $account) {
            $this->actingAs($account)->get(route('dashboard'))->assertOk()->assertSee($account->name);
        }
    }
}
