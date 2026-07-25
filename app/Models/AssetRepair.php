<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetRepair extends Model
{
    use HasFactory;

    protected $fillable = ['asset_id', 'ticket_id', 'technician_id', 'repair_date', 'complaint', 'diagnosis', 'repair_action', 'replaced_components', 'repair_cost', 'result', 'notes', 'next_maintenance_date'];

    protected function casts(): array
    {
        return ['repair_date' => 'date', 'next_maintenance_date' => 'date', 'repair_cost' => 'decimal:2'];
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
