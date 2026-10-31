<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Departments Table
        Schema::create('departments', function (Blueprint $table) {
            $table->string('department_code', 10)->primary();
            $table->string('department_name');
            $table->timestamps();
        });

        // Faculties (Options) Table
        Schema::create('faculties', function (Blueprint $table) {
            $table->string('faculty_code', 10)->primary();
            $table->string('faculty_name');
            $table->string('department_code', 10);
            $table->foreign('department_code')->references('department_code')->on('departments')->onDelete('cascade');
            $table->timestamps();
        });

        // Campuses Table
        Schema::create('campuses', function (Blueprint $table) {
            $table->id('campus_id');
            $table->string('campus_name')->unique();
            $table->timestamps();
        });

        // CampusDepartment Table
        Schema::create('campus_department', function (Blueprint $table) {
            $table->id('campus_department_id');
            $table->unsignedBigInteger('campus_id');
            $table->foreign('campus_id')->references('campus_id')->on('campuses')->onDelete('cascade');
            $table->string('department_code', 10);
            $table->foreign('department_code')->references('department_code')->on('departments')->onDelete('cascade');
            $table->timestamps();
        });

        // CampusDepartmentOption Table
        Schema::create('campus_department_option', function (Blueprint $table) {
            $table->id('cdo_id');
            $table->unsignedBigInteger('campus_department_id');
            $table->foreign('campus_department_id')->references('campus_department_id')->on('campus_department')->onDelete('cascade');
            $table->string('faculty_code', 10);
            $table->foreign('faculty_code')->references('faculty_code')->on('faculties')->onDelete('cascade');
            $table->timestamps();
        });

        // Students Table
        Schema::create('students', function (Blueprint $table) {
            $table->string('student_reg_number')->primary();
            $table->string('student_first_name');
            $table->string('student_last_name');
            $table->enum('student_gender', ['Male', 'Female']);
            $table->string('student_email')->unique();
            $table->string('student_phone_number')->unique();
            $table->unsignedBigInteger('cdo_id');
            $table->foreign('cdo_id')->references('cdo_id')->on('campus_department_option')->onDelete('cascade');
            $table->timestamps();
        });

        // Projects Table
        Schema::create('projects', function (Blueprint $table) {
            $table->id('project_code');
            $table->string('project_name');
            $table->text('project_problems');
            $table->text('project_solutions');
            $table->text('project_abstract');
            $table->string('project_dissertation');
            $table->string('project_source_codes');
            $table->string('student_reg_number');
            $table->foreign('student_reg_number')->references('student_reg_number')->on('students')->onDelete('cascade');
            $table->boolean('approved')->default(false);
            $table->dateTime('submission_date')->nullable();
            $table->timestamps();
        });

        // Supervisors Table
        Schema::create('supervisors', function (Blueprint $table) {
            $table->string('supervisor_email')->primary();
            $table->string('supervisor_first_name');
            $table->string('supervisor_last_name');
            $table->string('supervisor_phone_number');
            $table->string('supervisor_password');
            $table->timestamps();
        });

        // ProjectSupervisor Table
        Schema::create('project_supervisor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_code');
            $table->string('supervisor_email');
            $table->foreign('project_code')->references('project_code')->on('projects')->onDelete('cascade');
            $table->foreign('supervisor_email')->references('supervisor_email')->on('supervisors')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_supervisor');
        Schema::dropIfExists('supervisors');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('students');
        Schema::dropIfExists('campus_department_option');
        Schema::dropIfExists('campus_department');
        Schema::dropIfExists('campuses');
        Schema::dropIfExists('faculties');
        Schema::dropIfExists('departments');
    }
};