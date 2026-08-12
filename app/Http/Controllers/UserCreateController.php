<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\UserInfo;
use App\Models\Company;


class UserCreateController extends Controller
{

    public function index(Request $request)
    {
        $query = User::with('department')
            ->whereIn('role', ['admin', 'user']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        $users = $query->latest()->get();

        $departments = Department::latest()->get();

        return view(
            'super-admin.employee-management.add-employee.index',
            compact('users', 'departments')
        );
    }

    public function userProfile()
    {
        $users = Auth::user()->load('department');

        return view('user-profile.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:6',
            'role'       => 'required|in:admin,user',
            'department' => 'required',
        ]);

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role'          => $request->role,
            'department_id' => $request->department,
            'company_id'    => (app()->bound('current_company') && app('current_company'))
                ? app('current_company')->id
                : Company::first()->id ?? Company::create(['name' => 'Default Company'])->id,
        ]);

        audit_log(
            'Employee',
            'Create',
            'Employee Created',
            $user->id,
            null,
            "Employee '{$user->name}' created.",
            null,
            json_encode($user->toArray()),
            $user->id
        );



        return redirect()->back()->with('success', 'User created successfully');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'       => 'required',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'role'       => 'required|in:admin,user',
            'department' => 'required',
            'password'   => 'nullable|min:6'
        ]);

        $oldData = $user->toArray();

       

        $data = [
            'name'          => $request->name,
            'email'         => $request->email,
            'role'          => $request->role,
            'department_id' => $request->department,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // dd($data, $user);

       $audit_log = audit_log(
            'Employee',
            'Update',
            'Employee Updated',
            $user->id,
            null,
            "Employee '{$user->name}' updated.",
            json_encode($oldData),
            json_encode($user->fresh()->toArray()),
            $user->id
        );

        // dd($audit_log);

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'super_admin') {
            return redirect()->back()->with('error', 'Super Admin cannot be deleted!');
        }

        $oldData = $user->toArray();

        audit_log(
            'Employee',
            'Delete',
            'Employee Deleted',
            $user->id,
            null,
            "Employee '{$user->name}' deleted.",
            json_encode($oldData),
            null,
            $user->id
        );

        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully!');
    }

    public function userinfo(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $user = Auth::user();

        // Get existing userinfo (if any)
        $userinfo = $user->userinfo; // relationship

        // Handle photo upload
        if ($request->hasFile('photo')) {

            // Optional: delete old photo
            if ($userinfo && $userinfo->profile_photo) {
                \Storage::disk('public')->delete($userinfo->profile_photo);
            }

            $photoPath = $request->file('photo')->store('photos', 'public');
        } else {
            $photoPath = $userinfo->profile_photo ?? null;
        }

        // CREATE or UPDATE
        UserInfo::updateOrCreate(
            ['user_id' => $user->id],   // condition
            [
                'phone_number'  => $request->phone,
                'profile_photo' => $photoPath,
            ]
        );

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}
