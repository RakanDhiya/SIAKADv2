<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_events', function (Blueprint $table) {
            $table->foreignId('academic_year_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('semester_id')
                ->nullable()
                ->constrained()
                ->restrictOnDelete();

            $table->string('title', 150);
            $table->string('category', 30);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->string('location', 150)->nullable();

            $table->index(['academic_year_id', 'start_date']);
            $table->index(['semester_id', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::table('academic_events', function (Blueprint $table) {
            $table->dropIndex(['academic_year_id', 'start_date']);
            $table->dropIndex(['semester_id', 'start_date']);

            $table->dropConstrainedForeignId('semester_id');
            $table->dropConstrainedForeignId('academic_year_id');

            $table->dropColumn([
                'title',
                'category',
                'start_date',
                'end_date',
                'description',
                'location',
            ]);
        });
    }
};