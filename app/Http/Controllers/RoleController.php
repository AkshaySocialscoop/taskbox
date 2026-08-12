<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    // 📄 Show all roles
    public function index(Request $request)
    {
        $query = Role::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $roles = $query->latest()->get();

        return view('super-admin.employee-management.add-role.index', compact('roles'));
    }


    // 💾 Store new role
    public function store(Request $request)
    {
        $user = auth()->user();
        $company_id = $user->company->id;

        $request->validate([
            'name' => 'required|unique:roles,name|max:255',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'company_id' => $company_id,
        ]);

        audit_log(
            'Role',
            'Create',
            'Role Created',
            $role->id,
            null,
            "Role '{$role->name}' created.",
            null,
            json_encode($role->toArray())
        );

        return back()->with('success', 'Role created successfully');
    }

    // ✏️ Show edit form
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('roles.edit', compact('role'));
    }

    // 🔄 Update role
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255|unique:roles,name,' . $id,
        ]);

        $oldData = $role->toArray();

        $role->update([
            'name' => $request->name
        ]);

        audit_log(
            'Role',
            'Update',
            'Role Updated',
            $role->id,
            'name',
            "Role '{$role->name}' updated.",
            json_encode($oldData),
            json_encode($role->fresh()->toArray())
        );

        return back()->with('success', 'Role updated successfully');
    }

    // ❌ Delete role
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        $oldData = $role->toArray();

        audit_log(
            'Role',
            'Delete',
            'Role Deleted',
            $role->id,
            null,
            "Role '{$role->name}' deleted.",
            json_encode($oldData),
            null
        );

        $role->delete();

        return back()->with('success', 'Role deleted successfully');
    }
}
