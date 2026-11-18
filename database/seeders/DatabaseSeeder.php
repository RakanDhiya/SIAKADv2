<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\AssessmentComponent;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\ClassEnrollment;
use App\Models\Classroom;
use App\Models\Guardian;
use App\Models\LearningMaterial;
use App\Models\Major;
use App\Models\Schedule;
use App\Models\Score;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Mengisi database dengan data contoh untuk pengujian fitur SIAKAD.
 * Semua akun demo menggunakan password: "password".
 *
 * Akun demo:
 * admin@siakad.test
 * guru@siakad.test
 * guru2@siakad.test
 * siswa@siakad.test
 * ortu@siakad.test
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun
        User::create(['name' => 'Administrator', 'email' => 'admin@siakad.test', 'password' => 'password', 'role' => 'admin']);
        $guruUser = User::create(['name' => 'Budi Hartono', 'email' => 'guru@siakad.test', 'password' => 'password', 'role' => 'guru']);
        $guru2User = User::create(['name' => 'Rina Marlina', 'email' => 'guru2@siakad.test', 'password' => 'password', 'role' => 'guru']);
        $siswaUser = User::create(['name' => 'Ahmad Fauzi', 'email' => 'siswa@siakad.test', 'password' => 'password', 'role' => 'siswa']);
        $ortuUser = User::create(['name' => 'Hendra Fauzi', 'email' => 'ortu@siakad.test', 'password' => 'password', 'role' => 'ortu']);

        // Tahun ajaran & semester
        $year = AcademicYear::create(['name' => '2026/2027', 'start_date' => '2026-07-13', 'end_date' => '2027-06-26', 'is_active' => true]);
        $next = AcademicYear::create(['name' => '2027/2028', 'start_date' => '2027-07-12', 'end_date' => '2028-06-24', 'is_active' => false]);
        $ganjil = Semester::create(['academic_year_id' => $year->id, 'name' => 'Ganjil', 'is_active' => true]);
        Semester::create(['academic_year_id' => $year->id, 'name' => 'Genap', 'is_active' => false]);
        Semester::create(['academic_year_id' => $next->id, 'name' => 'Ganjil', 'is_active' => false]);

        // Jurusan & mapel
        $rpl = Major::create(['code' => 'RPL', 'name' => 'Rekayasa Perangkat Lunak']);
        Major::create(['code' => 'TKJ', 'name' => 'Teknik Komputer dan Jaringan']);

        $subjects = [];
        foreach ([['MTK', 'Matematika', 75], ['BIN', 'Bahasa Indonesia', 75], ['PWB', 'Pemrograman Web', 78], ['BDT', 'Basis Data', 78]] as [$code, $name, $kkm]) {
            $subjects[$code] = Subject::create(['code' => $code, 'name' => $name, 'kkm' => $kkm]);
        }

        // Guru
        $t1 = Teacher::create(['user_id' => $guruUser->id, 'nip' => '198001012005011001', 'name' => 'Budi Hartono', 'gender' => 'L', 'phone' => '081200000001']);
        $t2 = Teacher::create(['user_id' => $guru2User->id, 'nip' => '198505052010012002', 'name' => 'Rina Marlina', 'gender' => 'P', 'phone' => '081200000002']);
        $t3 = Teacher::create(['nip' => '199002022015011003', 'name' => 'Agus Salim', 'gender' => 'L']);

        // Kelas
        $x1 = Classroom::create(['name' => 'X RPL 1', 'grade_level' => 10, 'major_id' => $rpl->id, 'academic_year_id' => $year->id, 'homeroom_teacher_id' => $t1->id]);
        $x2 = Classroom::create(['name' => 'X RPL 2', 'grade_level' => 10, 'major_id' => $rpl->id, 'academic_year_id' => $year->id, 'homeroom_teacher_id' => $t2->id]);
        Classroom::create(['name' => 'XI RPL 1', 'grade_level' => 11, 'major_id' => $rpl->id, 'academic_year_id' => $next->id, 'homeroom_teacher_id' => $t1->id]);

        // Siswa
        $names = ['Ahmad Fauzi', 'Siti Aisyah', 'Budi Santoso', 'Dewi Lestari', 'Rizky Pratama', 'Putri Ayu',
            'Andi Wijaya', 'Nurul Hidayah', 'Fajar Ramadhan', 'Indah Permata', 'Dimas Saputra', 'Salsabila Putri'];
        $students = [];
        foreach ($names as $i => $name) {
            $students[] = Student::create([
                'user_id' => $i === 0 ? $siswaUser->id : null,
                'nis' => '2026'.str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'nisn' => '00'.(76543210 + $i),
                'name' => $name,
                'gender' => $i % 2 === 0 ? 'L' : 'P',
                'birth_place' => 'Tangerang Selatan',
                'birth_date' => '2010-0'.(($i % 9) + 1).'-1'.($i % 9),
                'status' => 'aktif',
            ]);

            ClassEnrollment::create([
                'student_id' => $students[$i]->id,
                'classroom_id' => $i < 6 ? $x1->id : $x2->id,
                'academic_year_id' => $year->id,
                'status' => 'aktif',
            ]);
        }

        // Orang tua
        $ortu = Guardian::create(['user_id' => $ortuUser->id, 'name' => 'Hendra Fauzi', 'relationship' => 'ayah', 'phone' => '081300000001', 'occupation' => 'Wiraswasta']);
        $ortu->students()->sync([$students[0]->id, $students[1]->id]);

        // Penugasan guru (X RPL 1, semester ganjil) & jadwal
        $assignments = [
            [$t1, 'MTK', 1, '07:00', '08:30'],
            [$t2, 'BIN', 1, '08:30', '10:00'],
            [$t3, 'PWB', 2, '07:00', '09:30'],
            [$t1, 'BDT', 3, '10:15', '12:00'],
        ];
        $tas = [];
        foreach ($assignments as [$teacher, $code, $day, $start, $end]) {
            $ta = TeachingAssignment::create([
                'teacher_id' => $teacher->id, 'subject_id' => $subjects[$code]->id,
                'classroom_id' => $x1->id, 'semester_id' => $ganjil->id,
            ]);
            $tas[$code] = $ta;
            Schedule::create(['teaching_assignment_id' => $ta->id, 'day' => $day, 'start_time' => $start, 'end_time' => $end, 'room' => 'R.'.(100 + $day)]);
        }

        // Komponen penilaian & nilai contoh (Matematika)
        $components = [];
        foreach ([['Tugas', 30], ['UTS', 30], ['UAS', 40]] as [$name, $weight]) {
            $components[] = AssessmentComponent::create(['teaching_assignment_id' => $tas['MTK']->id, 'name' => $name, 'weight' => $weight]);
        }
        foreach (array_slice($students, 0, 6) as $i => $student) {
            foreach ($components as $j => $component) {
                Score::create(['assessment_component_id' => $component->id, 'student_id' => $student->id, 'score' => 70 + (($i * 5 + $j * 3) % 28)]);
            }
        }

        // Absensi contoh (kemarin)
        foreach (array_slice($students, 0, 6) as $i => $student) {
            Attendance::create([
                'student_id' => $student->id, 'teaching_assignment_id' => $tas['MTK']->id,
                'date' => today()->subDay()->toDateString(), 'status' => $i === 3 ? 'sakit' : 'hadir',
            ]);
        }

        // E-learning
        LearningMaterial::create(['teaching_assignment_id' => $tas['MTK']->id, 'title' => 'Pengantar Logika Matematika', 'description' => 'Ringkasan materi bab 1. Baca sebelum pertemuan berikutnya.', 'link_url' => 'https://id.wikipedia.org/wiki/Logika_matematika']);
        Assignment::create(['teaching_assignment_id' => $tas['MTK']->id, 'title' => 'Latihan Soal Bab 1', 'description' => 'Kerjakan soal nomor 1-10 dan kumpulkan jawabannya.', 'due_at' => now()->addDays(7), 'max_score' => 100]);

        Announcement::create(['title' => 'Selamat datang di SIAKAD', 'body' => "Gunakan menu di sebelah kiri untuk mengakses jadwal, tugas, nilai, dan absensi.\nHubungi admin sekolah jika ada kendala akun.", 'audience' => 'semua', 'created_by' => 1, 'published_at' => now()]);
    }
}
