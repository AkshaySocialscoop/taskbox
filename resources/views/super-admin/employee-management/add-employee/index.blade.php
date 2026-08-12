@extends('layout.index')

@section('title', 'Dashboard')

@section('content')



    <!-- Page Heading -->
   

    <div class="mt-2 card px-3 py-4" id="filterSection" style="display:none;">

        <form method="GET" action="{{ route('users.index') }}">

            <div class="row g-3 align-items-end">

                <div class="col-md-3">
                    <label class="form-label">Employee Name</label>
                    <input type="text"
                        class="form-control"
                        name="search"
                        placeholder="Search Employee"
                        value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input type="text"
                        class="form-control"
                        name="email"
                        placeholder="Search Email"
                        value="{{ request('email') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Role</label>
                    <select class="form-select" name="role">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Department</label>
                    <select class="form-select" name="department">
                        <option value="">All Departments</option>

                        @foreach($departments as $department)
                        <option value="{{ $department->id }}"
                            {{ request('department') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        Search
                    </button>
                    <button type="button"
                        class="btn btn-secondary "
                        onclick="window.location.href='{{ route('users.index') }}'">
                        Reset
                    </button>
                </div>

            </div>

        </form>

    </div>




<!-- Employee List -->
<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h5 class="mb-0">Employee List</h5>
            <div class="d-flex gap-2">

                <x-filter-button target="filterSection" />

                <button type="button"
                    class="btn btn-sm btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#addEmployeeModal">
                    Add Employee
                </button>

            </div>

           

        </div>

        <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class=" ">
                                {{ ucfirst($user->department->name) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->role == 'admin' ? 'primary' : 'success' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            <a href="javascript:;"
                                class="btn btn-sm text-warning editUserBtn"
                                data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}"
                                data-role="{{ $user->role }}"
                                data-department="{{ $user->department_id }}">
                                <i class="lni lni-pencil-alt fs-6"></i>
                            </a>

                            <form action="{{ route('users.destroy', $user->id) }}"
                                method="POST"
                                class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="btn btn-sm text-danger"> <i class="lni lni-trash fs-6 "></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No employees found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">x</button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" id="edit_role" class="form-control">
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <div class="md-3">
                        <label for="input22" class="form-label">Department</label>
                        <select id="edit_department" class="form-select" name="department" required>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Password (Optional)</label>
                        <input type="password" name="password" class="form-control"
                            placeholder="Leave blank to keep old password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addEmployeeModalLabel">
                    Add Employee
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal">x</button>
            </div>

            <form method="POST" action="/super-admin/store-users">
                @csrf

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">First Name</label>
                            <div class="position-relative input-icon">
                                <input type="text"
                                    class="form-control"
                                    placeholder="First Name"
                                    name="name"
                                    required>

                                <span class="position-absolute top-50 translate-middle-y">
                                    <i class="material-icons-outlined fs-5">person_outline</i>
                                </span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <div class="position-relative input-icon">
                                <input type="email"
                                    class="form-control"
                                    placeholder="Email"
                                    name="email"
                                    required>

                                <span class="position-absolute top-50 translate-middle-y">
                                    <i class="material-icons-outlined fs-5">email</i>
                                </span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Password</label>
                            <div class="position-relative input-icon">
                                <input type="password"
                                    class="form-control"
                                    placeholder="Password"
                                    name="password"
                                    required>

                                <span class="position-absolute top-50 translate-middle-y">
                                    <i class="material-icons-outlined fs-5">lock_open</i>
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role" required>
                                <option value="">--Select--</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <select class="form-select" name="department" required>
                                <option value="">--Select--</option>

                                @foreach($departments as $department)
                                <option value="{{ $department->id }}">
                                    {{ $department->name }}
                                </option>
                                @endforeach

                            </select>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Add Employee
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection