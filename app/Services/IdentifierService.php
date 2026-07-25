<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Ticket;
use Illuminate\Support\Str;

class IdentifierService
{
    public function ticketNumber(): string
    {
        $prefix = 'TKT-'.now()->format('Ym').'-';
        $last = Ticket::withTrashed()->where('ticket_number', 'like', $prefix.'%')->lockForUpdate()->orderByDesc('ticket_number')->value('ticket_number');

        return $prefix.str_pad((string) (($last ? (int) Str::afterLast($last, '-') : 0) + 1), 4, '0', STR_PAD_LEFT);
    }

    public function assetCode(AssetCategory $category): string
    {
        $prefix = 'AST-'.$category->code.'-';
        $last = Asset::withTrashed()->where('asset_code', 'like', $prefix.'%')->lockForUpdate()->orderByDesc('asset_code')->value('asset_code');

        return $prefix.str_pad((string) (($last ? (int) Str::afterLast($last, '-') : 0) + 1), 4, '0', STR_PAD_LEFT);
    }
}
