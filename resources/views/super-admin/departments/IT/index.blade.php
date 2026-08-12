@extends('layout.index')

@section('title', 'Dashboard')

@section('content')
   <div class="luxury-card">
        <div class="card-header-custom">
            <div class="card-title"><i class="fas fa-folder icon-soft"></i>Website's</div>
            <div class="text-muted-custom btn btn-outline-primary" data-bs-toggle="modal"
                data-bs-target="#projectModal"><i class="fas fa-plus ms-1"></i> Add Project</div>
        </div> 
        <div class="row g-2"> 
            @foreach($projects as $project)
            <div class="col-3">
                <div class="project-box">
                    <div class="p-icon pi-indigo">
                        {{ strtoupper(substr($project->brand_name,0,2)) }}
                    </div>
                    <div class="row w-100">
                        <div class="col-9 editProjectDetailBtn"  style="cursor: pointer;" data-id="{{ $project->id }}"
                        data-name="{{ $project->brand_name }}"
                        data-format="{{ $project->format }}"
                        data-link="{{ $project->link }}"
                        data-requirement="{{ $project->requirement }}"
                        data-comments="{{ $project->comments }}"
                        data-status="{{ $project->status }}">
                            <div class="fw-bold-custom text-nowrap">{{ $project->brand_name }}</div>
                            <div class="text-muted-custom" style="font-size:0.7rem">{{ $project->format }}</div>
                        </div>
                        <div class="col-3 text-end d-flex">
                            <a href="{{ $project->link }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm text-success">
                                <i class="fa-solid fa-up-right-from-square"></i>
                            </a>
                            <form action="{{ route('projects.destroy', $project->id) }}"
                                method="POST"
                                class="deleteProjectForm">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm text-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                            
                        </div>
                    </div>
                </div>
            </div> 
            @endforeach
        </div> 
    </div>
@endsection