<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Membuat tabel untuk materi pembelajaran, tugas, pengumpulan, dan pengumuman.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_materials', function (Blueprint $table) {
            $table->id();
            $table->ForeignId('teaching_assignment_id')->constrained()->cascadeOnDelete();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('link_url')->nullable();
            $table->timestamps();
        });

        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->ForeignId('teaching_assignment_id')->constrained()->cascadeOnDelete();
            $table->string('title', 150);
            $table->text('description')->nullaable();
            $table->timestamp('due_at')->nullable();
            $table->unsignedSmallInteger('max_score')->default(100);
            $table->timestamps();
        });

        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->ForeignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->ForeignId('student_id')->constrained()->cascadeOnDelete();
            $table->text('content')->nullable();
            $table->string('file_path')->nullaable();
            $table->timestamp('submitted_at');
            $table->decimal('score', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();
            $table->unique(['assignment_id', 'student_id']);
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->text('body');
            $table->string('audience', 10)->default('semua'); // semua, guru, siswa, ortu)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullble();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('assignment');
        Schema::dropIfExists('learning_materials');
    }
};