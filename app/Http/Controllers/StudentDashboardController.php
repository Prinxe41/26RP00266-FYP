<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Campus; // Ensure the Campus model exists in the App\Models namespace
use App\Models\Hod; // Ensure the Hod model exists in the App\Models namespace
use App\Models\User; // Ensure the User model exists in the App\Models namespace

class StudentDashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware = ['role:student'];
    }

    public function index()
    {
        $user = Auth::user();
        $student = Session::get('student_data');

        // Get the student's submitted project (if any)
        $project = DB::table('projects')
            ->where('student_reg_number', $student->student_reg_number)
            ->first();

        // Join to get faculty name from cdo_id
        $faculty = DB::table('campus_department_option')
            ->join('faculties', 'campus_department_option.faculty_code', '=', 'faculties.faculty_code')
            ->where('campus_department_option.cdo_id', $student->cdo_id)
            ->select('faculties.faculty_name')
            ->first();


        $supervisor = DB::table('project_supervisor')
            ->join('projects', 'project_supervisor.project_code', '=', 'projects.project_code')
            ->join('supervisors', 'project_supervisor.supervisor_email', '=', 'supervisors.supervisor_email')
            ->where('projects.student_reg_number', $student->student_reg_number)
            ->select(
                'supervisors.supervisor_first_name',
                'supervisors.supervisor_last_name',
                'supervisors.supervisor_email',
                'supervisors.supervisor_phone_number'
            )
            ->get();

        return view('dashboards.student', [
            'user' => $user,
            'student' => $student,
            'project' => $project,
            'faculty' => $faculty,
            'supervisor' => $supervisor,
        ]);
    }


    public function submitProject(Request $request)
    {
        $request->validate([
            'project_name' => 'required',
            'project_problems' => 'required',
            'project_solutions' => 'required',
            'project_abstract' => 'required',
            'project_dissertation' => 'required|file|mimes:pdf,docx',
            'project_source_codes' => 'required|file|mimes:zip,rar',
        ]);

        // Save uploaded files
        $dissertationPath = $request->file('project_dissertation')->store('dissertations', 'public');
        $sourceCodePath = $request->file('project_source_codes')->store('source_codes', 'public');

        // Insert into DB
        DB::table('projects')->insert([
            'project_name' => $request->project_name,
            'project_problems' => $request->project_problems,
            'project_solutions' => $request->project_solutions,
            'project_abstract' => $request->project_abstract,
            'project_dissertation' => $dissertationPath,
            'project_source_codes' => $sourceCodePath,
            'student_reg_number' => session('student_data')->student_reg_number,
            'submission_date' => now(),
        ]);

        return back()->with('success', 'Project submitted successfully!');
    }
    public function deleteProject(Request $request)
    {
        $request->validate([
            'project_code' => 'required|exists:projects,project_code'
        ]);

        $project = DB::table('projects')
            ->where('project_code', $request->project_code)
            ->where('approved', null) // prevent delete if approved
            ->first();

        if (!$project) {
            return back()->withErrors(['error' => 'Project not found or already reviewed.']);
        }

        DB::table('projects')->where('project_code', $request->project_code)->delete();

        return back()->with('success', 'Your project was deleted successfully.');
    }
    public function updateProject(Request $request)
    {
        $request->validate([
            'project_code' => 'required|exists:projects,project_code',
            'project_name' => 'required|string|max:255',
            'project_problems' => 'required|string',
            'project_solutions' => 'required|string',
            'project_abstract' => 'required|string',
            'project_dissertation' => 'nullable|file|mimes:pdf|max:20480',
            'project_source_codes' => 'nullable|file|mimes:zip|max:20480',
        ]);

        $project = DB::table('projects')
            ->where('project_code', $request->project_code)
            ->whereNull('approved') // Only allow update if not yet approved
            ->first();

        if (!$project) {
            return back()->withErrors(['error' => 'Project not found or already approved.']);
        }

        $data = [
            'project_name' => $request->project_name,
            'project_problems' => $request->project_problems,
            'project_solutions' => $request->project_solutions,
            'project_abstract' => $request->project_abstract,
            'updated_at' => now(),
        ];

        // If dissertation file is uploaded
        if ($request->hasFile('project_dissertation')) {
            $dissertationPath = $request->file('project_dissertation')->store('projects', 'public');
            $data['project_dissertation'] = $dissertationPath;
        }

        // If source code file is uploaded
        if ($request->hasFile('project_source_codes')) {
            $sourceCodePath = $request->file('project_source_codes')->store('projects', 'public');
            $data['project_source_codes'] = $sourceCodePath;
        }

        DB::table('projects')->where('project_code', $request->project_code)->update($data);

        return back()->with('success', 'Project updated successfully.');
    }

}