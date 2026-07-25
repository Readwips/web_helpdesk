<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssetAssignmentService
{
    public function assign(Asset $asset, User $user, User $actor, array $data): AssetAssignment
    {
        return DB::transaction(function () use ($asset, $user, $actor, $data) {
            $asset = Asset::lockForUpdate()->findOrFail($asset->id);
            if ($asset->assignments()->whereNull('returned_at')->exists()) {
                throw ValidationException::withMessages(['asset' => 'Aset masih memiliki penugasan aktif.']);
            }if ($asset->status === 'diperbaiki' || $asset->condition === 'rusak_berat') {
                throw ValidationException::withMessages(['asset' => 'Aset yang diperbaiki atau rusak berat tidak dapat ditugaskan.']);
            }$assignment = $asset->assignments()->create(['user_id' => $user->id, 'assigned_by' => $actor->id, 'assigned_at' => now(), 'condition_when_assigned' => $asset->condition, 'notes' => $data['notes'] ?? null]);
            $asset->update(['assigned_user_id' => $user->id, 'status' => $data['status']]);

            return $assignment;
        });
    }

    public function return(Asset $asset, array $data): void
    {
        DB::transaction(function () use ($asset, $data) {
            $asset = Asset::lockForUpdate()->findOrFail($asset->id);
            $assignment = $asset->assignments()->whereNull('returned_at')->lockForUpdate()->firstOrFail();
            $assignment->update(['returned_at' => now(), 'condition_when_returned' => $data['condition'], 'notes' => $data['notes'] ?? $assignment->notes]);
            $asset->update(['assigned_user_id' => null, 'condition' => $data['condition'], 'status' => $data['condition'] === 'rusak_berat' ? 'rusak' : 'tersedia']);
        });
    }
}
