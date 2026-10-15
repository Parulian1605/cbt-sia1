<?php

namespace App\Filament\Test\Resources\Exams\Pages;

use App\Filament\Test\Resources\Exams\ExamResource;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamResult;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class StartingExam extends Page
{
    protected static string $resource = ExamResource::class;

    protected string $view = 'filament.test.resources.exams.pages.starting-exam';

    // === State: 'choose' | 'exam' | 'result' ===
    public string $step = 'choose';

    // Data ujian
    public int $examId;
    public string $examTitle = '';
    public float $threshold = 0;
    public int $examDuration = 0; // durasi dalam menit

    // Daftar mapel (dari pivot) untuk halaman pilih mapel
    // [ ['id'=>, 'name'=>, 'qty'=>, 'done'=>bool], ... ]
    public array $subjectList = [];

    // Mapel yang sedang dikerjakan
    public ?int $activeSubjectId = null;
    public string $activeSubjectName = '';
    public int $activeSubjectIndex = 0; // index di subjectList

    // Soal-soal mapel aktif
    public array $collection = [];   // [ ['id'=>, 'name'=>, 'soals'=>[...]] ] — hanya 1 mapel
    public array $jawaban = [];       // [question_id => answer_id]
    public int $totalSoal = 0;

    // Hasil per-mapel (setelah submit)
    public bool $isSubmitted = false;
    public float $finalScore = 0;
    public int $finalCorrect = 0;
    public int $finalTotal = 0;
    public ?int $examResultId = null;

    #[Computed]
    public function totalAnswered(): int
    {
        return collect($this->jawaban)->filter(fn($v) => $v !== null && $v !== '')->count();
    }

    #[Computed]
    public function allAnswered(): bool
    {
        return $this->totalSoal > 0 && $this->totalAnswered >= $this->totalSoal;
    }

    #[Computed]
    public function allSubjectsDone(): bool
    {
        return collect($this->subjectList)->every(fn($s) => $s['done']);
    }

    #[Computed]
    public function doneCount(): int
    {
        return collect($this->subjectList)->where('done', true)->count();
    }

    public function mount(Exam $exam): void
    {
        $this->examId       = $exam->id;
        $this->examTitle    = $exam->title;
        $this->threshold    = (float) $exam->threshold;
        $this->examDuration = (int) $exam->duration;

        $userId = Auth::id();

        // Bangun daftar mapel
        foreach ($exam->subjects as $mapel) {
            $done = ExamResult::where('exam_id', $exam->id)
                ->where('user_id', $userId)
                ->where('subject_id', $mapel->id)
                ->exists();

            $this->subjectList[] = [
                'id'   => $mapel->id,
                'name' => $mapel->name,
                'qty'  => $mapel->pivot->qty,
                'done' => $done,
            ];
        }

        $this->step = 'choose';
    }

    /**
     * Siswa memilih satu mapel untuk dikerjakan.
     */
    public function chooseSubject(int $subjectId): void
    {
        $index = collect($this->subjectList)->search(fn($s) => $s['id'] === $subjectId);

        if ($index === false) return;

        $subject = $this->subjectList[$index];

        if ($subject['done']) return; // sudah dikerjakan

        $exam = Exam::with(['subjects' => fn($q) => $q->where('subjects.id', $subjectId)])->findOrFail($this->examId);

        $mapel = $exam->subjects->first();
        if (! $mapel) return;

        $soals = $mapel->questions()
            ->where('is_active', true)
            ->with(['answers' => fn($q) => $q->orderBy('id')])
            ->inRandomOrder()
            ->limit($mapel->pivot->qty)
            ->get()
            ->toArray();

        $this->activeSubjectId    = $subjectId;
        $this->activeSubjectName  = $subject['name'];
        $this->activeSubjectIndex = $index;
        $this->collection         = [['id' => $subjectId, 'name' => $subject['name'], 'soals' => $soals]];
        $this->jawaban            = [];
        $this->totalSoal          = count($soals);
        $this->isSubmitted        = false;
        $this->finalScore         = 0;
        $this->finalCorrect       = 0;
        $this->finalTotal         = 0;
        $this->examResultId       = null;
        $this->step               = 'exam';
    }

    /**
     * Kembali ke halaman pilih mapel.
     */
    public function backToChoose(): void
    {
        $this->step = 'choose';
        $this->collection = [];
        $this->jawaban    = [];
    }

    /**
     * Submit jawaban untuk mapel aktif.
     */
    public function submit(): void
    {
        if (! $this->allAnswered) {
            $this->addError('submit', 'Harap jawab semua soal sebelum mengumpulkan.');
            return;
        }

        $correctCount   = 0;
        $totalQuestions = 0;
        $answerDataToSave = [];

        foreach ($this->collection as $pelajaran) {
            foreach ($pelajaran['soals'] as $soal) {
                $totalQuestions++;
                $answeredId = $this->jawaban[$soal['id']] ?? null;
                $isCorrect  = false;

                if ($answeredId) {
                    $isCorrect = Answer::where('id', $answeredId)
                        ->where('is_correct', true)
                        ->exists();
                    if ($isCorrect) $correctCount++;
                }

                $answerDataToSave[] = [
                    'question_id' => $soal['id'],
                    'answer_id'   => $answeredId,
                    'is_correct'  => $isCorrect,
                ];
            }
        }

        $score = $totalQuestions > 0
            ? round(($correctCount / $totalQuestions) * 100, 2)
            : 0;

        $examResult = ExamResult::create([
            'exam_id'         => $this->examId,
            'user_id'         => Auth::id(),
            'subject_id'      => $this->activeSubjectId,
            'score'           => $score,
            'correct_count'   => $correctCount,
            'total_questions' => $totalQuestions,
        ]);

        foreach ($answerDataToSave as $ans) {
            $examResult->answers()->create($ans);
        }

        // Tandai mapel sebagai selesai di subjectList
        $this->subjectList[$this->activeSubjectIndex]['done'] = true;

        $this->finalScore   = $score;
        $this->finalCorrect = $correctCount;
        $this->finalTotal   = $totalQuestions;
        $this->examResultId = $examResult->id;
        $this->isSubmitted  = true;
        $this->step         = 'result';
    }
}
