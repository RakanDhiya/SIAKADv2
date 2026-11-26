<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearSemesterTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => 'password',
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    private function createYear(
        string $name,
        bool $active = false
    ): AcademicYear {
        return AcademicYear::create([
            'name' => $name,
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => $active,
        ]);
    }

    public function test_activating_new_academic_year_deactivates_old_year_and_semester(): void
    {
        $admin = $this->admin();

        $oldYear = $this->createYear('2025/2026', true);

        $oldSemester = Semester::create([
            'academic_year_id' => $oldYear->id,
            'name' => 'Genap',
            'is_active' => true,
        ]);

        $newYear = $this->createYear('2026/2027');

        $this->actingAs($admin)
            ->post(route('academic-years.store'), [
                'name' => '2027/2028',
                'start_date' => '2027-07-01',
                'end_date' => '2028-06-30',
                'is_active' => '1',
            ])
            ->assertRedirect(route('academic-years.index'));

        $this->assertDatabaseHas('academic_years', [
            'id' => $oldYear->id,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('semesters', [
            'id' => $oldSemester->id,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('academic_years', [
            'name' => '2027/2028',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('academic_years', [
            'id' => $newYear->id,
            'is_active' => false,
        ]);
    }

    public function test_deactivating_academic_year_also_deactivates_its_semesters(): void
    {
        $admin = $this->admin();

        $year = $this->createYear('2026/2027', true);

        $semester = Semester::create([
            'academic_year_id' => $year->id,
            'name' => 'Ganjil',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('academic-years.update', $year), [
                'name' => $year->name,
                'start_date' => '2026-07-01',
                'end_date' => '2027-06-30',
                'is_active' => '0',
            ])
            ->assertRedirect(route('academic-years.index'));

        $this->assertDatabaseHas('academic_years', [
            'id' => $year->id,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('semesters', [
            'id' => $semester->id,
            'is_active' => false,
        ]);
    }

    public function test_semester_cannot_be_activated_under_inactive_academic_year(): void
    {
        $admin = $this->admin();

        $year = $this->createYear('2026/2027', false);

        $this->actingAs($admin)
            ->post(route('semesters.store'), [
                'academic_year_id' => $year->id,
                'name' => 'Ganjil',
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('academic_year_id');

        $this->assertDatabaseMissing('semesters', [
            'academic_year_id' => $year->id,
            'name' => 'Ganjil',
        ]);
    }

    public function test_activating_semester_deactivates_other_semesters(): void
    {
        $admin = $this->admin();

        $year = $this->createYear('2026/2027', true);

        $oldSemester = Semester::create([
            'academic_year_id' => $year->id,
            'name' => 'Ganjil',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('semesters.store'), [
                'academic_year_id' => $year->id,
                'name' => 'Genap',
                'is_active' => '1',
            ])
            ->assertRedirect(route('semesters.index'));

        $this->assertDatabaseHas('semesters', [
            'id' => $oldSemester->id,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('semesters', [
            'academic_year_id' => $year->id,
            'name' => 'Genap',
            'is_active' => true,
        ]);
    }
}