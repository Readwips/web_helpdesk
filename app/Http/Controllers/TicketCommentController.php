<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Services\TicketWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketCommentController extends Controller
{
    public function store(Request $r, Ticket $ticket, TicketWorkflowService $workflow)
    {
        $this->authorize('view', $ticket);
        $data = $r->validate(['comment' => 'required|string|max:5000', 'is_internal' => 'nullable|boolean', 'attachments' => 'nullable|array|max:5', 'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120']);
        $internal = ($data['is_internal'] ?? false) && in_array($r->user()->role, ['admin', 'technician'], true);
        $comment = $ticket->comments()->create(['user_id' => $r->user()->id, 'comment' => $data['comment'], 'is_internal' => $internal]);
        foreach ($r->file('attachments', []) as $file) {
            $path = $file->store('ticket-attachments', 'local');
            $comment->attachments()->create(['ticket_id' => $ticket->id, 'uploaded_by' => $r->user()->id, 'original_name' => $file->getClientOriginalName(), 'stored_name' => basename($path), 'file_path' => $path, 'mime_type' => $file->getMimeType(), 'file_size' => $file->getSize()]);
        }$workflow->history($ticket, $r->user(), $internal ? 'Catatan internal ditambahkan' : 'Komentar ditambahkan');

        return back()->with('success', 'Komentar ditambahkan.');
    }

    public function download(TicketAttachment $attachment)
    {
        $attachment->load('comment');
        $this->authorize('view', $attachment->ticket);
        abort_if($attachment->comment?->is_internal && request()->user()->role === 'user', 403);
        abort_unless(Storage::disk('local')->exists($attachment->file_path), 404);

        return Storage::disk('local')->download($attachment->file_path, $attachment->original_name, ['Content-Type' => $attachment->mime_type, 'X-Content-Type-Options' => 'nosniff']);
    }
}
