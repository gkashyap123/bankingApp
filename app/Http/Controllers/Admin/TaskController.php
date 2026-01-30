<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;   // 👈 IMPORTANT
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskActivity;
use App\Http\Requests\StoreTaskRequest;
use Illuminate\Http\Request;
use App\Services\WhatsAppService;
use Twilio\Rest\Client;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index(){
    $tasks = auth()->user()->role === 'admin' ? Task::with('assignee')->get() : Task::where('assigned_to', auth()->id())->get();
    return view('tasks.index', compact('tasks'));
  }

  public function store(StoreTaskRequest $req){
    $data = $req->validated();
    $data['created_by'] = auth()->id();
    $task = Task::create($data);

    // dispatch notification to assigned user
    if (!empty($data['assigned_to'])) {
        $user = User::find($data['assigned_to']);
        if ($user) {
            try {
                $user->notify(new TaskAssigned($task));
                $user->notify(new TaskActivity($task, 'assigned'));
            } catch (\Throwable $e) {
                \Log::error('Failed sending task assigned notification: '.$e->getMessage());
            }
        }
    }

    return redirect()->route('tasks.index')->with('success','Task created');
  }

//   public function update(Request $req, Task $task){
//     $task->update($req->only(['status']));
//     return back();
//   }


    public function create(WhatsAppService $whatsapp)
    {
        $users = User::all();
        if($users){
            $whatsapp->send('+917888706164', 'Task: New Task Creation Initiated');
        }
        return view('tasks.create', compact('users'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'title' => 'required',
    //         'assigned_to' => 'required|exists:users,id'
    //     ]);

    //     Task::create([
    //         'title' => $request->title,
    //         'description' => $request->description,
    //         'assigned_to' => $request->assigned_to,
    //         'created_by' => auth()->id(),
    //         'due_date' => $request->due_date,
    //     ]);

    //     return redirect()->route('tasks.index')->with('success', 'Task created successfully');
    // }

    public function edit(Task $task)
    {
        $users = User::all();
        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $oldAssigned = $task->assigned_to;
        $oldStatus = $task->status;

        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $task->update($data);

        // If assigned_to changed, notify previous and new assignees
        if (isset($data['assigned_to']) && $data['assigned_to'] != $oldAssigned) {
            try {
                if ($oldAssigned) {
                    $oldUser = User::find($oldAssigned);
                    if ($oldUser) {
                        $oldUser->notify(new TaskActivity($task, 'unassigned', ['to' => $data['assigned_to']]));
                    }
                }

                if ($data['assigned_to']) {
                    $newUser = User::find($data['assigned_to']);
                    if ($newUser) {
                        $newUser->notify(new TaskAssigned($task));
                        $newUser->notify(new TaskActivity($task, 'reassigned', ['from' => $oldAssigned]));
                    }
                }
            } catch (\Throwable $e) {
                \Log::error('Failed sending reassignment notifications: '.$e->getMessage());
            }
        }

        // If status changed, notify current assignee
        if (isset($data['status']) && $data['status'] != $oldStatus) {
            try {
                $assignee = User::find($task->assigned_to);
                if ($assignee) {
                    $assignee->notify(new TaskActivity($task, 'status_changed', ['old_status' => $oldStatus, 'new_status' => $data['status']]));
                }
            } catch (\Throwable $e) {
                \Log::error('Failed sending status change notification: '.$e->getMessage());
            }
        } else {
            // general update notification
            try {
                $assignee = User::find($task->assigned_to);
                if ($assignee) {
                    $assignee->notify(new TaskActivity($task, 'updated'));
                }
            } catch (\Throwable $e) {
                \Log::error('Failed sending update notification: '.$e->getMessage());
            }
        }

        return redirect()->route('tasks.index')->with('success', 'Task updated');
    }

    public function show(Task $task)
    {
        $task->load('assignee');
        return view('tasks.show', compact('task'));
    }

    public function destroy(Task $task)
    {
        try {
            $assignee = User::find($task->assigned_to);
            if ($assignee) {
                $assignee->notify(new TaskActivity($task, 'deleted'));
            }
        } catch (\Throwable $e) {
            \Log::error('Failed sending delete notification: '.$e->getMessage());
        }

        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted');
    }

    public function testWhatsApp(WhatsAppService $whatsapp)
    {
        $whatsapp->send('+917888706164', 'Hello from Fund Manager App');
        return 'Sent';
    }


}

?>