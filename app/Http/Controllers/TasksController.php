<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [];
        
        if (Auth::check()) {
            
            $user = Auth::user();
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);
            
            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
        }
        return view('dashboard', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $task = new Task;
        
        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required',
            'status' => 'required|max:10'
        ]);
    
        $request->user()->tasks()->create([
            'content' => $request->content,
            'status' => $request->status,
        ]);
        
        return redirect('/');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
        $task = Task::findOrFail($id);
        
        if (Auth::id() === $task->user_id) {
            return view('tasks.show', [
                'task' => $task,
            ]);
        }

        return redirect('/');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $task = Task::findOrFail($id);
        
        if (Auth::id() === $task->user_id) {
            return view('tasks.edit', [
                'task' => $task,
            ]);
        }
        
        return redirect('/');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'content' => 'required',
            'status' => 'required|max:10'
        ]);
    
        $task = Task::findOrFail($id);
        
        if (Auth::id() === $task->user_id) {
            $task->content = $request->content;
            $task->status = $request->status;
            $task->save();
        
            return redirect('/');
        }
        
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        
        if (Auth::id() === $task->user_id) {
            $task->delete();
            return redirect('/')
                ->with('success','Delete Successful');;
        }
        
        return back()
            ->with('Delete Failed');
    }
}
