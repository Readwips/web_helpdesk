<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use App\Services\AssetAssignmentService;
use Illuminate\Http\Request;

class AssetAssignmentController extends Controller
{
    public function create(Asset $asset)
    {
        $this->authorize('update', $asset);

        return view('assets.assign', ['asset' => $asset, 'users' => User::where('role', 'user')->where('status', 'active')->get()]);
    }

    public function store(Request $r, Asset $asset, AssetAssignmentService $service)
    {
        $this->authorize('update', $asset);
        $data = $r->validate(['user_id' => 'required|exists:users,id', 'status' => 'required|in:digunakan,dipinjamkan', 'notes' => 'nullable|string']);
        $user = User::where('role', 'user')->findOrFail($data['user_id']);
        $service->assign($asset, $user, $r->user(), $data);

        return to_route('assets.show', $asset)->with('success', 'Aset berhasil ditugaskan.');
    }

    public function return(Request $r, Asset $asset, AssetAssignmentService $service)
    {
        $this->authorize('update', $asset);
        $data = $r->validate(['condition' => 'required|in:baik,perlu_perawatan,rusak_ringan,rusak_berat', 'notes' => 'nullable|string']);
        $service->return($asset, $data);

        return back()->with('success','Pengembalian aset dicatat.');
    }
}
