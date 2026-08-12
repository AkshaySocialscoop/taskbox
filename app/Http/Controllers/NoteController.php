<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notes = Note::latest()->get();
        return view('sticky_notes.index', compact('notes'));
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
        $request->validate([
            'content' => 'required',
            'color' => 'required'
        ]);

        Note::create($request->all());
        return redirect()->back()->with('success', 'Notes Added successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $note = Note::findOrFail($id);

    $note->update([
        'content' => $request->content,
        'color' => $request->color,
    ]);

    return redirect()->route('sticky-notes.index');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    Note::findOrFail($id)->delete();
    return redirect()->route('sticky-notes.index');
}

}
