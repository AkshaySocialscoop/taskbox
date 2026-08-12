@extends('layout.index')

@section('title', 'Dashboard')

@section('content')



<div class="mt-2 card py-3 px-3" id="filterSection" style="display:none;">

    <form method="GET" action="{{ route('departments.index') }}">

        <div class="row g-3 align-items-end">

            <div class="col-md-4">
                <label class="form-label">Department Name</label>

                <input type="text"
                    class="form-control"
                    name="search"
                    placeholder="Search Department"
                    value="{{ request('search') }}">
            </div>

            <div class="col-md-2 d-flex  gap-2">
                <button type="submit" class="btn btn-primary">
                    Search
                </button>
                <button
                    type="button"
                    class="btn btn-secondary "
                    onclick="window.location.href='{{ route('departments.index') }}'">
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

            <h5 class="mb-0">Departments List</h5>
            <div class="d-flex gap-2">

                <x-filter-button target="filterSection" />

                <button type="button"
                    class="btn btn-sm btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#addDepartmentModal">
                    Add Department
                </button>

            </div>

        </div>

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
                    @forelse($departments as $department)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $department->name }}</td>
                        <td>
                            <a href="javascript:;"
                                class="btn btn-sm text-warning editDepartmentBtn"
                                data-id="{{ $department->id }}"
                                data-name="{{ $department->name }}">
                                <i class="lni lni-pencil-alt fs-6"></i>
                            </a>

                            <form action="{{ route('departments.destroy', $department->id) }}"
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
<div class="modal fade" id="editDepartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editDepartmentForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Department Name</label>
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

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addDepartmentModalLabel">
                    Add Department
                </h5>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>

            <form method="POST" action="{{ route('departments.store') }}">
                @csrf

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Department Name</label>

                        <div class="position-relative input-icon">
                            <input type="text"
                                class="form-control"
                                name="name"
                                placeholder="Enter Department Name"
                                required>

                            <span class="position-absolute top-50 translate-middle-y">
                                <i class="material-icons-outlined fs-5">business</i>
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
                        Add Department
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection