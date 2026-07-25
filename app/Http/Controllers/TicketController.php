<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketRequest;
use App\Models\Asset;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\IdentifierService;
use App\Services\TicketWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index(Request $r)
    {
        $this->authorize('viewAny', Ticket::class);
        $u = $r->user();
        $q = Ticket::with(['user', 'technician', 'category'])->when($u->role === 'user', fn ($x) => $x->where('user_id', $u->id))->when($u->role === 'technician', fn ($x) => $x->where('technician_id', $u->id))->when($r->q, fn ($x, $s) => $x->where(fn ($z) => $z->where('ticket_number', 'like', "%$s%")->orWhere('title', 'like', "%$s%")->orWhereHas('user', fn ($a) => $a->where('name', 'like', "%$s%"))->orWhereHas('technician', fn ($a) => $a->where('name', 'like', "%$s%"))))->when($r->status, fn ($x, $v) => $x->where('status', $v))->when($r->priority, fn ($x, $v) => $x->where('priority', $v))->when($r->ticket_category_id, fn ($x, $v) => $x->where('ticket_category_id', $v))->when($r->technician_id, fn ($x, $v) => $x->where('technician_id', $v))->when($r->user_id, fn ($x, $v) => $x->where('user_id', $v))->when($r->date_from, fn ($x, $v) => $x->whereDate('created_at', '>=', $v))->when($r->date_to, fn ($x, $v) => $x->whereDate('created_at', '<=', $v))->orderByRaw("CASE priority WHEN 'kritis' THEN 1 WHEN 'tinggi' THEN 2 WHEN 'sedang' THEN 3 ELSE 4 END")->latest();

        return view('tickets.index', ['tickets' => $q->paginate(15)->withQueryString(), 'categories' => TicketCategory::all(), 'technicians' => User::where('role', 'technician')->where('status', 'active')->get()]);
    }

    public function create(Request $r)
    {
        $this->authorize('create', Ticket::class);

        return view('tickets.form', ['ticket' => new Ticket, 'categories' => TicketCategory::all(), 'assets' => $r->user()->isAdmin() ? Asset::all() : Asset::where('assigned_user_id', $r->user()->id)->get()]);
    }

    public function store(TicketRequest $r, IdentifierService $ids, TicketWorkflowService $workflow)
    {
        $this->authorize('create', Ticket::class);
        $ticket = DB::transaction(function () use ($r, $ids, $workflow) {
            $t = Ticket::create($r->validated() + ['ticket_number' => $ids->ticketNumber(), 'user_id' => $r->user()->id, 'status' => 'baru']);
            $workflow->history($t, $r->user(), 'Tiket dibuat', null, 'baru');

            return $t;
        });

        return to_route('tickets.show', $ticket)->with('success', 'Tiket berhasil dibuat.');
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        $ticket->load(['user.department', 'technician', 'category', 'asset', 'comments.user', 'comments.attachments', 'attachments', 'histories.actor']);
        $comments = request()->user()->role === 'user' ? $ticket->comments->where('is_internal', false) : $ticket->comments;

        return view('tickets.show', compact('ticket', 'comments') + ['technicians' => User::where('role', 'technician')->where('status', 'active')->get()]);
    }

    public function edit(Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        return view('tickets.form', ['ticket' => $ticket, 'categories' => TicketCategory::all(), 'assets' => Asset::where('assigned_user_id', $ticket->user_id)->orWhere('id', $ticket->asset_id)->get()]);
    }

    public function update(TicketRequest $r, Ticket $ticket, TicketWorkflowService $workflow)
    {
        $this->authorize('update', $ticket);
        $ticket->update($r->validated());
        $workflow->history($ticket, $r->user(), 'Tiket diedit', note: 'Data tiket diperbarui.');

        return to_route('tickets.show',$ticket)->with('success','Tiket diperbarui.');
    }

    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete',$ticket);
        $ticket->delete();

        return to_route('tickets.index')->with('success','Tiket dihapus secara lunak.');
    }
}
