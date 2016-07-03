<?php

namespace CityNexus\CityNexus\Http;

use App\ApiKey;
use App\User;
use Carbon\Carbon;
use CityNexus\CityNexus\SendEmail;
use CityNexus\CityNexus\Task;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Salaback\Tabler\Table;

class TaskController extends Controller
{
    public function getShow($id)
    {
        return null;
    }
    public function postCreate(Request $request)
    {
        // Save Task
        $task = $request->all();
        if($task['assigned_to'] == null) unset($task['assigned_to']);
        $task = Task::create();
        $task->created_by = Auth::getUser()->id;
        $task->save();

        // Retrieve Related model
        $model = "\\CityNexus\\CityNexus\\" . $request->get('model');
        $model_id = $request->get('model_id');
        $related = $model::find($model_id);
        $relation = $request->get('relation');

        // Attach Model
        $related->$relation()->attach($task);

        // Send Email Notification
        $assignee = $request->get("assigned_to");

        if($request->get('assigned_to') != null)
        {
            $this->sendNotification($task, $assignee);
        }

        return redirect()->back();
    }

    public function getMarkComplete($id)
    {
        $task = Task::find($id);
        $task->completed_at = Carbon::now();
        $task->completed_by = Auth::getUser()->id;
        $task->save();

        return response();
    }

    private function sendNotification($task, $assignee)
    {
        $assignee = User::find($assignee);
        $message = view('citynexus::property._task_email', compact('task', 'assignee'));
        $subject = 'New Task: ' . $task->task;
        $email = $assignee->email;
        $this->dispatch(new SendEmail($email, $subject, $message));
        dd('failed');
        Session::flash('flash_success', "Email sent to task owner");
        dd('passed');
    }
}
