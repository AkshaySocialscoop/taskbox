<?php

namespace App\Http\Controllers;
use App\Models\Project;
use App\Models\CalendarEvent;
use App\Models\Task;
use Carbon\Carbon;
use App\Models\SocialAccount;

use Illuminate\Http\Request; 

class SuperAdminController extends Controller
{
    public function index()
{
    try {

        $today = Carbon::today();

        $projects = Project::whereIn('status', ['pending', 'in_progress'])
            ->latest()
            ->get();

        $posts = CalendarEvent::where('status', 'pending')
            ->latest()
            ->get();

        $uploaded_posts = CalendarEvent::where('status', 'completed')->count();

        $completedtasks = Task::where('status', 'completed')->count();

        $totaltasks = Task::count();

        $inprogress = Task::where('status', 'in_progress')->count();

        $overdue = Task::where('due_date', '<', $today)
            ->where('status', '!=', 'completed')
            ->count();

        $websitecompleted = Project::where('status', 'completed')->count();

        return view('super-admin.dashboard', compact(
            'projects',
            'posts',
            'completedtasks',
            'totaltasks',
            'inprogress',
            'overdue',
            'websitecompleted',
            'uploaded_posts'
        ));

    } catch (\Exception $e) {

        dd($e->getMessage());
    }
}

    public function IT()
    {   
        $projects = Project::latest()->get();
        return view('super-admin.departments.IT.index', compact('projects'));
    }

    public function social()
    {   
        $accounts = SocialAccount::all();
        return view('super-admin.departments.social-media.index', compact('accounts'));
    }
     
}