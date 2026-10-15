<x-filament-panels::page>
    <style>
        /* ── Base card ── */
        .exam-header-card {
            background: var(--gray-900, #111827);
            border: 1px solid var(--gray-700, #374151);
            border-radius: 1rem;
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .exam-header-left {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .exam-icon-wrap {
            background: rgba(59, 130, 246, 0.15);
            border-radius: 0.75rem;
            padding: 0.75rem;
            flex-shrink: 0;
        }

        .exam-step-label {
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--gray-400, #9CA3AF);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.2rem;
        }

        .exam-header-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 0.3rem;
        }

        .exam-header-desc {
            font-size: 0.85rem;
            color: var(--gray-400, #9CA3AF);
            margin-bottom: 0.6rem;
        }

        .exam-header-meta {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .meta-badge {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 0.4rem;
            padding: 0.25rem 0.7rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--gray-300, #D1D5DB);
        }

        .btn-back-small {
            background: transparent;
            border: 1px solid var(--gray-600, #4B5563);
            color: var(--gray-300, #D1D5DB);
            border-radius: 0.5rem;
            padding: 0.5rem 1.1rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .btn-back-small:hover {
            background: var(--gray-700, #374151);
            border-color: var(--gray-500, #6B7280);
        }

        /* ── Subject card (choose step) ── */
        .subject-cards-grid {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .subject-card {
            background: var(--gray-900, #111827);
            border: 1px solid var(--gray-700, #374151);
            border-radius: 0.85rem;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            transition: border-color 0.2s;
        }

        .subject-card:not(.subject-card--done):hover {
            border-color: rgb(59, 130, 246);
        }

        .subject-card--done {
            opacity: 0.6;
        }

        .subject-card-left {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .subject-status-badge {
            display: inline-block;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
        }

        .badge-done {
            background: rgba(22, 163, 74, 0.15);
            color: #4ade80;
            border: 1px solid rgba(22, 163, 74, 0.3);
        }

        .badge-pending {
            background: rgba(59, 130, 246, 0.12);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.25);
        }

        .subject-card-name {
            font-size: 1.05rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.25rem;
        }

        .subject-card-desc {
            font-size: 0.8rem;
            color: var(--gray-400, #9CA3AF);
        }

        .btn-start-subject {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: #fff;
            border: none;
            border-radius: 0.55rem;
            padding: 0.6rem 1.4rem;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .btn-start-subject:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(59, 130, 246, 0.4);
        }

        /* ── Exam (question) step ── */
        .exam-subject-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            padding: 0.5rem 1rem;
            background: rgba(59, 130, 246, 0.1);
            border-left: 4px solid rgb(59, 130, 246);
            border-radius: 0.25rem;
            color: #fff;
        }

        .question-card {
            background: var(--gray-800, #1f2937);
            border: 1px solid var(--gray-700, #374151);
            border-radius: 0.75rem;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .question-number {
            font-size: 0.8rem;
            font-weight: 700;
            color: rgb(96, 165, 250);
            margin-bottom: 0.35rem;
        }

        .question-payload {
            font-size: 0.97rem;
            margin-bottom: 1rem;
            line-height: 1.6;
            color: var(--gray-100, #F3F4F6);
        }

        .answer-options {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .answer-label {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.5rem 0.85rem;
            border-radius: 0.5rem;
            border: 1px solid var(--gray-600, #4B5563);
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
            font-size: 0.92rem;
            color: var(--gray-200, #E5E7EB);
        }

        .answer-label:hover {
            background: rgba(59, 130, 246, 0.1);
            border-color: rgb(96, 165, 250);
        }

        .answer-label input[type="radio"] {
            accent-color: rgb(59, 130, 246);
            width: 1rem;
            height: 1rem;
            flex-shrink: 0;
        }

        /* ── Submit bar ── */
        .submit-wrapper {
            position: sticky;
            bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1rem;
            padding: 0.5rem;
        }

        .submit-progress {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.85rem;
            color: var(--gray-400, #9CA3AF);
            background: var(--gray-800, #1f2937);
            border: 1px solid var(--gray-700, #374151);
            border-radius: 0.5rem;
            padding: 0.45rem 0.9rem;
            font-weight: 600;
        }

        .submit-progress.all-done {
            color: #4ade80;
            background: rgba(22, 163, 74, 0.08);
            border-color: rgba(22, 163, 74, 0.3);
        }

        .progress-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--gray-500, #6B7280);
            flex-shrink: 0;
        }

        .submit-progress.all-done .progress-dot { background: #4ade80; }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            padding: 0.8rem 2rem;
            border-radius: 0.6rem;
            border: 2px solid #15803d;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(22, 163, 74, 0.35);
            transition: all 0.2s ease;
            outline: none;
        }

        .btn-submit:hover:not(:disabled) {
            background: linear-gradient(135deg, #15803d 0%, #166534 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(22, 163, 74, 0.5);
        }

        .btn-submit:disabled {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            border-color: #4b5563;
            box-shadow: none;
            cursor: not-allowed;
            opacity: 0.65;
        }

        /* ── Result step ── */
        .result-box {
            text-align: center;
            padding: 3rem 2rem;
            background: var(--gray-900, #111827);
            border: 1px solid var(--gray-700, #374151);
            border-radius: 1rem;
            max-width: 620px;
            margin: 1.5rem auto;
        }

        .result-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--gray-400, #9CA3AF);
            margin-bottom: 0.4rem;
        }

        .result-subject-name {
            font-size: 1.3rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 1rem;
        }

        .result-score {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 1rem;
            color: #fff;
        }

        .result-stats {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--gray-300, #D1D5DB);
        }

        .result-status {
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 1rem;
        }

        .status-pass { color: #4ade80; }
        .status-fail { color: #f87171; }

        .result-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-detail {
            background: rgb(59, 130, 246);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .btn-detail:hover { background: rgb(37, 99, 235); }

        .btn-back {
            background: var(--gray-700, #374151);
            color: var(--gray-100, #F3F4F6);
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
        }

        .btn-back:hover { background: var(--gray-600, #4B5563); }

        /* remaining progress chips */
        .subjects-done-info {
            font-size: 0.85rem;
            color: var(--gray-400);
            margin-top: 0.5rem;
        }
    </style>

    {{-- ══════════════════════════════════
         STEP: choose (pilih mapel)
    ══════════════════════════════════ --}}
    @if ($step === 'choose')
        <div class="exam-header-card">
            <div class="exam-header-left">
                <div class="exam-icon-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none"
                         viewBox="0 0 24 24" stroke="rgb(96,165,250)" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
                <div>
                    <div class="exam-step-label">
                        LANGKAH 1 DARI {{ count($subjectList) }}
                    </div>
                    <div class="exam-header-title">{{ $examTitle }}</div>
                    <div class="exam-header-desc">
                        Pilih satu mata pelajaran untuk dikerjakan. Setiap mapel hanya dapat dikerjakan <strong>1 kali</strong>.
                    </div>
                    <div class="exam-header-meta">
                        <span class="meta-badge">Durasi: {{ $examDuration }} menit per mapel</span>
                        <span class="meta-badge">KKM: {{ number_format($threshold, 2) }}</span>
                        <span class="meta-badge">{{ $this->doneCount }}/{{ count($subjectList) }} selesai</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="subject-cards-grid">
            @foreach ($subjectList as $subject)
                <div class="subject-card {{ $subject['done'] ? 'subject-card--done' : '' }}">
                    <div class="subject-card-left">
                        <div>
                            @if ($subject['done'])
                                <span class="subject-status-badge badge-done">✓ Selesai</span>
                            @else
                                <span class="subject-status-badge badge-pending">Belum dikerjakan</span>
                            @endif
                            <div class="subject-card-name">{{ $subject['name'] }}</div>
                            <div class="subject-card-desc">
                                {{ $subject['qty'] }} soal &bull; Sekali dimulai, timer berjalan dan tidak bisa diulang.
                            </div>
                        </div>
                    </div>

                    @if (!$subject['done'])
                        <button
                            class="btn-start-subject"
                            wire:click="chooseSubject({{ $subject['id'] }})"
                            wire:loading.attr="disabled"
                        >
                            Mulai {{ $subject['name'] }}
                        </button>
                    @endif
                </div>
            @endforeach
        </div>

        @if ($this->allSubjectsDone)
            <div style="text-align:center; margin-top:2rem;">
                <a href="{{ \App\Filament\Test\Resources\Exams\ExamResource::getUrl() }}" class="btn-back">
                    ← Kembali ke Sesi Ujian
                </a>
            </div>
        @endif
    @endif

    {{-- ══════════════════════════════════
         STEP: exam (mengerjakan soal)
    ══════════════════════════════════ --}}
    @if ($step === 'exam')
        <div class="exam-header-card">
            <div class="exam-header-left">
                <div class="exam-icon-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none"
                         viewBox="0 0 24 24" stroke="rgb(96,165,250)" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
                <div>
                    <div class="exam-step-label">
                        LANGKAH {{ $this->doneCount + 1 }} DARI {{ count($subjectList) }}
                    </div>
                    <div class="exam-header-title">{{ $activeSubjectName }}</div>
                    <div class="exam-header-desc">
                        Kerjakan semua soal dengan teliti. Jawaban tidak dapat diubah setelah dikumpulkan.
                    </div>
                    <div class="exam-header-meta">
                        <span class="meta-badge">{{ $totalSoal }} soal</span>
                        <span class="meta-badge">KKM: {{ number_format($threshold, 2) }}</span>
                    </div>
                </div>
            </div>
            <button class="btn-back-small" wire:click="backToChoose">Kembali</button>
        </div>

        <form wire:submit.prevent="submit">
            @php $no = 1; @endphp
            @foreach ($collection as $pelajarans)
                <div class="exam-subject-title">{{ $pelajarans['name'] }}</div>

                @foreach ($pelajarans['soals'] as $pertanyaan)
                    <div class="question-card">
                        <div class="question-number">Soal No. {{ $no++ }}</div>
                        <div class="question-payload">{!! $pertanyaan['payload'] !!}</div>

                        <div class="answer-options">
                            @foreach ($pertanyaan['answers'] as $pilihanJawaban)
                                <label class="answer-label">
                                    <input
                                        type="radio"
                                        wire:model.live="jawaban.{{ $pertanyaan['id'] }}"
                                        value="{{ $pilihanJawaban['id'] }}"
                                    >
                                    <span style="font-weight:600; min-width:1.2rem;">{{ chr(65 + $loop->index) }}.</span>
                                    {{ $pilihanJawaban['text'] }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endforeach

            @error('submit')
                <div style="color:#f87171; margin-bottom:1rem; font-size:0.9rem;">⚠ {{ $message }}</div>
            @enderror

            <div class="submit-wrapper">
                <div class="submit-progress {{ $this->allAnswered ? 'all-done' : '' }}">
                    <span class="progress-dot"></span>
                    @if ($this->allAnswered)
                        Semua soal sudah dijawab ✓
                    @else
                        {{ $this->totalAnswered }} / {{ $totalSoal }} soal dijawab
                    @endif
                </div>

                <button
                    type="submit"
                    class="btn-submit"
                    @if (!$this->allAnswered) disabled @endif
                    title="{{ !$this->allAnswered ? 'Jawab semua soal terlebih dahulu ('.$this->totalAnswered.'/'.$totalSoal.')' : 'Kumpulkan ujian' }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Selesai &amp; Kumpulkan
                </button>
            </div>
        </form>
    @endif

    {{-- ══════════════════════════════════
         STEP: result (hasil per-mapel)
    ══════════════════════════════════ --}}
    @if ($step === 'result')
        <div class="result-box">
            <div class="result-title">Hasil Ujian</div>
            <div class="result-subject-name">{{ $activeSubjectName }}</div>
            <div class="result-score">{{ number_format($finalScore, 0) }}</div>

            <div class="result-stats">
                Benar {{ $finalCorrect }} dari {{ $finalTotal }} soal
            </div>

            @if ($finalScore >= $threshold)
                <div class="result-status status-pass">
                    ✓ LULUS (nilai minimal {{ number_format($threshold, 0) }})
                </div>
            @else
                <div class="result-status status-fail">
                    ✗ BELUM LULUS (nilai minimal {{ number_format($threshold, 0) }})
                </div>
            @endif

            <div class="subjects-done-info">
                {{ $this->doneCount }} dari {{ count($subjectList) }} mapel selesai
            </div>

            <div class="result-actions" style="margin-top:1.5rem;">
                @if (!$this->allSubjectsDone)
                    <button class="btn-back" wire:click="backToChoose">
                        ← Pilih Mapel Berikutnya
                    </button>
                @else
                    <a href="{{ \App\Filament\Test\Resources\Exams\ExamResource::getUrl() }}" class="btn-back">
                        ← Kembali ke Sesi Ujian
                    </a>
                @endif

                @if ($examResultId)
                    <a href="{{ route('filament.test.pages.exam-result-detail', ['id' => $examResultId]) }}"
                       class="btn-detail">
                        Lihat Pembahasan
                    </a>
                @endif
            </div>
        </div>
    @endif

</x-filament-panels::page>
