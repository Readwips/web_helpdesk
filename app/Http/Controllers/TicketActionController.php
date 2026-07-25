<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketWorkflowService;
use Illuminate\Http\Request;

class TicketActionController extends Controller
{
    public function assign(Request $r, Ticket $ticket, TicketWorkflowService $service)
    {
        $this->authorize('handle', $ticket);
        $data = $r->validate(['technician_id' => 'required|exists:users,id', 'priority' => 'required|in:rendah,sedang,tinggi,kritis']);
        $technician = User::where('role', 'technician')->where('status', 'active')->findOrFail($data['technician_id']);
        $service->assign($ticket, $r->user(), $technician, $data['priority']);

        return back()->with('success', 'Teknisi berhasil ditugaskan.');
    }

    public function status(Request $r, Ticket $ticket, TicketWorkflowService $service)
    {
        $this->authorize('handle', $ticket);
        $data = $r->validate(['status' => 'required|in:diproses,menunggu_konfirmasi,dibatalkan', 'diagnosis' => 'nullable|required_if:status,menunggu_konfirmasi|string', 'solution' => 'nullable|required_if:status,menunggu_konfirmasi|string', 'note' => 'nullable|string']);
        $service->updateStatus($ticket, $r->user(), $data['status'], $data);

        return back()->with('success', 'Status tiket diperbarui.');
    }

    public function confirm(Request $r, Ticket $ticket, TicketWorkflowService $service)
    {
        $this->authorize('confirm', $ticket);
        abort_unless($ticket->status === 'menunggu_konfirmasi', 422);
        $service->updateStatus($ticket, $r->user(), 'selesai');

        return back()->with('success', 'Penyelesaian dikonfirmasi.');
    }

    public function reopen(Request $r, Ticket $ticket, TicketWorkflowService $service)
    {
        $this->authorize('confirm', $ticket);
        abort_unless(in_array($ticket->status, ['menunggu_konfirmasi', 'selesai'], true), 422);
        $data = $r->validate(['note' => 'required|string|max:2000']);
        $service->updateStatus($ticket, $r->user(), 'diproses', $data);

        return back()->with('success','Tiket dibuka kembali.');
    }
}
