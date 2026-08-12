@extends('layout.index')

@section('title', 'Dashboard')

@section('content')



<div class="mt-2 card py-3 px-3" id="filterSection" style="display:none;">

    <form method="GET" action="{{ route('roles.index') }}">

        <div class="row g-3 align-items-end">

            <div class="col-md-3">
                <label class="form-label">Role Name</label>

                <input type="text"
                    class="form-control"
                    name="search"
                    placeholder="Search Role"
                    value="{{ request('search') }}">
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Search
                </button>
                <button type="button"
                    class="btn btn-secondary "
                    onclick="window.location.href='{{ route('roles.index') }}'">
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

            <h5 class="mb-0">Roles List</h5>

            <div class="d-flex gap-2">

                <x-filter-button target="filterSection" />

                <button type="button"
                    class="btn btn-sm btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#addRoleModal">
                    Add Role
                </button>

            </div>

        </div>

        <h5 class="mb-4"></h5>


        <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Role Name</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $role->name }}</td>
                        <td>
                            <a href="javascript:;"
                                class="btn btn-sm text-warning editRoleBtn"
                                data-id="{{ $role->id }}"
                                data-name="{{ $role->name }}">
                                <i class="lni lni-pencil-alt fs-6"></i>
                            </a>

                            <form action="{{ route('roles.destroy', $role->id) }}"
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
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editRoleForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Role Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
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

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addRoleModalLabel">
                    Add Role
                </h5>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>

            <form method="POST" action="{{ route('roles.store') }}">
                @csrf

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Role Name</label>

                        <div class="position-relative input-icon">
                            <input type="text"
                                class="form-control"
                                name="name"
                                placeholder="Enter Role Name"
                                required>

                            <span class="position-absolute top-50 translate-middle-y">
                                <i class="material-icons-outlined fs-5">person_outline</i>
                            </span>
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
                        Add Role
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection