<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\TeacherScope;
use App\Models\ReportGrade;
use App\Models\Score;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScoreController extends Controller
{
    use TeacherScope;

    public function index(Request $request)
    {
        $options = $this->assignmentOptions();
        $ta = null;
        $components = $students = $scores = $finals = collect();

        if ($id = $request->query('teaching_assignment_id')) {
            abort_unless(isset($options[$id]), 404);

            $ta = TeachingAssignment::with(['subject', 'classroom', 'semester.academicYear'])->findOrFail($id);
            $components = $ta->components()->orderBy('id')->get();
            $students = $ta->enrolledStudents()->get();
            $scores = Score::whereIn('assessment_component_id', $components->pluck('id'))
                ->get()->groupBy('student_id')->map->keyBy('assessment_component_id');
            $finals = ReportGrade::where('semester_id', $ta->semester_id)
                ->where('subject_id', $ta->subject_id)
                ->whereIn('student_id', $students->pluck('id'))->get()->keyBy('student_id');
        }

        return view('modules.scores.index', compact('options', 'ta', 'components', 'students', 'scores', 'finals'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'teaching_assignment_id' => ['required', Rule::in(array_keys($this->assignmentOptions()))],
            'scores'     => ['nullable', 'array'],
            'scores.*.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $ta = TeachingAssignment::findOrFail($data['teaching_assignment_id']);
        $componentIds = $ta->components()->pluck('id')->all();
        $studentIds = $ta->enrolledStudents()->pluck('id')->all();

        foreach (($data['scores'] ?? []) as $studentId => $byComponent) {
            if (! in_array((int) $studentId, $studentIds, true)) {
                continue;
            }

            foreach ($byComponent as $componentId => $value) {
                if (! in_array((int) $componentId, $componentIds, true)) {
                    continue;
                }

                $key = ['assessment_component_id' => $componentId, 'student_id' => $studentId];

                if ($value === null || $value === '') {
                    Score::where($key)->delete();
                } else {
                    Score::updateOrCreate($key, ['score' => $value]);
                }
            }
        }

        return redirect()
            ->route('scores.index', ['teaching_assignment_id' => $ta->id])
            ->with('success', 'Nilai berhasil disimpan.');
    }

    /** Hitung nilai rapor = rata-rata tertimbang dari komponen yang sudah memiliki nilai. */
    public function compute(Request $request)
    {
        $data = $request->validate([
            'teaching_assignment_id' => ['required', Rule::in(array_keys($this->assignmentOptions()))],
        ]);

        $ta = TeachingAssignment::with('subject')->findOrFail($data['teaching_assignment_id']);
        $components = $ta->components()->get()->keyBy('id');

        if ($components->isEmpty() || $components->sum('weight') <= 0) {
            return back()->with('error', 'Tambahkan komponen penilaian dengan bobot terlebih dahulu.');
        }

        $scores = Score::whereIn('assessment_component_id', $components->keys())->get()->groupBy('student_id');
        $count = 0;

        foreach ($ta->enrolledStudents()->get() as $student) {
            $rows = $scores->get($student->id);
            if (! $rows || $rows->isEmpty()) {
                continue;
            }

            $sum = 0;
            $weights = 0;
            foreach ($rows as $row) {
                $w = (float) $components[$row->assessment_component_id]->weight;
                $sum += (float) $row->score * $w;
                $weights += $w;
            }
            if ($weights <= 0) {
                continue;
            }

            $final = round($sum / $weights, 2);

            ReportGrade::updateOrCreate(
                ['student_id' => $student->id, 'subject_id' => $ta->subject_id, 'semester_id' => $ta->semester_id],
                [
                    'classroom_id' => $ta->classroom_id,
                    'final_score'  => $final,
                    'predicate'    => ReportGrade::predicate($final),
                    'remarks'      => $final >= $ta->subject->kkm ? 'Tuntas' : 'Belum tuntas',
                ]
            );
            $count++;
        }

        return back()->with('success', "Nilai rapor dihitung untuk {$count} siswa.");
    }
}
