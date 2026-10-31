<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Supervisor;
use App\Models\Hod;
use App\Models\User;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Support\Facades\DB;
use App\Models\CampusDepartmentOption;
use App\Models\Student;
use App\Models\Project;

use Illuminate\Support\Facades\Session;

use Illuminate\Http\Request;

class HODDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware = ['role:hod'];
    }

    public function index()
    {
        // Get HOD data from session
        $hod = Session::get('hod_data');

        // Step 1: Get all cdo_ids for this HOD's department
        $cdoIds = DB::table('campus_department_option')
            ->join('faculties', 'campus_department_option.faculty_code', '=', 'faculties.faculty_code')
            ->where('faculties.department_code', $hod->department_code)
            ->pluck('campus_department_option.cdo_id');

        // Step 2: Get all students whose cdo_id matches this department
        $students = DB::table('students')
            ->join('campus_department_option', 'students.cdo_id', '=', 'campus_department_option.cdo_id')
            ->join('campus_department', 'campus_department_option.campus_department_id', '=', 'campus_department.campus_department_id')
            ->join('faculties', 'campus_department_option.faculty_code', '=', 'faculties.faculty_code')
            ->join('campuses', 'campus_department.campus_id', '=', 'campuses.campus_id')
            ->where('campus_department.department_code', $hod->department_code)
            ->where('campus_department.campus_id', $hod->campus_id)
            ->select(
                'students.*',
                'faculties.faculty_name',
                'campuses.campus_name'
            )
            ->get();

        $supervisors = DB::table('supervisors')
            ->where('department_code', $hod->department_code)
            ->where('campus_id', $hod->campus_id)
            ->get();
        // $supervisors = DB::table('supervisors')->get(); // You can later filter by department
        // Step 3: Get all supervisors for this HOD's department
        $assignedSupervisors = DB::table('project_supervisor')
            ->join('projects', 'project_supervisor.project_code', '=', 'projects.project_code')
            ->join('supervisors', 'project_supervisor.supervisor_email', '=', 'supervisors.supervisor_email')
            ->select(
                'projects.student_reg_number',
                'projects.project_code',
                'supervisors.supervisor_first_name',
                'supervisors.supervisor_last_name',
                'supervisors.supervisor_email'
            )
            ->get()
            ->groupBy('student_reg_number'); // group by student for easy display
        // Get all projects for this HOD's department
        $projects = DB::table('projects')
            ->join('students', 'projects.student_reg_number', '=', 'students.student_reg_number')
            ->join('campus_department_option', 'students.cdo_id', '=', 'campus_department_option.cdo_id')
            ->join('faculties', 'campus_department_option.faculty_code', '=', 'faculties.faculty_code')
            ->leftJoin('project_supervisor', 'projects.project_code', '=', 'project_supervisor.project_code')
            ->leftJoin('supervisors', 'project_supervisor.supervisor_email', '=', 'supervisors.supervisor_email')
            ->where('faculties.department_code', $hod->department_code)
            ->select(
                'projects.project_code',
                'projects.project_name',
                'projects.project_abstract',
                'projects.project_dissertation',
                'projects.project_source_codes',
                'projects.student_reg_number',
                'projects.approved',
                'projects.comment',
                'projects.submission_date',
                DB::raw('GROUP_CONCAT(CONCAT(supervisors.supervisor_first_name, " ", supervisors.supervisor_last_name)) as supervisor_names'),
                DB::raw('GROUP_CONCAT(supervisors.supervisor_email) as supervisor_emails')
            )
            ->groupBy(
                'projects.project_code',
                'projects.project_name',
                'projects.project_abstract',
                'projects.project_dissertation',
                'projects.project_source_codes',
                'projects.student_reg_number',
                'projects.approved',
                'projects.comment',
                'projects.submission_date'
            )
            ->get();


        // get campus name
        $campus = DB::table('campuses')
            ->where('campus_id', $hod->campus_id)
            ->first();
        // Get the department name
        $department = DB::table('departments')
            ->where('department_code', $hod->department_code)
            ->first();
        // Get the faculty name
        $faculty = DB::table('faculties')
            ->where('faculty_code', $hod->faculty_code)
            ->first();


        return view('dashboards.hod', [
            'hod' => $hod,
            'students' => $students, // Pass students to the view
            'supervisors' => $supervisors,

            'assignedSupervisors' => $assignedSupervisors,
            'projects' => $projects, // Pass projects to
            'department' => $department, // Pass department name to the view
            'faculty' => $faculty, // Pass faculty name to the view
            'campus' => $campus, // Pass campus name to the view
            // Pass assigned supervisors to the view
        ]);
    }

    public function storeSupervisor(Request $request)
    {
        $request->validate([
            'supervisor_email' => 'required|email|unique:supervisors,supervisor_email',
            'supervisor_first_name' => 'required',
            'supervisor_last_name' => 'required',
            'supervisor_phone_number' => 'required',
            'supervisor_password' => 'required|min:6'
        ]);
        $hod = Session::get('hod_data');
        DB::table('supervisors')->insert([
            'supervisor_email' => $request->supervisor_email,
            'supervisor_first_name' => $request->supervisor_first_name,
            'supervisor_last_name' => $request->supervisor_last_name,
            'supervisor_phone_number' => $request->supervisor_phone_number,
            'supervisor_password' => Hash::make($request->supervisor_password),
            'department_code' => $hod->department_code, // ✅ set department
            'campus_id' => $hod->campus_id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Supervisor added successfully!');
    }
    public function updateSupervisor(Request $request, $email)
    {
        $request->validate([
            'supervisor_email' => 'required|email',
            'supervisor_first_name' => 'required',
            'supervisor_last_name' => 'required',
            'supervisor_phone_number' => 'required',
        ]);

        DB::table('supervisors')->where('supervisor_email', $email)->update([
            'supervisor_email' => $request->supervisor_email,
            'supervisor_first_name' => $request->supervisor_first_name,
            'supervisor_last_name' => $request->supervisor_last_name,
            'supervisor_phone_number' => $request->supervisor_phone_number,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Supervisor updated successfully!');
    }
    public function deleteSupervisor($email)
    {
        DB::table('supervisors')->where('supervisor_email', $email)->delete();

        return redirect()->back()->with('success', 'Supervisor deleted successfully!');
    }
    public function assignSupervisor(Request $request)
    {
        $request->validate([
            'student_reg_number' => 'required|exists:students,student_reg_number',
            'supervisor_email' => 'required|exists:supervisors,supervisor_email',
        ]);

        // Get the student's project
        $project = DB::table('projects')
            ->where('student_reg_number', $request->student_reg_number)
            ->first();

        if (!$project) {
            return back()->withErrors(['error' => 'Student has not submitted a project.']);
        }
        // Check if already assigned
        $existing = DB::table('project_supervisor')
            ->where('project_code', $project->project_code)
            ->where('supervisor_email', $request->supervisor_email)
            ->exists();

        if ($existing) {
            return back()->withErrors(['error' => 'This supervisor is already assigned to the project.']);
        }


        // Assign the supervisor
        DB::table('project_supervisor')->insert([
            'project_code' => $project->project_code,
            'supervisor_email' => $request->supervisor_email,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Supervisor assigned successfully!');
    }
    public function unassignSupervisor(Request $request)
    {
        DB::table('project_supervisor')
            ->where('project_code', $request->project_code)
            ->where('supervisor_email', $request->supervisor_email)
            ->delete();

        return back()->with('success', 'Supervisor unassigned successfully.');
    }
    public function updateProjectStatus(Request $request)
    {
        $request->validate([
            'project_code' => 'required|exists:projects,project_code',
            'status' => 'required|in:0,1',
            'comment' => 'nullable|string|max:1000'
        ]);

        DB::table('projects')
            ->where('project_code', $request->project_code)
            ->update([
                'approved' => $request->status,
                'comment' => $request->comment,
                'updated_at' => now()
            ]);

        return back()->with('success', 'Project status updated with comment.');
    }




}