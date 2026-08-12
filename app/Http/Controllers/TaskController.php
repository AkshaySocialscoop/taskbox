<?php



namespace App\Http\Controllers;

 

use App\Models\User;

use App\Models\Notification;

use App\Models\UserInfo;

use App\Models\Task;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request; 



class TaskController extends Controller

{

    public function index()

    { 



        // Get all employees (exclude super admin)

        $users = User::whereIn('role', ['admin', 'user'])->select('id', 'name')->orderBy('name')->get();

        // tasks

        $tasks = Task::where('created_by', Auth::id())

                    ->orderBy('due_date', 'asc')

                    ->get();



        return view('tasks.index', compact('users', 'tasks'));

    }



    public function store(Request $request)

    {

        $request->validate([

            'title'       => 'required|string|max:255',

            'assigned_to' => 'required|exists:users,id',

            'priority'    => 'required|in:low,medium,high', 

            'due_date'    => 'nullable|date',

            'attachment'  => 'nullable|file|max:2048',

            'description' => 'nullable|string',

        ]);



        $filePath = null;



        if ($request->hasFile('attachment')) {

            $filePath = $request->file('attachment')

                                ->store('tasks', 'public');

        }



        Task::create([

            'title'       => $request->title,

            'description' => $request->description,

            'assigned_to' => $request->assigned_to,

            'created_by'  => auth()->id(),

            'priority'    => $request->priority,

            'status'      => 'not_started',

            'due_date'    => $request->due_date,

            'attachment'  => $filePath,

        ]);



        return redirect()->back()->with('success', 'Task assigned successfully!');

    }
    public function update(Request $request, $id)

    {

        $request->validate([

            'comment'       => 'required|string|max:255', 

        ]);
        Task::where('id', $id)->update([

            'comment' => $request->comment,

        ]);
        return redirect()->back()->with('success', 'Comment added successfully!');
    }

    // AJAX Update Task Status

   public function updateStatus(Request $request)

    {

       $task = Task::findOrFail($request->task_id);

        $task->status = $request->status;
        $task->comment = $request->comment;
        $task->save();

        return redirect()->back()->with('success', 'Task updated successfully.');
    }



 

}

