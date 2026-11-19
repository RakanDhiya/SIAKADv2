<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesStudent;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\LearningMaterial;
use App\Models\ReportGrade;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Submission;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Halaman untuk Siswa dan Orang Tua (orang tua melihat anak yang terhubung). */
class MeController extends Controller
{
    use ResolvesStudent;

    /** @return array{students: mixed, student: mixed, semester: ?Semester, classroomId: ?int} */
    private function context(Request $request): array
    {
        [$students, $student] = $this->pickStudent($request);

        return [
            'students'    => $students,
            'student'     => $student,
            'semester'    => Semester::active(),
            'classroomId' => $student?->activeEnrollment?->classroom_id,
        ];
    }

    private function classAssignments(array $ctx)
    {
        return fn ($q) => $q->where('classroom_id', $ctx['classroomId'])->where('semester_id', $ctx['semester']?->id);
    }

    public function schedule(Request $request)
    {
        $ctx = $this->context($request);
        $schedules = collect();

        if ($ctx['classroomId'] && $ctx['semester']) {
            $schedules = Schedule::with(['teachingAssignment.subject', 'teachingAssignment.teacher'])
                ->whereHas('teachingAssignment', $this->classAssignments($ctx))
                ->orderBy('day')->orderBy('start_time')->get()->groupBy('day');
        }

        return view('modules.me.schedule', $ctx + ['schedules' => $schedules]);
    }

    public function scores(Request $request)
    {
        $ctx = $this->context($request);
        $assignments = collect();
        $finals = collect();

        if ($ctx['student'] && $ctx['classroomId'] && $ctx['semester']) {
            $studentId = $ctx['student']->id;

            $assignments = TeachingAssignment::with([
                'subject',
                'components' => fn ($q) => $q->orderBy('id'),
                'components.scores' => fn ($q) => $q->where('student_id', $studentId),
            ])->where('classroom_id', $ctx['classroomId'])->where('semester_id', $ctx['semester']->id)->get();

            $finals = ReportGrade::where('student_id', $studentId)
                ->where('semester_id', $ctx['semester']->id)->get()->keyBy('subject_id');
        }

        return view('modules.me.scores', $ctx + ['assignments' => $assignments, 'finals' => $finals]);
    }

    public function attendance(Request $request)
    {
        $ctx = $this->context($request);
        $rows = null;
        $summary = collect();

        if ($ctx['student']) {
            $rows = Attendance::with('teachingAssignment.subject')
                ->where('student_id', $ctx['student']->id)
                ->orderByDesc('date')->paginate(20)->withQueryString();

            $summary = Attendance::where('student_id', $ctx['student']->id)
                ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        }

        return view('modules.me.attendance', $ctx + ['rows' => $rows, 'summary' => $summary]);
    }

    public function materials(Request $request)
    {
        $ctx = $this->context($request);
        $materials = collect();

        if ($ctx['classroomId'] && $ctx['semester']) {
            $materials = LearningMaterial::with('teachingAssignment.subject')
                ->whereHas('teachingAssignment', $this->classAssignments($ctx))
                ->latest()->get();
        }

        return view('modules.me.materials', $ctx + ['materials' => $materials]);
    }

    public function assignments(Request $request)
    {
        $ctx = $this->context($request);
        $assignments = collect();

        if ($ctx['student'] && $ctx['classroomId'] && $ctx['semester']) {
            $studentId = $ctx['student']->id;

            $assignments = Assignment::with([
                'teachingAssignment.subject',
                'submissions' => fn ($q) => $q->where('student_id', $studentId),
            ])->whereHas('teachingAssignment', $this->classAssignments($ctx))
                ->orderByDesc('due_at')->get();
        }

        return view('modules.me.assignments', $ctx + ['assignments' => $assignments]);
    }

    public function assignment(Request $request, $id)
    {
        $ctx = $this->context($request);
        $assignment = Assignment::with('teachingAssignment.subject')->findOrFail($id);

        abort_unless($ctx["student"] && (int) $assignment->teachingAssignment->classroom_id === (int) $ctx["classroomId"], 404);

        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', $ctx['student']->id)->first();

        return view('modules.me.assignment', $ctx + ['assignment' => $assignment, 'submission' => $submission]);
    }

    public function submit(Request $request, $id)
    {
        $student = $request->user()->student;
        abort_unless($student, 403, 'Akun ini belum ditautkan ke data siswa.');

        $assignment = Assignment::with('teachingAssignment')->findOrFail($id);
        abort_unless((int) $assignment->teachingAssignment->classroom_id === (int) $student->activeEnrollment?->classroom_id, 404);

        $submission = Submission::where('assignment_id', $assignment->id)->where('student_id', $student->id)->first();

        if ($submission && $submission->graded_at) {
            return back()->with('error', 'Tugas sudah dinilai dan tidak dapat diubah lagi.');
        }

        $data = $request->validate([
            'content' => ['nullable', 'string', 'max:5000', 'required_without:file'],
            'file'    => ['nullable', 'file', 'max:5120', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,zip,txt'],
        ], ['content.required_without' => 'Isi jawaban atau unggah berkas.']);

        $payload = ['content' => $data['content'] ?? null, 'submitted_at' => now()];

        if ($request->hasFile('file')) {
            if ($submission?->file_path) {
                Storage::disk('public')->delete($submission->file_path);
            }
            $payload['file_path'] = $request->file('file')->store('submissions', 'public');
        }

        Submission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => $student->id],
            $payload
        );

        return redirect()->route('me.assignments.show', $assignment->id)
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }
}
