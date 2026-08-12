@extends('layout.index')

@section('title', 'Dashboard')

@section('content')
<h1>Profile</h1>
<div class="row">
    <div class="col-12 col-md-4 d-flex">
        <div class="card w-100">
            <div class="card-body">
                <div class="position-relative">
                    <img src="{{ asset('assets/images/gallery/18.png') }}" class="img-fluid rounded" alt="">
                    <div class="position-absolute top-100 start-50 translate-middle">
                        <img src="{{ $users->userinfo?->profile_photo 
                  ? asset('storage/' . $users->userinfo->profile_photo) 
                  : asset('assets/images/avatars/01.png') }}" width="100" height="100"
                            class="rounded-circle raised p-1 bg-white" alt="">
                    </div>
                </div>
                <div class="text-center mt-5 pt-4">
                    <h4 class="mb-1">{{ auth()->user()->name }}</h4>
                    <p class="mb-0">{{ $users->department?->name ?? 'SuperAdmin' }}</p>
                </div>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <b>Email</b>
                        <br>
                        {{ $users->email }}
                    </li>
                    <li class="list-group-item">
                        <b>Phone</b>
                        <br>
                        Mobile : {{ $users->userinfo->phone_number ?? '+91-XXXXXXX' }}
                    </li>
                    <li class="list-group-item border-top">
                        <b>Password</b>
                        <br>
                        ************
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-8 d-flex">
        <div class="card w-100">
            <div class="row p-4">
                <div class="col-md-6">
                    <h5 class="mb-4">User Details</h5>
                    <form class="row g-3" method="POST" action="{{ route('user.info') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-12 ">
                            <label for="input15" class="form-label">Phone</label>
                            <div class="position-relative input-icon">
                                <input type="number" class="form-control" name="phone" id="input15" placeholder="Phone"
                                    value="{{ old('phone', $users->userinfo?->phone_number) }}">
                                <span class="position-absolute top-50 translate-middle-y"><i
                                        class="material-icons-outlined fs-5">call</i></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="input17" class="form-label">Profie Photo</label>
                            <div class="input-group mb-3">

                                <div class="input-group">
                                    <label class="input-group-text">Upload</label>
                                    <input type="file" class="form-control" name="photo">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="d-md-flex d-grid align-items-center gap-3">
                                <button type="submit" class="btn btn-primary px-4">
                                    {{ $users->userinfo ? 'Update Profile' : 'Save Profile' }}</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <h5 class="mb-4">Change Password</h5>
                    <form class="row g-3" method="POST" action="{{ route('password.change') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-12 ">
                            <label for="input15" class="form-label">Old Password</label>
                            <div class="position-relative input-icon">
                                <input type="password" class="form-control" name="old_password" id="input15" placeholder="Old Password">
                                <span class="position-absolute top-50 translate-middle-y"><i
                                        class="material-icons-outlined fs-5">lock</i></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="input17" class="form-label">New Password</label>
                            <div class="position-relative input-icon">
                                <input type="password" class="form-control" name="new_password" id="input17" placeholder="New Password">
                                <span class="position-absolute top-50 translate-middle-y"><i
                                        class="material-icons-outlined fs-5">lock</i></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                          <label class="form-label">Confirm Password</label>
                          <input type="password"
                                class="form-control"
                                name="new_password_confirmation"
                                placeholder="Confirm Password">
                      </div>
                        <div class="col-md-12">
                            <div class="d-md-flex d-grid align-items-center gap-3">
                                <button type="submit" class="btn btn-primary px-4">
                                    Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection