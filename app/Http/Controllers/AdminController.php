<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Hod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminController extends Controller
{
    public function __construct()
    {
        // Apply admin middleware to all methods in this controller
        $this->middleware = ['admin'];
    }
    public function manageCampus()
    {
        $campuses = Campus::all();
        return view('admin.campus', compact('campuses'));
    }
    public function manageDepartment()
    {
        $campuses = Campus::all();
        $departments = Department::all();
        return view('admin.department', compact('campuses', 'departments'));
    }

    public function manageFaculty()
    {
        $faculties = Faculty::all();

        // Get all campus-department combinations
        $campusDepartments = DB::table('campus_department')
            ->join('campuses', 'campus_department.campus_id', '=', 'campuses.campus_id')
            ->join('departments', 'campus_department.department_code', '=', 'departments.department_code')
            ->select(
                'campus_department.campus_department_id',
                'campus_department.department_code', // ✅ Make sure this line is included!
                'campuses.campus_name',
                'departments.department_name'
            )
            ->orderBy('departments.department_name', 'asc')
            ->get();



        // Get assigned faculty options
        $assignedFaculties = DB::table('campus_department_option')
            ->join('campus_department', 'campus_department_option.campus_department_id', '=', 'campus_department.campus_department_id')
            ->join('departments', 'campus_department.department_code', '=', 'departments.department_code')
            ->join('campuses', 'campus_department.campus_id', '=', 'campuses.campus_id')
            ->join('faculties', 'campus_department_option.faculty_code', '=', 'faculties.faculty_code')
            ->select(
                'departments.department_name',
                'campuses.campus_name',
                'faculties.faculty_name'
            )
            ->orderBy('departments.department_name', 'asc')

            ->get();

        return view('admin.faculty', compact('faculties', 'campusDepartments', 'assignedFaculties'));
    }
    public function storeCampusFaculty(Request $request)
    {
        $request->validate([
            'campus_department_id' => 'required|exists:campus_department,campus_department_id',
            'faculty_code' => 'required|exists:faculties,faculty_code',
        ]);

        $exists = DB::table('campus_department_option')->where([
            'campus_department_id' => $request->campus_department_id,
            'faculty_code' => $request->faculty_code
        ])->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['This faculty is already assigned to this department.']);
        }

        DB::table('campus_department_option')->insert([
            'campus_department_id' => $request->campus_department_id,
            'faculty_code' => $request->faculty_code,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.manage-faculty')->with('success', 'Faculty added successfully');
    }
    public function getFacultiesByDepartment($department_code)
    {
        $faculties = Faculty::where('department_code', $department_code)
            ->get(['faculty_code', 'faculty_name']);

        return response()->json($faculties);
    }


    public function manageHOD()
    {
        $campuses = Campus::all();
        $departments = Department::all();

        $hods = DB::table('hods')
            ->join('campuses', 'hods.campus_id', '=', 'campuses.campus_id')
            ->join('departments', 'hods.department_code', '=', 'departments.department_code')
            ->select('hods.*', 'campuses.campus_name', 'departments.department_name')
            ->get();

        return view('admin.hod', compact('campuses', 'departments', 'hods'));
    }

    public function storeCampus(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'campus_name' => 'required|string|max:255|unique:campuses,campus_name',
        ]);
        // Create a new campus record
        Campus::create([
            'campus_name' => $request->campus_name,
        ]);

        // Redirect or return a success message
        return redirect()->route('admin.manage-campus')->with('success', 'Campus added successfully');
    }

    public function storeDepartment(Request $request)
    {
        $request->validate([
            'department_id' => 'required|string|max:255',
            'department_name' => 'required|string|max:255',
        ]);

        Department::create($request->all());

        return redirect()->route('admin.manage-department')->with('success', 'Department created successfully.');
    }
    public function storeCampusDepartment(Request $request)
    {
        $request->validate([
            'campus_id' => 'required|exists:campuses,campus_id',
            'department_code' => 'required|exists:departments,department_code',
        ]);

        // Prevent duplicate record
        $exists = DB::table('campus_department')->where([
            'campus_id' => $request->campus_id,
            'department_code' => $request->department_code
        ])->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['This department is already assigned to the selected campus.']);
        }

        DB::table('campus_department')->insert([
            'campus_id' => $request->campus_id,
            'department_code' => $request->department_code,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.manage-department')->with('success', 'Department assigned to campus successfully.');
    }
    public function storeFaculty(Request $request)
    {
        $request->validate([
            'faculty_id' => 'required|string|max:255',
            'faculty_name' => 'required|string|max:255',
        ]);

        Faculty::create($request->all());

        return redirect()->route('admin.manage-faculty')->with('success', 'Faculty created successfully.');
    }
    public function storeHOD(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'hod_email' => 'required|email|unique:hods,hod_email',
            'hod_phone' => 'required|string|unique:hods,hod_phone',
            'campus_id' => 'required|exists:campuses,campus_id',
            'department_code' => 'required|exists:departments,department_code',
            'password' => 'required|string|min:6',
        ]);

        // ✅ Check if a HOD already exists for that department in the selected campus
        $exists = DB::table('hods')
            ->where('campus_id', $request->campus_id)
            ->where('department_code', $request->department_code)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['A HOD is already assigned to this department in the selected campus.'])
                ->withInput();
        }

        // ✅ Proceed with inserting if no duplicate
        DB::table('hods')->insert([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'hod_email' => $request->hod_email,
            'hod_phone' => $request->hod_phone,
            'campus_id' => $request->campus_id,
            'department_code' => $request->department_code,
            'password' => bcrypt($request->password),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.manage-hod')->with('success', 'HOD assigned successfully.');
    }

    public function getDepartmentsByCampus($campus_id)
    {
        $departments = DB::table('campus_department')
            ->join('departments', 'campus_department.department_code', '=', 'departments.department_code')
            ->where('campus_department.campus_id', $campus_id)
            ->select('departments.department_code', 'departments.department_name')
            ->distinct()
            ->get();

        return response()->json($departments);
    }
    public function dashboard()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.dashboard', compact('admin'));
    }

}