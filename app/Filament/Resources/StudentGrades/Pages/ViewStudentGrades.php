<?php

namespace App\Filament\Resources\StudentGrades\Pages;

use App\Filament\Resources\StudentGrades\StudentGradesResource;
use App\Models\Exam;
use App\Models\ExamResult;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ViewStudentGrades extends Page
{
    protected static string $resource = StudentGradesResource::class;

    protected string $view = 'filament.resources.student-grades.pages.view-student-grades';

    public Exam $record;

    public function mount(int|string $record): void
    {
        $this->record = Exam::findOrFail($record);
    }

    public function getTitle(): string|Htmlable
    {
        return 'Nilai — ' . $this->record->title;
    }

    public function getBreadcrumbs(): array
    {
        return [
            StudentGradesResource::getUrl() => 'Nilai Siswa',
            '' => $this->record->title,
        ];
    }

    public function getResults()
    {
        return ExamResult::with(['user.student'])
            ->where('exam_id', $this->record->id)
            ->orderByDesc('score')
            ->get();
    }

    public function getStats(): array
    {
        $results = ExamResult::where('exam_id', $this->record->id)->get();
        $total   = $results->count();
        $threshold = (float) $this->record->threshold;
        $passed  = $results->filter(fn ($r) => (float) $r->score >= $threshold)->count();

        return [
            'total'     => $total,
            'passed'    => $passed,
            'failed'    => $total - $passed,
            'avg'       => $total ? round($results->avg('score'), 2) : null,
            'highest'   => $total ? $results->max('score') : null,
            'lowest'    => $total ? $results->min('score') : null,
            'threshold' => $threshold,
        ];
    }
}
