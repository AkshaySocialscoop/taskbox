@extends('layout.index')

@section('title', 'Dashboard')

@section('content')




<div class="mt-2 card py-3 px-3" id="filterSection" style="display:none;">

    <form method="GET" action="{{ route('shifts.index') }}">

        <div class="row g-3 align-items-end">

            <div class="col-md-4">
                <label class="form-label">Shift Name</label>
                <input type="text"
                    class="form-control"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search Shift">
            </div>

            <div class="col-md-3">
                <label class="form-label">Check In</label>
                <input type="time"
                    class="form-control"
                    name="start_time"
                    value="{{ request('start_time') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Check Out</label>
                <input type="time"
                    class="form-control"
                    name="end_time"
                    value="{{ request('end_time') }}">
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary">
                    Search
                </button>

                <button type="button"
                    class="btn btn-secondary "
                    onclick="window.location.href='{{ route('shifts.index') }}'">
                    Reset
                </button>
            </div>

        </div>

    </form>

</div>





<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h5 class="mb-0">Shift List</h5>
            <div class="d-flex gap-2">

                <x-filter-button target="filterSection" />

                <button type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#addShiftModal">
                    Add Shift
                </button>

            </div>

        </div>

        <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Shift Name</th>
                        <th>Check IN</th>
                        <th>Check OUT</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($shifts as $shift)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $shift->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                        <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>
                        <td>
                            <a href="javascript:;" class="btn btn-sm text-warning editShiftBtn"
                                data-id="{{ $shift->id }}" data-name="{{ $shift->name }}"
                                data-start-time="{{ $shift->start_time }}" data-end-time="{{ $shift->end_time }}">
                                <i class="lni lni-pencil-alt fs-6"></i>
                            </a>

                            <form action="{{ route('shifts.destroy', $shift->id) }}" method="POST"
                                class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm text-danger"> <i
                                        class="lni lni-trash fs-6 "></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No shifts found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editShiftModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editShiftForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Shift Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Check IN</label>
                        <input type="time" name="start_time" id="edit_start_time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Check OUT</label>
                        <input type="time" name="end_time" id="edit_end_time" class="form-control" required>
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


<div class="modal fade" id="addShiftModal" tabindex="-1" aria-labelledby="addShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addShiftModalLabel">
                    Add Shift
                </h5>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('shifts.store') }}">
                @csrf

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Shift Name</label>

                            <div class="position-relative input-icon">
                                <input type="text"
                                    class="form-control"
                                    name="name"
                                    placeholder="Shift Name"
                                    required>

                                <span class="position-absolute top-50 translate-middle-y">
                                    <i class="material-icons-outlined fs-5">badge</i>
                                </span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Check In</label>

                            <div class="position-relative input-icon">
                                <input type="time"
                                    class="form-control"
                                    name="start_time"
                                    required>

                                <span class="position-absolute top-50 translate-middle-y">
                                    <i class="material-icons-outlined fs-5">access_time</i>
                                </span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Check Out</label>

                            <div class="position-relative input-icon">
                                <input type="time"
                                    class="form-control"
                                    name="end_time"
                                    required>

                                <span class="position-absolute top-50 translate-middle-y">
                                    <i class="material-icons-outlined fs-5">schedule</i>
                                </span>
                            </div>
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
                        Add Shift
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection