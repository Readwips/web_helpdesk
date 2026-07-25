<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    public function index()
    {
        return view('master.asset-categories', ['items' => AssetCategory::withCount('assets')->get()]);
    }

    public function store(Request $r)
    {
        AssetCategory::create($r->validate(['name' => 'required|max:100', 'code' => 'required|max:10|alpha_dash|unique:asset_categories', 'description' => 'nullable|string']));

        return back()->with('success', 'Kategori aset ditambahkan.');
    }

    public function update(Request $r, AssetCategory $asset_category)
    {
        $asset_category->update($r->validate(['name' => 'required|max:100', 'code' => 'required|max:10|alpha_dash|unique:asset_categories,code,'.$asset_category->id, 'description' => 'nullable|string']));

        return back()->with('success', 'Kategori aset diperbarui.');
    }

    public function destroy(AssetCategory $asset_category)
    {
        abort_if($asset_category->assets()->exists(), 422, 'Kategori masih digunakan.');
        $asset_category->delete();

        return back()->with('success', 'Kategori dihapus.');
    }
}
