<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // USERS TABLE
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->string('school_name')->nullable();
            $table->string('avatar')->nullable();
            $table->enum('role', ['admin', 'wali_kelas', 'super_admin'])->default('wali_kelas');
            $table->enum('tier', ['free', 'pro', 'enterprise'])->default('free');
            $table->date('subscription_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // CLASSES TABLE
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('alias', 50)->nullable();
            $table->string('jurusan');
            $table->string('tingkat', 10)->nullable();
            $table->integer('school_year_start')->default(date('Y'));
            $table->integer('school_year_end')->default(date('Y') + 1);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // STUDENTS TABLE
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->string('nisn', 20)->nullable()->index();
            $table->string('nis', 20)->nullable();
            $table->string('name');
            $table->string('gender', 20)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place', 100)->nullable();
            $table->string('religion', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('father_name', 100)->nullable();
            $table->string('mother_name', 100)->nullable();
            $table->string('parent_phone', 20)->nullable();
            $table->string('parent_whatsapp', 20)->nullable();
            $table->text('emergency_contact')->nullable();
            $table->string('photo')->nullable();
            $table->integer('poin')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // ATTENDANCE_SESSIONS TABLE
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id');
            $table->date('date');
            $table->string('token', 64)->unique();
            $table->string('pin', 4)->nullable();
            $table->string('method', 20)->default('database');
            $table->enum('status', ['pending', 'active', 'used', 'expired'])->default('active');
            $table->timestamp('expires_at');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('students')->nullOnDelete();
            $table->string('submitted_by_name')->nullable();
            $table->timestamps();
            $table->index(['class_id', 'date', 'status']);
        });

        // ATTENDANCES TABLE
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_session_id')->cascadeOnDelete();
            $table->foreignId('student_id')->cascadeOnDelete();
            $table->foreignId('user_id');
            $table->foreignId('class_id')->nullable();
            $table->date('date');
            $table->enum('status', ['hadir', 'terlambat', 'sakit', 'izin', 'alpa']);
            $table->text('notes')->nullable();
            $table->integer('minutes_late')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'date']);
            $table->index(['class_id', 'date']);
        });

        // SCHEDULES TABLE
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->cascadeOnDelete();
            $table->string('subject');
            $table->string('teacher_name', 100)->nullable();
            $table->enum('day', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu']);
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('week_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // VIOLATIONS TABLE
        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->cascadeOnDelete();
            $table->foreignId('user_id');
            $table->foreignId('class_id');
            $table->string('category');
            $table->text('description');
            $table->integer('poin_reduced')->default(0);
            $table->integer('poin_before');
            $table->integer('poin_after');
            $table->enum('severity', ['ringan', 'sedang', 'berat'])->default('ringan');
            $table->date('date');
            $table->string('attachment')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        // CASH_BOOKS TABLE
        Schema::create('cash_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->cascadeOnDelete();
            $table->foreignId('user_id');
            $table->string('type', 20);
            $table->string('category');
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->string('receipt')->nullable();
            $table->foreignId('student_id')->nullable()->cascadeOnDelete();
            $table->string('created_by_name');
            $table->timestamps();
        });

        // ORGANIZATION_STRUCTURES TABLE
        Schema::create('organization_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->cascadeOnDelete();
            $table->foreignId('student_id')->cascadeOnDelete();
            $table->string('position');
            $table->string('academic_year');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // SEATING_CHARTS TABLE
        Schema::create('seating_charts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->cascadeOnDelete();
            $table->string('name');
            $table->json('layout');
            $table->date('effective_date');
            $table->date('expired_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // JOURNALS TABLE
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->cascadeOnDelete();
            $table->string('category');
            $table->string('subject');
            $table->text('content');
            $table->date('date');
            $table->string('outcome')->nullable();
            $table->string('follow_up')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
        });

        // WA_QUEUES TABLE
        Schema::create('wa_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->cascadeOnDelete();
            $table->string('phone', 20);
            $table->string('recipient_name');
            $table->text('message');
            $table->enum('type', ['attendance', 'announcement', 'warning', 'report'])->default('announcement');
            $table->enum('status', ['pending', 'processing', 'sent', 'failed'])->default('pending');
            $table->text('response')->nullable();
            $table->integer('attempts')->default(0);
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        // SCHEDULE_JOBS TABLE
        Schema::create('schedule_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->cascadeOnDelete();
            $table->string('type');
            $table->string('class_ids');
            $table->time('schedule_time');
            $table->enum('day_of_week', ['everyday', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'])->default('everyday');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run')->nullable();
            $table->timestamps();
        });

        // API_TOKENS TABLE
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->cascadeOnDelete();
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->json('abilities');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        // ACTIVITY_LOGS TABLE
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->cascadeOnDelete();
            $table->string('action');
            $table->string('model_type')->nullable();
            $table->bigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('activity_logs');
        Schema::drop('api_tokens');
        Schema::drop('schedule_jobs');
        Schema::drop('wa_queues');
        Schema::drop('journals');
        Schema::drop('seating_charts');
        Schema::drop('organization_structures');
        Schema::drop('cash_books');
        Schema::drop('violations');
        Schema::drop('schedules');
        Schema::drop('attendances');
        Schema::drop('attendance_sessions');
        Schema::drop('students');
        Schema::drop('classes');
        Schema::drop('users');
    }
};
