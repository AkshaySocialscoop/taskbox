@extends('layout.index')

@section('title', 'Dashboard')

@section('content')
    <div class="luxury-card">
        <div class="card-header-custom">
            <div class="card-title"><i class="fas fa-folder icon-soft"></i>Social Media Accounts</div> 
            <div class="">
                <a href="/auth/facebook" target="_blank" rel="noopener noreferrer" class="btn btn-sm text-success">Log in
                </a>
            </div> 
        </div> 
        <div class="row g-2">  
        @forelse($accounts as $account)
            <div class="col-3">
                <div class="project-box">
                    <div class="p-icon pi-indigo"> 
                    </div>
                    <div class="row w-100">
                        <div class="col-9"  style="cursor: pointer;"  >
                            <div class="fw-bold-custom text-nowrap">{{ $account->instagram_username }}</div>
                            <div class="text-muted-custom" style="font-size:0.7rem"> </div>
                        </div>
                        <div class="col-3 text-end d-flex">
                            <a href="/instagram/account/{{ $account->id }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm text-success">
                                <i class="fa-solid fa-up-right-from-square"></i>
                            </a> 
                        </div>
                    </div>
                </div>
            </div> 
            @empty
                <tr>
                    <td colspan="5" class="text-center">No Accounts found</td>
                </tr>
            @endforelse
        </div> 
    </div>
@endsection
