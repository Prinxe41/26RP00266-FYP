<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Project;
use App\Models\ProjectSupervisor;
use Illuminate\Support\Facades\Session;

class ChatController extends Controller
{
    public function index($project_code, $receiver_email = null)
    {
        // Get the current user role from session
        $user_role = Session::get('user_role');

        // Get the project
        $project = Project::where('project_code', $project_code)->first();

        if (!$project) {
            return back()->with('error', 'Project not found.');
        }

        // Get the student
        $student = Student::where('student_reg_number', $project->student_reg_number)->first();

        // Get all supervisors for this project
        $supervisors = Supervisor::join('project_supervisor', 'supervisors.supervisor_email', '=', 'project_supervisor.supervisor_email')
            ->where('project_supervisor.project_code', $project_code)
            ->get();

        if ($supervisors->isEmpty() && $user_role == 'student') {
            return back()->with('error', 'No supervisor assigned to this project yet.');
        }

        // Determine current user's email based on role
        $current_user_email = '';
        if ($user_role == 'student') {
            $current_user_email = $student->student_email;
        } elseif ($user_role == 'supervisor') {
            $current_user_email = Session::get('supervisor_data')->supervisor_email;
        }

        // Determine which supervisor to chat with (for students)
        $active_receiver = null;
        if ($user_role == 'student') {
            if ($receiver_email) {
                // Check if the receiver is actually a supervisor for this project
                $active_receiver = $supervisors->where('supervisor_email', $receiver_email)->first();
            }

            // If no valid receiver specified, default to the first supervisor
            if (!$active_receiver && $supervisors->isNotEmpty()) {
                $active_receiver = $supervisors->first();
            }
        } else {
            // For supervisors, the receiver is always the student
            $active_receiver = $student;
        }

        // Get messages for this project between the current user and active receiver
        $messages = [];
        if ($active_receiver) {
            $messages = Message::where('project_code', $project_code)
                ->where(function ($query) use ($current_user_email, $active_receiver, $user_role) {
                    $receiver_email = $user_role == 'student' ? $active_receiver->supervisor_email : $active_receiver->student_email;

                    $query->where(function ($q) use ($current_user_email, $receiver_email) {
                        $q->where('sender_email', $current_user_email)
                            ->where('receiver_email', $receiver_email);
                    })->orWhere(function ($q) use ($current_user_email, $receiver_email) {
                        $q->where('sender_email', $receiver_email)
                            ->where('receiver_email', $current_user_email);
                    });
                })
                ->orderBy('timestamp', 'asc')
                ->get();
        }

        return view('dashboards.chat', compact(
            'project',
            'student',
            'supervisors',
            'messages',
            'current_user_email',
            'user_role',
            'active_receiver'
        ));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message_body' => 'required',
            'project_code' => 'required',
            'receiver_email' => 'required|email'
        ]);

        $user_role = Session::get('user_role');
        $sender_email = '';

        if ($user_role == 'student') {
            $sender_email = Session::get('student_data')->student_email;
        } elseif ($user_role == 'supervisor') {
            $sender_email = Session::get('supervisor_data')->supervisor_email;
        }

        $message = new Message();
        $message->sender_email = $sender_email;
        $message->receiver_email = $request->receiver_email;
        $message->project_code = $request->project_code;
        $message->message_body = $request->message_body;
        $message->timestamp = now();
        $message->save();

        // For AJAX requests, return JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()->back();
    }

    public function getMessages(Request $request)
    {
        $request->validate([
            'project_code' => 'required',
            'receiver_email' => 'required|email',
            'last_message_id' => 'nullable|integer'
        ]);

        $user_role = Session::get('user_role');
        $current_user_email = $user_role == 'student'
            ? Session::get('student_data')->student_email
            : Session::get('supervisor_data')->supervisor_email;

        // Get new messages
        $query = Message::where('project_code', $request->project_code)
            ->where(function ($q) use ($current_user_email, $request) {
                $q->where(function ($subq) use ($current_user_email, $request) {
                    $subq->where('sender_email', $current_user_email)
                        ->where('receiver_email', $request->receiver_email);
                })->orWhere(function ($subq) use ($current_user_email, $request) {
                    $subq->where('sender_email', $request->receiver_email)
                        ->where('receiver_email', $current_user_email);
                });
            });

        // Only get messages newer than the last seen message
        if ($request->has('last_message_id') && $request->last_message_id) {
            $query->where('message_id', '>', $request->last_message_id);
        }

        $messages = $query->orderBy('timestamp', 'asc')->get();

        return response()->json([
            'messages' => $messages,
            'current_user_email' => $current_user_email
        ]);
    }
}