<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin() || ($user->isTechnician() && $ticket->technician_id === $user->id) || $ticket->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'user' || $user->isAdmin();
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin() || ($ticket->user_id === $user->id && in_array($ticket->status, ['baru', 'ditugaskan'], true));
    }

    public function handle(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin() || ($user->isTechnician() && $ticket->technician_id === $user->id);
    }

    public function confirm(User $user, Ticket $ticket): bool
    {
        return $ticket->user_id === $user->id;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }
}
