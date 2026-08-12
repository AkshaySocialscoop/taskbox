<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validate input
        $request->validate([
            'brand_name'  => 'required|string|max:255',
            'format'      => 'nullable|string|max:100',
            'link'        => 'nullable|url',
            'requirement' => 'nullable|string',
            'comments'    => 'nullable|string', 
        ]);

        // 2. Save data
        Project::create([
            'brand_name'  => $request->brand_name,
            'format'      => $request->format,
            'link'        => $request->link,
            'requirement' => $request->requirement,
            'comments'    => $request->comments,   
            'status'      => 'pending',
            'user_id'  => Auth::id(), // optional but best practice
        ]);

        // 3. Redirect with success message
        return redirect()->back()->with('success', 'Project Added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */ 
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'brand_name'  => 'required|string|max:255',
            'format'      => 'nullable|string|max:255',
            'link'        => 'nullable|url',
            'requirement' => 'nullable|string',
            'comments'    => 'nullable|string',
            'status'      => 'nullable|string',
        ]);

        $project->update([
            'brand_name'  => $request->brand_name,
            'format'      => $request->format,
            'link'        => $request->link,
            'requirement' => $request->requirement,
            'comments'    => $request->comments,
            'status'      => $request->status,
        ]);

        return redirect()->back()->with('success', 'Project updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        // Optional: Only IT department can delete
        if (auth()->user()->department_id !== 0) {
            abort(403);
        }

        $project->delete();

        return redirect()->back()->with('success', 'Project deleted successfully!');
    }

}
