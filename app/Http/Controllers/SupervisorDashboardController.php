<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Campus; // Ensure the Campus model exists in the App\Models namespace
use App\Models\Hod; // Ensure the Hod model exists in the App\Models namespace
use App\Models\User; // Ensure the User model exists in the App\Models namespace
use App\Models\Supervisor;
use Illuminate\Support\Facades\Session;
use App\Models\Department; // Ensure the Department model exists in the App\Models namespace
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Project; // Ensure the Project model exists in the App\Models namespace
use App\Models\Message; // Ensure the Message model exists in the App\Models namespace
use App\Models\ProjectSupervisor; // Ensure the ProjectSupervisor model exists in the App\Models namespace
use App\Models\Faculty; // Ensure the Faculty model exists in the App\Models namespace
use App\Models\CampusDepartmentOption; // Ensure the CampusDepartmentOption model exists in the App\Models namespace


class SupervisorDashboardController extends Controller
{
    use AuthorizesRequests, DispatchesJobs;
    public function __construct()
    {
        $this->middleware = ['role:supervisor'];
    }

    public function index()
    {
        // Get supervisor data from session
        $supervisor = Session::get('supervisor_data');
        $supervisor = Session::get('supervisor_data'); // or use Auth if you're logging them in

        $students = DB::table('project_supervisor')
            ->join('projects', 'project_supervisor.project_code', '=', 'projects.project_code')
            ->join('students', 'projects.student_reg_number', '=', 'students.student_reg_number')
            ->where('project_supervisor.supervisor_email', $supervisor->supervisor_email)
            ->select(
                'students.student_reg_number',
                'students.student_first_name',
                'students.student_last_name',
                'students.student_email',
                'projects.project_code',
                'projects.project_name',
                'projects.project_problems',
                'projects.project_solutions',
                'projects.project_abstract',
                'projects.project_dissertation',
                'projects.approved',
                'projects.project_source_codes'
            )
            ->get();



        return view('dashboards.supervisor', [
            'students' => $students,
            'supervisor' => $supervisor
        ]);

    }
    public function viewProject($project_code)
    {
        $project = DB::table('projects')
            ->join('students', 'projects.student_reg_number', '=', 'students.student_reg_number')
            ->where('projects.project_code', $project_code)
            ->select('projects.*', 'students.student_first_name', 'students.student_last_name', 'students.student_email')
            ->first();

        if (!$project) {
            return back()->withErrors(['error' => 'Project not found.']);
        }

        return view('supervisor.view-project', compact('project'));
    }

}