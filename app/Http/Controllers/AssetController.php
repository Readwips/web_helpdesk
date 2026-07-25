<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetRequest;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Services\IdentifierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    public function index(Request $r)
    {
        $this->authorize('viewAny', Asset::class);
        $q = Asset::with(['category', 'assignedUser'])->when($r->user()->role === 'user', fn ($x) => $x->where('assigned_user_id', $r->user()->id))->when($r->q, fn ($x, $s) => $x->where(fn ($a) => $a->where('asset_code', 'like', "%$s%")->orWhere('serial_number', 'like', "%$s%")->orWhere('name', 'like', "%$s%")))->when($r->asset_category_id, fn ($x, $v) => $x->where('asset_category_id', $v))->when($r->condition, fn ($x, $v) => $x->where('condition', $v))->when($r->status, fn ($x, $v) => $x->where('status', $v))->when($r->location, fn ($x, $v) => $x->where('location', 'like', "%$v%"));

        return view('assets.index', ['assets' => $q->latest()->paginate(15)->withQueryString(), 'categories' => AssetCategory::all()]);
    }

    public function create()
    {
        $this->authorize('create', Asset::class);

        return view('assets.form', ['asset' => new Asset, 'categories' => AssetCategory::all()]);
    }

    public function store(AssetRequest $r, IdentifierService $ids)
    {
        $asset = DB::transaction(function () use ($r, $ids) {
            $cat = AssetCategory::lockForUpdate()->findOrFail($r->asset_category_id);

            return Asset::create($r->validated() + ['asset_code' => $ids->assetCode($cat)]);
        });

        return to_route('assets.show', $asset)->with('success', 'Aset ditambahkan.');
    }

    public function show(Asset $asset)
    {
        $this->authorize('view', $asset);
        $asset->load(['category', 'assignedUser', 'assignments.user', 'assignments.assigner', 'repairs.technician', 'repairs.ticket', 'tickets']);

        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $this->authorize('update', $asset);

        return view('assets.form', ['asset' => $asset, 'categories' => AssetCategory::all()]);
    }

    public function update(AssetRequest $r, Asset $asset)
    {
        $asset->update($r->validated());

        return to_route('assets.show', $asset)->with('success', 'Aset diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        $this->authorize('delete', $asset);
        abort_if($asset->assignments()->whereNull('returned_at')->exists(), 422, 'Aset masih ditugaskan.');
        $asset->delete();

        return to_route('assets.index')->with('success','Aset dihapus secara lunak.');
    }
}
