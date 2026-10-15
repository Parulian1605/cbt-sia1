<?php

namespace App\Filament\Test\Resources\Exams\Pages;

use App\Filament\Test\Resources\Exams\ExamResource;
use App\Models\Exam;
use App\Models\ExamResult;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ManageExams extends Page
{
    protected static string $resource = ExamResource::class;

    protected string $view = 'filament.test.resources.exams.pages.manage-exams';

    protected static ?string $title = 'Sesi Ujian';

    public string $search = '';

    public string $filter = 'all'; // 'all', 'in_progress', 'completed'

    public function getExams(bool $ignoreFilter = false): Collection
    {
        $now    = now();
        $userId = Auth::id();

        $query = Exam::with(['subjects'])
            ->where('is_available', true)
            ->where(function ($q) use ($now) {
                $q->where(function ($q) use ($now) {
                    $q->where('exact_time', true)
                      ->where('started_at', '<=', $now)
                      ->whereRaw('DATE_ADD(started_at, INTERVAL duration MINUTE) >= ?', [$now]);
                })->orWhere(function ($q) use ($now) {
                    $q->whereNotNull('expired_at')
                      ->where('expired_at', '>=', $now);
                });
            })
            ->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->latest('started_at');

        $exams = $query->get()->map(function (Exam $exam) use ($userId) {
            $total = $exam->subjects->count();
            
            $completedSubjectIds = ExamResult::where('exam_id', $exam->id)
                ->where('user_id', $userId)
                ->whereNotNull('subject_id')
                ->pluck('subject_id')
                ->unique()
                ->toArray();

            $done = count($completedSubjectIds);

            $exam->_completedSubjectIds = $completedSubjectIds;
            $exam->_done       = $done;
            $exam->_total      = $total;
            $exam->_allDone    = ($total > 0 && $done >= $total);
            $exam->_percentage = $total > 0 ? min(100, round(($done / $total) * 100)) : 0;
            $exam->_isLocked   = $exam->started_at && $exam->started_at > now();
            
            $exam->_progress = $total === 0 ? 'Belum ada mapel'
                : ($done >= $total ? 'Semua mapel selesai'
                : ($done === 0 ? 'Belum dikerjakan'
                : "{$done}/{$total} mapel selesai"));

            return $exam;
        });

        if ($ignoreFilter || $this->filter === 'all') {
            return $exams;
        }

        if ($this->filter === 'in_progress') {
            return $exams->filter(fn($e) => !$e->_allDone);
        }

        if ($this->filter === 'completed') {
            return $exams->filter(fn($e) => $e->_allDone);
        }

        return $exams;
    }

    public function getStats(): array
    {
        $allExams = $this->getExams(ignoreFilter: true);
        $total = $allExams->count();
        $completed = $allExams->where('_allDone', true)->count();
        $inProgress = $allExams->filter(fn($e) => $e->_done > 0 && !$e->_allDone)->count();
        $notStarted = $allExams->where('_done', 0)->count();

        return [
            'total'       => $total,
            'completed'   => $completed,
            'in_progress' => $inProgress,
            'not_started' => $notStarted,
        ];
    }

    public function getExamUrl(Exam $exam): string
    {
        return route('filament.test.resources.exams.mulai', ['exam' => $exam->id]);
    }
}
