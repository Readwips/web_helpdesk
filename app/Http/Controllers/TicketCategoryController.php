<?php

namespace App\Http\Controllers;

use App\Models\TicketCategory;
use Illuminate\Http\Request;

class TicketCategoryController extends Controller
{
    public function index()
    {
        return view('master.index', ['title' => 'Kategori Tiket', 'items' => TicketCategory::withCount('tickets')->get(), 'route' => 'ticket-categories']);
    }

    public function store(Request $r)
    {
        TicketCategory::create($r->validate(['name' => 'required|max:100|unique:ticket_categories', 'description' => 'nullable|string']));

        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function update(Request $r, TicketCategory $ticket_category)
    {
        $ticket_category->update($r->validate(['name' => 'required|max:100|unique:ticket_categories,name,'.$ticket_category->id, 'description' => 'nullable|string']));

        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroy(TicketCategory $ticket_category)
    {
        abort_if($ticket_category->tickets()->exists(), 422, 'Kategori masih digunakan.');
        $ticket_category->delete();

        return back()->with('success', 'Kategori dihapus.');
    }
}
