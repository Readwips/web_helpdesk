<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetRepairRequest;
use App\Models\Asset;
use App\Models\AssetRepair;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class AssetRepairController extends Controller
{
    public function create(Asset $asset)
    {
        abort_unless(in_array(request()->user()->role, ['admin', 'technician'], true), 403);

        return view('repairs.form', ['asset' => $asset, 'repair' => new AssetRepair, 'tickets' => Ticket::where('asset_id', $asset->id)->get()]);
    }

    public function store(AssetRepairRequest $r, Asset $asset)
    {
        $repair = DB::transaction(function () use ($r, $asset) {
            $data = $r->validated();
            $condition = $data['asset_condition'];
            $status = $data['asset_status'];
            unset($data['asset_condition'],$data['asset_status']);
            $repair = $asset->repairs()->create($data + ['technician_id' => $r->user()->id]);
            $asset->update(['condition' => $condition, 'status' => $status]);

            return $repair;
        });

        return to_route('assets.show', $asset)->with('success', 'Riwayat perbaikan ditambahkan.');
    }

    public function show(AssetRepair $repair)
    {
        abort_unless(request()->user()->role !== 'user', 403);
        $repair->load(['asset', 'ticket', 'technician']);

        return view('repairs.show', compact('repair'));
    }

    public function edit(AssetRepair $repair)
    {
        abort_unless(request()->user()->isAdmin() || $repair->technician_id === request()->user()->id, 403);

        return view('repairs.form', ['asset' => $repair->asset, 'repair' => $repair, 'tickets' => Ticket::where('asset_id', $repair->asset_id)->get()]);
    }

    public function update(AssetRepairRequest $r, AssetRepair $repair)
    {
        abort_unless($r->user()->isAdmin() || $repair->technician_id === $r->user()->id, 403);
        DB::transaction(function () use ($r, $repair) {
            $data = $r->validated();
            $repair->asset->update(['condition' => $data['asset_condition'], 'status' => $data['asset_status']]);
            unset($data['asset_condition'],$data['asset_status']);
            $repair->update($data);
        });

        return to_route('assets.show', $repair->asset)->with('success','Catatan perbaikan diperbarui.');
    }
}
