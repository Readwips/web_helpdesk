<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        return view('departments.index', ['departments' => Department::withCount('users')->paginate(15)]);
    }

    public function create()
    {
        return view('departments.form', ['department' => new Department]);
    }

    public function store(Request $r)
    {
        Department::create($r->validate(['name' => 'required|string|max:255|unique:departments', 'description' => 'nullable|string']));

        return to_route('departments.index')->with('success', 'Departemen ditambahkan.');
    }

    public function edit(Department $department)
    {
        return view('departments.form', compact('department'));
    }

    public function update(Request $r, Department $department)
    {
        $department->update($r->validate(['name' => 'required|string|max:255|unique:departments,name,'.$department->id, 'description' => 'nullable|string']));

        return to_route('departments.index')->with('success', 'Departemen diperbarui.');
    }

    public function destroy(Department $department)
    {
        if ($department->users()->exists()) {
            return back()->withErrors(['department' => 'Departemen masih digunakan.']);
        }$department->delete();

        return back()->with('success', 'Departemen dihapus.');
    }
}
