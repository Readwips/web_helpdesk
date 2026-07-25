<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketWorkflowService
{
    public function history(Ticket $ticket, User $actor, string $action, ?string $old = null, ?string $new = null, ?string $note = null, array $metadata = []): void
    {
        $ticket->histories()->create(['changed_by' => $actor->id, 'action' => $action, 'old_status' => $old, 'new_status' => $new, 'note' => $note, 'metadata' => $metadata ?: null]);
    }

    public function assign(Ticket $ticket, User $actor, User $technician, string $priority): void
    {
        DB::transaction(function () use ($ticket, $actor, $technician, $priority) {
            $old = $ticket->status;
            $oldPriority = $ticket->priority;
            $ticket->update(['technician_id' => $technician->id, 'priority' => $priority, 'status' => 'ditugaskan']);
            $this->history($ticket, $actor, 'Teknisi ditugaskan', $old, 'ditugaskan', null, ['technician' => $technician->name, 'old_priority' => $oldPriority, 'new_priority' => $priority]);
            if ($oldPriority !== $priority) {
                $this->history($ticket, $actor, 'Prioritas berubah', note: $oldPriority.' → '.$priority);
            }
        });
    }

    public function updateStatus(Ticket $ticket, User $actor, string $status, array $data = []): void
    {
        DB::transaction(function () use ($ticket, $actor, $status, $data) {
            if (in_array($status, ['menunggu_konfirmasi', 'selesai'], true) && blank($data['solution'] ?? $ticket->solution)) {
                throw ValidationException::withMessages(['solution' => 'Solusi wajib diisi sebelum tiket diselesaikan.']);
            }
            $old = $ticket->status;
            $oldDiagnosis = $ticket->diagnosis;
            $oldSolution = $ticket->solution;
            $values = ['status' => $status];
            foreach (['diagnosis', 'solution'] as $field) {
                if (array_key_exists($field, $data)) {
                    $values[$field] = $data[$field];
                }
            } if ($status === 'diproses' && ! $ticket->started_at) {
                $values['started_at'] = now();
            } if ($status === 'menunggu_konfirmasi') {
                $values['resolved_at'] = now();
            } if ($status === 'selesai') {
                $values['confirmed_at'] = now();
            }
            $ticket->update($values);
            $this->history($ticket, $actor, 'Status berubah', $old, $status, $data['note'] ?? null);
            if (array_key_exists('diagnosis', $data) && $data['diagnosis'] !== $oldDiagnosis) {
                $this->history($ticket, $actor, 'Diagnosis ditambahkan');
            }
            if (array_key_exists('solution', $data) && $data['solution'] !== $oldSolution) {
                $this->history($ticket, $actor, 'Solusi ditambahkan');
            }
        });
    }
}
