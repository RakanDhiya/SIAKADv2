<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Membuat tabel untuk penempatan kelas, kegiatan belajar, presensi, dan penilaian.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Riwayat penempatan siswa per tahun ajaran (kenaikan kelas = baris baru).
        Schema::create('class_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('classroom_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->string('status', 10)->default('aktif'); // aktif, naik, tinggal, lulus, pindah
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'academic_year_id']);
        });

        Schema::create('teaching_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->restrictOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->foreignId('classroom_id')->constrained()->restrictOnDelete();
            $table->foreignId('semester_id')->constrained()->restrictOnDelete();
            $table->timestamps();
            $table->unique(['subject_id', 'classroom_id', 'semester_id']);
        });

        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_assignment_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day'); // 1 = Senin ... 6 = Sabtu
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('teaching_assignment_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('status', 10)->default('hadir'); // hadir, izin, sakit, alpa
            $table->string('note', 150)->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'teaching_assignment_id', 'date']);
        });

        Schema::create('assessment_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_assignment_id')->constrained()->cascadeOnDelete();
            $table->string('name', 50);
            $table->decimal('weight', 5, 2);
            $table->timestamps();
            $table->unique(['teaching_assignment_id', 'name']);
        });

        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_component_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->decimal('score', 5, 2);
            $table->timestamps();
            $table->unique(['assessment_component_id', 'student_id']);
        });

        Schema::create('report_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->foreignId('semester_id')->constrained()->restrictOnDelete();
            $table->foreignId('classroom_id')->constrained()->restrictOnDelete();
            $table->decimal('final_score', 5, 2);
            $table->string('predicate', 2)->nullable();
            $table->string('remarks', 100)->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'subject_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_grades');
        Schema::dropIfExists('scores');
        Schema::dropIfExists('assessment_components');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('teaching_assignments');
        Schema::dropIfExists('class_enrollments');
    }
};