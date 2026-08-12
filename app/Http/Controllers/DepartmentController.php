<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $departments = $query->latest()->get();

        return view('super-admin.employee-management.add-department.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $company_id = $user->company->id;

        $request->validate([
            'name' => 'required|unique:departments,name|max:255',
        ]);

        $department = Department::create([
            'name' => $request->name,
            'company_id' => $company_id,
        ]);

        audit_log(
            'Department',
            'Create',
            'Department Created',
            $department->id,
            null,
            "Department '{$department->name}' created.",
            null,
            json_encode($department->toArray())
        );

        return back()->with('success', 'Department created successfully');
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255|unique:departments,name,' . $id,
        ]);

        $oldData = $department->toArray();

        $department->update([
            'name' => $request->name
        ]);

        audit_log(
            'Department',
            'Update',
            'Department Updated',
            $department->id,
            'name',
            "Department '{$department->name}' updated.",
            json_encode($oldData),
            json_encode($department->fresh()->toArray())
        );

        return back()->with('success', 'Department updated successfully');
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);

        $oldData = $department->toArray();

        audit_log(
            'Department',
            'Delete',
            'Department Deleted',
            $department->id,
            null,
            "Department '{$department->name}' deleted.",
            json_encode($oldData),
            null
        );

        $department->delete();

        return back()->with('success', 'Department deleted successfully');
    }
}
