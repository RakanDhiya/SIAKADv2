<?php

namespace Tests\Feature;

use App\Models\AcademicEvent;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicCalendarTest extends TestCase
{
    use RefreshDatabase;

    private AcademicYear $academicYear;

    private Semester $semester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->academicYear = AcademicYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);

        $this->semester = Semester::create([
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Ganjil',
            'is_active' => true,
        ]);
    }

    /**
     * Membuat akun pengujian berdasarkan role.
     */
    private function createUser(string $role): User
    {
        return User::create([
            'name' => 'Test '.ucfirst($role),
            'email' => $role.'@example.com',
            'password' => 'password',
            'role' => $role,
            'is_active' => true,
        ]);
    }

    /**
     * Menyiapkan data agenda pengujian.
     */
    private function eventData(): array
    {
        return [
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'title' => 'Ujian Tengah Semester',
            'category' => 'Ujian',
            'start_date' => '2026-09-14',
            'end_date' => '2026-09-18',
            'description' => 'Pelaksanaan ujian tengah semester.',
            'location' => 'Ruang Kelas',
        ];
    }

    /**
     * Semua role yang sudah login dapat melihat kalender akademik.
     */
    public function test_all_authenticated_roles_can_view_academic_calendar(): void
    {
        foreach (['admin', 'guru', 'siswa', 'ortu'] as $role) {
            $user = $this->createUser($role);

            $this->actingAs($user)
                ->get(route('academic-calendar.index'))
                ->assertOk();
        }
    }

    /**
     * Admin dapat menambahkan agenda akademik.
     */
    public function test_admin_can_create_academic_event(): void
    {
        $admin = $this->createUser('admin');

        $this->actingAs($admin)
            ->post(route('academic-calendar.store'), $this->eventData())
            ->assertRedirect(route('academic-calendar.index'));

        $this->assertDatabaseHas('academic_events', [
            'title' => 'Ujian Tengah Semester',
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
        ]);
    }

    /**
     * Admin dapat mengubah agenda akademik.
     */
    public function test_admin_can_update_academic_event(): void
    {
        $admin = $this->createUser('admin');

        $event = AcademicEvent::create($this->eventData());

        $this->actingAs($admin)
            ->put(route('academic-calendar.update', $event), [
                ...$this->eventData(),
                'title' => 'Ujian Akhir Semester',
            ])
            ->assertRedirect(route('academic-calendar.index'));

        $this->assertDatabaseHas('academic_events', [
            'id' => $event->id,
            'title' => 'Ujian Akhir Semester',
        ]);
    }

    /**
     * Admin dapat menghapus agenda akademik.
     */
    public function test_admin_can_delete_academic_event(): void
    {
        $admin = $this->createUser('admin');

        $event = AcademicEvent::create($this->eventData());

        $this->actingAs($admin)
            ->delete(route('academic-calendar.destroy', $event))
            ->assertRedirect(route('academic-calendar.index'));

        $this->assertDatabaseMissing('academic_events', [
            'id' => $event->id,
        ]);
    }

    /**
     * Guru, siswa, dan orang tua tidak dapat mengelola agenda.
     */
    public function test_non_admin_roles_cannot_manage_academic_events(): void
    {
        $event = AcademicEvent::create($this->eventData());

        foreach (['guru', 'siswa', 'ortu'] as $role) {
            $user = $this->createUser($role);

            $this->actingAs($user)
                ->post(route('academic-calendar.store'), $this->eventData())
                ->assertForbidden();

            $this->actingAs($user)
                ->put(route('academic-calendar.update', $event), [
                    ...$this->eventData(),
                    'title' => 'Agenda Diubah',
                ])
                ->assertForbidden();

            $this->actingAs($user)
                ->delete(route('academic-calendar.destroy', $event))
                ->assertForbidden();
        }

        $this->assertDatabaseHas('academic_events', [
            'id' => $event->id,
            'title' => 'Ujian Tengah Semester',
        ]);
    }

    /**
     * Tanggal akhir agenda tidak boleh sebelum tanggal mulai.
     */
    public function test_event_end_date_must_not_be_before_start_date(): void
    {
        $admin = $this->createUser('admin');

        $data = $this->eventData();
        $data['start_date'] = '2026-09-20';
        $data['end_date'] = '2026-09-15';

        $this->actingAs($admin)
            ->post(route('academic-calendar.store'), $data)
            ->assertSessionHasErrors('end_date');

        $this->assertDatabaseCount('academic_events', 0);
    }

    /**
     * Semester harus berasal dari tahun ajaran yang dipilih.
     */
    public function test_event_semester_must_belong_to_selected_academic_year(): void
    {
        $admin = $this->createUser('admin');

        $anotherYear = AcademicYear::create([
            'name' => '2027/2028',
            'start_date' => '2027-07-01',
            'end_date' => '2028-06-30',
            'is_active' => false,
        ]);

        $data = $this->eventData();
        $data['academic_year_id'] = $anotherYear->id;

        $this->actingAs($admin)
            ->post(route('academic-calendar.store'), $data)
            ->assertSessionHasErrors('semester_id');

        $this->assertDatabaseCount('academic_events', 0);
    }
}