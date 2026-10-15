<x-filament-panels::page>
<style>
    /* ── Back button ── */
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        color: #6b7280;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 0.45rem 0.9rem;
        border-radius: 0.55rem;
        border: 1px solid rgba(255,255,255,0.07);
        background: rgba(255,255,255,0.04);
        transition: all 0.2s;
        margin-bottom: 1.75rem;
        width: fit-content;
    }
    .back-btn:hover {
        color: #e5e7eb;
        background: rgba(255,255,255,0.08);
        border-color: rgba(255,255,255,0.12);
    }

    /* ── Score summary banner ── */
    .score-banner {
        background: linear-gradient(135deg, #1a1f2e 0%, #161b27 100%);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 1.25rem;
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        gap: 2rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .score-circle {
        width: 80px; height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        font-weight: 900;
        flex-shrink: 0;
    }
    .score-circle--pass {
        background: radial-gradient(circle, rgba(34,197,94,0.25) 0%, rgba(34,197,94,0.08) 100%);
        border: 3px solid rgba(34,197,94,0.45);
        color: #4ade80;
        box-shadow: 0 0 24px rgba(34,197,94,0.2);
    }
    .score-circle--fail {
        background: radial-gradient(circle, rgba(239,68,68,0.2) 0%, rgba(239,68,68,0.07) 100%);
        border: 3px solid rgba(239,68,68,0.4);
        color: #f87171;
        box-shadow: 0 0 24px rgba(239,68,68,0.15);
    }

    .banner-info { flex: 1; min-width: 180px; }

    .banner-exam-name {
        font-size: 1.15rem;
        font-weight: 800;
        color: #f9fafb;
        margin-bottom: 0.2rem;
    }

    .banner-subject-name {
        font-size: 0.82rem;
        color: #6b7280;
        margin-bottom: 0.6rem;
        font-weight: 500;
    }

    .banner-stats {
        display: flex;
        gap: 1.25rem;
        flex-wrap: wrap;
    }

    .banner-stat {
        font-size: 0.8rem;
        color: #6b7280;
        font-weight: 600;
    }

    .banner-stat span {
        color: #d1d5db;
        font-weight: 700;
    }

    .banner-status {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 1rem;
        border-radius: 9999px;
        font-size: 0.82rem;
        font-weight: 700;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .banner-status--pass {
        background: rgba(34,197,94,0.12);
        color: #4ade80;
        border: 1px solid rgba(34,197,94,0.3);
    }
    .banner-status--fail {
        background: rgba(239,68,68,0.1);
        color: #f87171;
        border: 1px solid rgba(239,68,68,0.25);
    }

    /* ── Progress bar in banner ── */
    .banner-progress {
        flex: 1;
        min-width: 180px;
    }
    .banner-progress-label {
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 600;
        margin-bottom: 0.35rem;
        display: flex;
        justify-content: space-between;
    }
    .banner-bar-bg {
        height: 7px;
        background: rgba(255,255,255,0.06);
        border-radius: 9999px;
        overflow: hidden;
    }
    .banner-bar-fill {
        height: 100%;
        border-radius: 9999px;
        transition: width 0.8s ease;
    }
    .fill-pass { background: linear-gradient(90deg, #16a34a, #4ade80); }
    .fill-fail { background: linear-gradient(90deg, #dc2626, #f87171); }

    /* ── Section divider ── */
    .section-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #4b5563;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: rgba(255,255,255,0.05);
    }

    /* ── Question card ── */
    .question-card {
        background: linear-gradient(135deg, #1a1f2e 0%, #161b27 100%);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        transition: border-color 0.2s;
    }
    .question-card:hover {
        border-color: rgba(255,255,255,0.1);
    }

    .question-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.9rem;
    }

    .question-number {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: #4b5563;
        background: rgba(255,255,255,0.05);
        padding: 0.25rem 0.65rem;
        border-radius: 0.35rem;
    }

    .badge-correct {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.73rem;
        font-weight: 700;
        background: rgba(34,197,94,0.15);
        color: #4ade80;
        border: 1px solid rgba(34,197,94,0.3);
    }
    .badge-wrong {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.73rem;
        font-weight: 700;
        background: rgba(239,68,68,0.12);
        color: #f87171;
        border: 1px solid rgba(239,68,68,0.25);
    }

    .question-payload {
        font-size: 0.95rem;
        color: #e5e7eb;
        line-height: 1.7;
        margin-bottom: 1.25rem;
    }

    /* ── Answer options ── */
    .answer-list {
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
    }

    .answer-item {
        padding: 0.75rem 1rem;
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 0.65rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.88rem;
        color: #9ca3af;
        background: rgba(255,255,255,0.02);
        transition: all 0.15s;
    }

    .answer-item.correct-answer {
        background: rgba(34,197,94,0.08);
        border-color: rgba(34,197,94,0.35);
        color: #d1fae5;
    }
    .answer-item.wrong-answer {
        background: rgba(239,68,68,0.08);
        border-color: rgba(239,68,68,0.3);
        color: #fecaca;
    }

    .answer-letter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px; height: 24px;
        border-radius: 50%;
        font-size: 0.72rem;
        font-weight: 800;
        background: rgba(255,255,255,0.06);
        color: #6b7280;
        flex-shrink: 0;
        margin-right: 0.5rem;
    }
    .correct-answer .answer-letter {
        background: rgba(34,197,94,0.2);
        color: #4ade80;
    }
    .wrong-answer .answer-letter {
        background: rgba(239,68,68,0.18);
        color: #f87171;
    }

    .answer-text { flex: 1; font-weight: 500; }

    .answer-tag {
        font-size: 0.7rem;
        font-weight: 700;
        white-space: nowrap;
        padding: 0.2rem 0.6rem;
        border-radius: 0.35rem;
        flex-shrink: 0;
    }
    .correct-answer .answer-tag {
        background: rgba(34,197,94,0.15);
        color: #4ade80;
    }
    .wrong-answer .answer-tag {
        background: rgba(239,68,68,0.15);
        color: #f87171;
    }
</style>

@php
    $result    = $this->result;
    $exam      = $result->exam;
    $pass      = $result->score >= $exam->threshold;
    $totalQ    = $result->total_questions;
    $correct   = $result->correct_count;
    $pct       = $totalQ > 0 ? round(($correct / $totalQ) * 100) : 0;
@endphp

{{-- Back --}}
<a href="{{ \App\Filament\Test\Pages\ExamHistory::getUrl() }}" class="back-btn">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="15 18 9 12 15 6"/>
    </svg>
    Kembali ke Riwayat Ujian
</a>

{{-- Score Banner --}}
<div class="score-banner">
    {{-- Score circle --}}
    <div class="score-circle {{ $pass ? 'score-circle--pass' : 'score-circle--fail' }}">
        {{ number_format($result->score, 0) }}
    </div>

    {{-- Exam info --}}
    <div class="banner-info">
        <div class="banner-exam-name">{{ $exam->title }}</div>
        @if ($result->subject)
            <div class="banner-subject-name">{{ $result->subject->name }}</div>
        @endif
        <div class="banner-stats">
            <div class="banner-stat">Benar: <span>{{ $correct }}</span></div>
            <div class="banner-stat">Total Soal: <span>{{ $totalQ }}</span></div>
            <div class="banner-stat">KKM: <span>{{ number_format($exam->threshold, 0) }}</span></div>
        </div>
    </div>

    {{-- Progress bar --}}
    <div class="banner-progress">
        <div class="banner-progress-label">
            <span>Akurasi</span>
            <span>{{ $pct }}%</span>
        </div>
        <div class="banner-bar-bg">
            <div class="banner-bar-fill {{ $pass ? 'fill-pass' : 'fill-fail' }}"
                 style="width:{{ $pct }}%"></div>
        </div>
    </div>

    {{-- Status badge --}}
    <div class="banner-status {{ $pass ? 'banner-status--pass' : 'banner-status--fail' }}">
        @if ($pass)
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            LULUS
        @else
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            TIDAK LULUS
        @endif
    </div>
</div>

{{-- Questions --}}
<div class="section-label">Pembahasan Soal</div>

@foreach ($result->answers as $index => $answer)
    @php
        $allOptions = \App\Models\Answer::where('question_id', $answer->question_id)->orderBy('id')->get();
    @endphp
    <div class="question-card">
        <div class="question-header">
            <div class="question-number">Soal {{ $index + 1 }}</div>
            @if ($answer->is_correct)
                <div class="badge-correct">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Benar
                </div>
            @else
                <div class="badge-wrong">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Salah
                </div>
            @endif
        </div>

        <div class="question-payload">{!! $answer->question->payload !!}</div>

        <div class="answer-list">
            @foreach ($allOptions as $opt)
                @php
                    $isUserChoice    = ($answer->answer_id === $opt->id);
                    $isActualCorrect = $opt->is_correct;

                    $itemClass = '';
                    $tag       = '';

                    if ($isUserChoice && $isActualCorrect) {
                        $itemClass = 'correct-answer';
                        $tag = '✓ Jawaban kamu (Benar)';
                    } elseif ($isUserChoice && !$isActualCorrect) {
                        $itemClass = 'wrong-answer';
                        $tag = '✗ Jawaban kamu (Salah)';
                    } elseif (!$isUserChoice && $isActualCorrect) {
                        $itemClass = 'correct-answer';
                        $tag = '✓ Kunci Jawaban';
                    }
                @endphp

                <div class="answer-item {{ $itemClass }}">
                    <div style="display:flex; align-items:center; flex:1; min-width:0;">
                        <span class="answer-letter">{{ chr(65 + $loop->index) }}</span>
                        <span class="answer-text">{{ $opt->text }}</span>
                    </div>
                    @if ($tag)
                        <span class="answer-tag">{{ $tag }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endforeach

</x-filament-panels::page>
