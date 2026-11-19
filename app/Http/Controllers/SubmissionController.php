<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\TeacherScope;
use App\Models\Assignment;
use Illuminate\Http\Request;

/** Guru melihat pengumpulan tugas dan memberi nilai. */
class SubmissionController extends Controller
{
    use TeacherScope;

    private function findAssignment($id): Assignment
    {
        $assignment = Assignment::with(['teachingAssignment.subject', 'teachingAssignment.classroom'])->findOrFail($id);

        $teacherId = $this->teacherId();
        abort_if($teacherId && (int) $assignment->teachingAssignment->teacher_id !== $teacherId, 404);

        return $assignment;
    }

    public function index($id)
    {
        $assignment = $this->findAssignment($id);
        $students = $assignment->teachingAssignment->enrolledStudents()->get();
        $submissions = $assignment->submissions()->get()->keyBy('student_id');

        return view('modules.submissions.index', compact('assignment', 'students', 'submissions'));
    }

    public function grade(Request $request, $id)
    {
        $assignment = $this->findAssignment($id);

        $data = $request->validate([
            'score'      => ['nullable', 'array'],
            'score.*'    => ['nullable', 'numeric', 'min:0', 'max:'.$assignment->max_score],
            'feedback'   => ['nullable', 'array'],
            'feedback.*' => ['nullable', 'string', 'max:500'],
        ]);

        $submissions = $assignment->submissions()->get()->keyBy('id');

        foreach (($data['score'] ?? []) as $submissionId => $score) {
            $submission = $submissions->get((int) $submissionId);
            if (! $submission) {
                continue;
            }

            $submission->update([
                'score'     => $score,
                'feedback'  => $data['feedback'][$submissionId] ?? null,
                'graded_at' => $score === null ? null : now(),
            ]);
        }

        return back()->with('success', 'Nilai tugas berhasil disimpan.');
    }
}
