<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassEnrollment;
use App\Models\Classroom;
use App\Models\Major;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionTest extends TestCase
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

    private function createYear(string $name, bool $active): AcademicYear
    {
        return AcademicYear::create([
            'name' => $name,
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => $active,
        ]);
    }

    private function createClassroom(
        AcademicYear $year,
        int $grade,
        ?int $majorId = null,
        string $name = 'Kelas Test'
    ): Classroom {
        return Classroom::create([
            'name' => $name,
            'grade_level' => $grade,
            'major_id' => $majorId,
            'academic_year_id' => $year->id,
        ]);
    }

    private function createStudent(string $status = 'aktif'): Student
    {
        return Student::create([
            'nis' => 'NIS'.uniqid(),
            'name' => 'Siswa Test',
            'status' => $status,
        ]);
    }

    private function createEnrollment(
        Student $student,
        Classroom $classroom,
        AcademicYear $year
    ): ClassEnrollment {
        return ClassEnrollment::create([
            'student_id' => $student->id,
            'classroom_id' => $classroom->id,
            'academic_year_id' => $year->id,
            'status' => 'aktif',
        ]);
    }

    public function test_student_can_be_promoted_from_grade_10_to_11(): void
    {
        $admin = $this->admin();

        $oldYear = $this->createYear('2025/2026', false);
        $activeYear = $this->createYear('2026/2027', true);

        $source = $this->createClassroom($oldYear, 10, null, 'X RPL 1');
        $target = $this->createClassroom($activeYear, 11, null, 'XI RPL 1');

        $student = $this->createStudent();
        $enrollment = $this->createEnrollment($student, $source, $oldYear);

        $this->actingAs($admin)
            ->post(route('promotion.store'), [
                'from_classroom_id' => $source->id,
                'to_classroom_id' => $target->id,
                'result' => [
                    $enrollment->id => 'naik',
                ],
            ])
            ->assertRedirect(route('promotion.index', [
                'from_classroom_id' => $source->id,
            ]));

        $this->assertDatabaseHas('class_enrollments', [
            'id' => $enrollment->id,
            'status' => 'naik',
        ]);

        $this->assertDatabaseHas('class_enrollments', [
            'student_id' => $student->id,
            'classroom_id' => $target->id,
            'academic_year_id' => $activeYear->id,
            'status' => 'aktif',
        ]);
    }

    public function test_student_can_be_promoted_from_grade_11_to_12(): void
    {
        $admin = $this->admin();

        $oldYear = $this->createYear('2025/2026', false);
        $activeYear = $this->createYear('2026/2027', true);

        $source = $this->createClassroom($oldYear, 11, null, 'XI RPL 1');
        $target = $this->createClassroom($activeYear, 12, null, 'XII RPL 1');

        $student = $this->createStudent();
        $enrollment = $this->createEnrollment($student, $source, $oldYear);

        $this->actingAs($admin)
            ->post(route('promotion.store'), [
                'from_classroom_id' => $source->id,
                'to_classroom_id' => $target->id,
                'result' => [
                    $enrollment->id => 'naik',
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('class_enrollments', [
            'student_id' => $student->id,
            'classroom_id' => $target->id,
            'status' => 'aktif',
        ]);
    }

    public function test_grade_12_student_can_graduate(): void
    {
        $admin = $this->admin();

        $oldYear = $this->createYear('2025/2026', false);
        $this->createYear('2026/2027', true);

        $source = $this->createClassroom($oldYear, 12, null, 'XII RPL 1');

        $student = $this->createStudent();
        $enrollment = $this->createEnrollment($student, $source, $oldYear);

        $this->actingAs($admin)
            ->post(route('promotion.store'), [
                'from_classroom_id' => $source->id,
                'result' => [
                    $enrollment->id => 'lulus',
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'status' => 'lulus',
        ]);

        $this->assertDatabaseHas('class_enrollments', [
            'id' => $enrollment->id,
            'status' => 'lulus',
        ]);

        $this->assertDatabaseCount('class_enrollments', 1);
    }

    public function test_student_can_repeat_same_grade_in_new_academic_year(): void
    {
        $admin = $this->admin();

        $oldYear = $this->createYear('2025/2026', false);
        $activeYear = $this->createYear('2026/2027', true);

        $source = $this->createClassroom($oldYear, 10, null, 'X RPL 1');
        $target = $this->createClassroom($activeYear, 10, null, 'X RPL 2');

        $student = $this->createStudent();
        $enrollment = $this->createEnrollment($student, $source, $oldYear);

        $this->actingAs($admin)
            ->post(route('promotion.store'), [
                'from_classroom_id' => $source->id,
                'stay_classroom_id' => $target->id,
                'result' => [
                    $enrollment->id => 'tinggal',
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('class_enrollments', [
            'id' => $enrollment->id,
            'status' => 'tinggal',
        ]);

        $this->assertDatabaseHas('class_enrollments', [
            'student_id' => $student->id,
            'classroom_id' => $target->id,
            'status' => 'aktif',
        ]);
    }

    public function test_grade_12_student_cannot_be_promoted(): void
    {
        $admin = $this->admin();

        $oldYear = $this->createYear('2025/2026', false);
        $activeYear = $this->createYear('2026/2027', true);

        $source = $this->createClassroom($oldYear, 12);
        $target = $this->createClassroom($activeYear, 12, null, 'Kelas Tujuan');

        $student = $this->createStudent();
        $enrollment = $this->createEnrollment($student, $source, $oldYear);

        $this->actingAs($admin)
            ->post(route('promotion.store'), [
                'from_classroom_id' => $source->id,
                'to_classroom_id' => $target->id,
                'result' => [
                    $enrollment->id => 'naik',
                ],
            ])
            ->assertSessionHasErrors('result');

        $this->assertDatabaseHas('class_enrollments', [
            'id' => $enrollment->id,
            'status' => 'aktif',
        ]);

        $this->assertDatabaseCount('class_enrollments', 1);
    }

    public function test_student_cannot_be_promoted_to_different_major(): void
    {
        $admin = $this->admin();

        $oldYear = $this->createYear('2025/2026', false);
        $activeYear = $this->createYear('2026/2027', true);

        $majorA = Major::create([
            'code' => 'RPL',
            'name' => 'Rekayasa Perangkat Lunak',
        ]);

        $majorB = Major::create([
            'code' => 'TKJ',
            'name' => 'Teknik Komputer dan Jaringan',
        ]);

        $source = $this->createClassroom($oldYear, 10, $majorA->id);
        $target = $this->createClassroom($activeYear, 11, $majorB->id);

        $student = $this->createStudent();
        $enrollment = $this->createEnrollment($student, $source, $oldYear);

        $this->actingAs($admin)
            ->post(route('promotion.store'), [
                'from_classroom_id' => $source->id,
                'to_classroom_id' => $target->id,
                'result' => [
                    $enrollment->id => 'naik',
                ],
            ])
            ->assertSessionHasErrors('to_classroom_id');

        $this->assertDatabaseCount('class_enrollments', 1);
    }

    public function test_target_class_must_belong_to_active_academic_year(): void
    {
        $admin = $this->admin();

        $oldYear = $this->createYear('2025/2026', false);
        $activeYear = $this->createYear('2026/2027', true);
        $inactiveYear = $this->createYear('2027/2028', false);

        $source = $this->createClassroom($oldYear, 10);
        $target = $this->createClassroom($inactiveYear, 11);

        $student = $this->createStudent();
        $enrollment = $this->createEnrollment($student, $source, $oldYear);

        $this->actingAs($admin)
            ->post(route('promotion.store'), [
                'from_classroom_id' => $source->id,
                'to_classroom_id' => $target->id,
                'result' => [
                    $enrollment->id => 'naik',
                ],
            ])
            ->assertSessionHasErrors('to_classroom_id');

        $this->assertDatabaseMissing('class_enrollments', [
            'student_id' => $student->id,
            'academic_year_id' => $activeYear->id,
        ]);

        $this->assertDatabaseCount('class_enrollments', 1);
    }

    public function test_inactive_student_cannot_be_processed(): void
    {
        $admin = $this->admin();

        $oldYear = $this->createYear('2025/2026', false);
        $activeYear = $this->createYear('2026/2027', true);

        $source = $this->createClassroom($oldYear, 10);
        $target = $this->createClassroom($activeYear, 11);

        $student = $this->createStudent('pindah');
        $enrollment = $this->createEnrollment($student, $source, $oldYear);

        $this->actingAs($admin)
            ->post(route('promotion.store'), [
                'from_classroom_id' => $source->id,
                'to_classroom_id' => $target->id,
                'result' => [
                    $enrollment->id => 'naik',
                ],
            ])
            ->assertSessionHasErrors('result');

        $this->assertDatabaseCount('class_enrollments', 1);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'status' => 'pindah',
        ]);
    }
}