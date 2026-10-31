<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Student; // Ensure the Student model exists in the App\Models namespace
// Removed duplicate import of Supervisor model
use App\Models\Campus; // Ensure the Campus model exists in the App\Models namespace
use App\Models\Hod; // Ensure the Hod model exists in the App\Models namespace
use App\Models\User; // Ensure the User model exists in the App\Models namespace
use App\Models\Supervisor;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        $campuses = Campus::all();
        return view('auth.register', compact('campuses'));
    }


    public function register(Request $request)
    {
        $request->validate([
            'student_reg_number' => 'required|unique:users,StudentRegNumber',
            'student_email' => 'required|email|unique:users,StudentEmail',
            'password' => 'required|min:6|confirmed',
            'student_first_name' => 'required',
            'student_last_name' => 'required',
            'student_gender' => 'required|in:Male,Female',
            'student_phone_number' => 'required|unique:students,student_phone_number',
            'cdo_id' => 'required|exists:campus_department_option,cdo_id',
            'campus_department_id' => 'required|exists:campus_department,campus_department_id',
        ]);

        // Save in users table
        User::create([
            'StudentRegNumber' => $request->student_reg_number,
            'StudentEmail' => $request->student_email,
            'password' => Hash::make($request->password),
        ]);

        // Save in students table
        Student::create([
            'student_reg_number' => $request->student_reg_number,
            'student_email' => $request->student_email,
            'student_first_name' => $request->student_first_name,
            'student_last_name' => $request->student_last_name,
            'student_gender' => $request->student_gender,
            'student_phone_number' => $request->student_phone_number,
            'cdo_id' => $request->cdo_id,
        ]);

        return redirect()->route('login')->with('success', 'Registration successful. Please login.');
    }
    public function getCampusDepartments($campus_id)
    {
        $departments = DB::table('campus_department')
            ->join('departments', 'campus_department.department_code', '=', 'departments.department_code')
            ->where('campus_id', $campus_id)
            ->select('campus_department.campus_department_id', 'departments.department_name')
            ->get();

        return response()->json($departments);
    }

    public function getFacultiesByCampusDepartment($campus_department_id)
    {
        $faculties = DB::table('campus_department_option')
            ->join('faculties', 'campus_department_option.faculty_code', '=', 'faculties.faculty_code')
            ->where('campus_department_id', $campus_department_id)
            ->select('campus_department_option.cdo_id', 'faculties.faculty_name')
            ->get();

        return response()->json($faculties);
    }


    public function showLoginForm()
    {
        // Check if already logged in
        if (Session::has('user_role')) {
            $role = Session::get('user_role');
            return redirect()->route("$role.dashboard");
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email_or_reg' => 'required',
            'password' => 'required',
        ]);

        $loginField = $request->email_or_reg;
        $password = $request->password;

        // Check if input is email or registration number
        $isEmail = filter_var($loginField, FILTER_VALIDATE_EMAIL);

        // Try login as student
        if ($user = $this->attemptStudentLogin($loginField, $password, $isEmail)) {
            // Use Laravel's authentication for students only
            Auth::login($user);
            Session::put('user_role', 'student');
            $student = Student::where('Student_reg_number', $user->StudentRegNumber)->first();
            Session::put('student_data', $student);
            return redirect()->route('student.dashboard');
        }

        // Try login as HOD
        if ($hod = $this->attemptHODLogin($loginField, $password, $isEmail)) {
            // Store HOD in session instead of using Auth
            Session::put('user_role', 'hod');
            Session::put('hod_data', $hod);
            
            return redirect()->route('hod.dashboard');
            
        }

        // Try login as supervisor
        if ($supervisor = $this->attemptSupervisorLogin($loginField, $password, $isEmail)) {
            // Store supervisor in session instead of using Auth
            Session::put('user_role', 'supervisor');
            Session::put('supervisor_data', $supervisor);
            return redirect()->route('supervisor.dashboard');
        }

        return back()->withErrors(['login' => 'Invalid credentials.']);
    }

    private function attemptStudentLogin($loginField, $password, $isEmail)
    {
        $query = User::query();

        if ($isEmail) {
            $query->where('StudentEmail', $loginField);
        } else {
            $query->where('StudentRegNumber', $loginField);
        }

        $user = $query->first();

        if ($user && Hash::check($password, $user->password)) {
            return $user;
        }

        return null;
    }

    private function attemptHODLogin($loginField, $password, $isEmail)
    {
        if (!$isEmail) {
            return null; // HODs only login with email
        }

        $hod = HOD::where('hod_email', $loginField)->first();

        if ($hod && Hash::check($password, $hod->password)) {
            return $hod;
        }

        return null;
    }

    private function attemptSupervisorLogin($loginField, $password, $isEmail)
    {
        if (!$isEmail) {
            return null; // Supervisors only login with email
        }

        $supervisor = Supervisor::where('supervisor_email', $loginField)->first();

        if ($supervisor && Hash::check($password, $supervisor->supervisor_password)) {
            return $supervisor;
        }

        return null;
    }

    public function logout()
    {
        // Clear all session data
        Auth::logout();
        Session::forget('user_role');
        Session::forget('student_data');
        Session::forget('hod_data');
        Session::forget('supervisor_data');

        return redirect()->route('login');
    }
}