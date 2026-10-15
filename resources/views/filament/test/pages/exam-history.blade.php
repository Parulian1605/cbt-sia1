<x-filament-panels::page>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        /* ── Summary Stats Bar ── */
        .riwayat-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #1e2433 0%, #161b27 100%);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 1rem;
            padding: 1.1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.9rem;
            transition: transform 0.2s, border-color 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255,255,255,0.14);
        }

        .stat-icon {
            width: 40px; height: 40px;
            border-radius: 0.65rem;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon--blue  { background: rgba(59,130,246,0.18); }
        .stat-icon--green { background: rgba(34,197,94,0.18); }
        .stat-icon--red   { background: rgba(239,68,68,0.18); }
        .stat-icon--amber { background: rgba(245,158,11,0.18); }

        .stat-value {
            font-size: 1.4rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            margin-bottom: 0.15rem;
        }

        .stat-label {
            font-size: 0.73rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        /* ── Table Container ── */
        .riwayat-wrap {
            background: linear-gradient(135deg, #1a1f2e 0%, #141821 100%);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 1.25rem;
            overflow: hidden;
        }

        /* ── Table header ── */
        .riwayat-table {
            width: 100%;
            border-collapse: collapse;
        }

        .riwayat-table thead {
            background: rgba(255,255,255,0.03);
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

        .riwayat-table th {
            padding: 0.9rem 1.25rem;
            text-align: left;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #4b5563;
        }

        .riwayat-table th.col-center { text-align: center; }

        /* ── Table rows ── */
        .riwayat-table tbody tr {
            border-bottom: 1px solid rgba(255,255,255,0.04);
            transition: background 0.15s;
        }

        .riwayat-table tbody tr:last-child {
            border-bottom: none;
        }

        .riwayat-table tbody tr:hover {
            background: rgba(255,255,255,0.03);
        }

        .riwayat-table td {
            padding: 1rem 1.25rem;
            font-size: 0.88rem;
            vertical-align: middle;
            color: #d1d5db;
        }

        .riwayat-table td.col-center { text-align: center; }

        /* ── Row number ── */
        .row-num {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.78rem;
            font-weight: 700;
            color: #6b7280;
        }

        /* ── Exam title cell ── */
        .exam-title-main {
            font-weight: 700;
            color: #f9fafb;
            font-size: 0.92rem;
            margin-bottom: 0.15rem;
        }

        .exam-title-sub {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 500;
        }

        /* ── Score ── */
        .score-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px; height: 52px;
            border-radius: 50%;
            font-size: 1.05rem;
            font-weight: 800;
            flex-shrink: 0;
        }

        .score-wrap--pass {
            background: rgba(34,197,94,0.12);
            border: 2px solid rgba(34,197,94,0.3);
            color: #4ade80;
        }

        .score-wrap--fail {
            background: rgba(239,68,68,0.1);
            border: 2px solid rgba(239,68,68,0.25);
            color: #f87171;
        }

        /* ── Correct/total bar ── */
        .correct-bar-wrap {
            min-width: 100px;
        }

        .correct-text {
            font-weight: 600;
            color: #e5e7eb;
            font-size: 0.85rem;
            margin-bottom: 0.3rem;
        }

        .correct-bar-bg {
            height: 5px;
            border-radius: 9999px;
            background: rgba(255,255,255,0.07);
            overflow: hidden;
        }

        .correct-bar-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.6s ease;
        }

        .fill-pass { background: linear-gradient(90deg, #22c55e, #4ade80); }
        .fill-fail { background: linear-gradient(90deg, #ef4444, #f87171); }

        /* ── Status badge ── */
        .badge-lulus {
            display: inline-flex; align-items: center; gap: 0.3rem;
            padding: 0.3rem 0.8rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            background: rgba(34,197,94,0.12);
            color: #4ade80;
            border: 1px solid rgba(34,197,94,0.25);
            white-space: nowrap;
        }

        .badge-tidak-lulus {
            display: inline-flex; align-items: center; gap: 0.3rem;
            padding: 0.3rem 0.8rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            background: rgba(239,68,68,0.1);
            color: #f87171;
            border: 1px solid rgba(239,68,68,0.22);
            white-space: nowrap;
        }

        /* ── Date ── */
        .date-cell {
            font-size: 0.82rem;
            color: #6b7280;
            white-space: nowrap;
        }

        .date-cell strong {
            display: block;
            color: #d1d5db;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* ── Action button ── */
        .btn-detail {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.9rem;
            border-radius: 0.55rem;
            font-size: 0.8rem;
            font-weight: 700;
            text-decoration: none;
            background: rgba(59,130,246,0.12);
            color: #60a5fa;
            border: 1px solid rgba(59,130,246,0.25);
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-detail:hover {
            background: rgba(59,130,246,0.22);
            border-color: rgba(59,130,246,0.45);
            transform: translateY(-1px);
        }

        /* ── Empty state ── */
        .empty-state {
            padding: 5rem 2rem;
            text-align: center;
        }

        .empty-icon-wrap {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
        }

        .empty-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #e5e7eb;
            margin-bottom: 0.4rem;
        }

        .empty-sub {
            font-size: 0.85rem;
            color: #4b5563;
        }
    </style>

    @php
        $results = $this->getResults();
        $total   = $results->count();
        $lulus   = $results->filter(fn($r) => $r->score >= $r->exam->threshold)->count();
        $gagal   = $total - $lulus;
        $avgScore = $total > 0 ? round($results->avg('score')) : 0;
    @endphp

    {{-- ── Stats Bar ── --}}
    @if ($total > 0)
        <div class="riwayat-stats">
            <div class="stat-card">
                <div class="stat-icon stat-icon--blue">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-value">{{ $total }}</div>
                    <div class="stat-label">Total Ujian</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon--green">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-value">{{ $lulus }}</div>
                    <div class="stat-label">Lulus</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon--red">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-value">{{ $gagal }}</div>
                    <div class="stat-label">Tidak Lulus</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon--amber">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-value">{{ $avgScore }}</div>
                    <div class="stat-label">Rata-rata</div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Table ── --}}
    @if ($results->isEmpty())
        <div class="riwayat-wrap">
            <div class="empty-state">
                <div class="empty-icon-wrap">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#4b5563" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/>
                    </svg>
                </div>
                <div class="empty-title">Belum ada riwayat ujian</div>
                <div class="empty-sub">Selesaikan satu ujian untuk melihat hasilnya di sini.</div>
            </div>
        </div>
    @else
        <div class="riwayat-wrap">
            <table class="riwayat-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Ujian</th>
                        <th class="col-center">Nilai</th>
                        <th>Benar / Total</th>
                        <th class="col-center">Status</th>
                        <th>Dikerjakan Pada</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($results as $i => $result)
                        @php
                            $pass    = $result->score >= $result->exam->threshold;
                            $pct     = $result->total_questions > 0
                                        ? round(($result->correct_count / $result->total_questions) * 100)
                                        : 0;
                        @endphp
                        <tr>
                            {{-- # --}}
                            <td><span class="row-num">{{ $i + 1 }}</span></td>

                            {{-- Nama Ujian --}}
                            <td>
                                <div class="exam-title-main">{{ $result->exam->title }}</div>
                                @if ($result->subject)
                                    <div class="exam-title-sub">{{ $result->subject->name }}</div>
                                @endif
                            </td>

                            {{-- Nilai (lingkaran) --}}
                            <td class="col-center">
                                <span class="score-wrap {{ $pass ? 'score-wrap--pass' : 'score-wrap--fail' }}">
                                    {{ number_format($result->score, 0) }}
                                </span>
                            </td>

                            {{-- Benar / Total + bar --}}
                            <td>
                                <div class="correct-bar-wrap">
                                    <div class="correct-text">
                                        {{ $result->correct_count }} / {{ $result->total_questions }} soal
                                    </div>
                                    <div class="correct-bar-bg">
                                        <div class="correct-bar-fill {{ $pass ? 'fill-pass' : 'fill-fail' }}"
                                             style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            </td>

                            {{-- Status badge --}}
                            <td class="col-center">
                                @if ($pass)
                                    <span class="badge-lulus">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        Lulus
                                    </span>
                                @else
                                    <span class="badge-tidak-lulus">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        Tidak Lulus
                                    </span>
                                @endif
                            </td>

                            {{-- Tanggal --}}
                            <td>
                                <div class="date-cell">
                                    <strong>{{ $result->created_at->translatedFormat('d M Y') }}</strong>
                                    {{ $result->created_at->format('H:i') }} WIB
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td>
                                <a href="{{ route('filament.test.pages.exam-result-detail', ['id' => $result->id]) }}"
                                   class="btn-detail">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-filament-panels::page>
