<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['asset_code', 'asset_category_id', 'assigned_user_id', 'name', 'brand', 'model', 'serial_number', 'specifications', 'purchase_date', 'purchase_price', 'warranty_end_date', 'location', 'condition', 'status', 'notes'];

    protected function casts(): array
    {
        return ['purchase_date' => 'date', 'warranty_end_date' => 'date', 'purchase_price' => 'decimal:2'];
    }

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function repairs()
    {
        return $this->hasMany(AssetRepair::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
