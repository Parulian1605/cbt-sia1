<x-filament-panels::page>
    <style>
        /* ── Stats Cards ─────────────────────────────────────── */
        .grade-stats {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        @media (max-width: 1100px) { .grade-stats { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 640px)  { .grade-stats { grid-template-columns: repeat(2, 1fr); } }

        .stat-card {
            background: var(--surface-bg, white);
            border: 1px solid var(--gray-200);
            border-radius: 0.875rem;
            padding: 1.1rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }
        .dark .stat-card {
            background: rgba(255,255,255,0.03);
            border-color: var(--gray-700);
        }
        .stat-card-icon {
            width: 36px;
            height: 36px;
            border-radius: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.35rem;
        }
        .stat-card-num {
            font-size: 1.7rem;
            font-weight: 800;
            line-height: 1;
        }
        .stat-card-lbl {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-400);
            font-weight: 600;
        }

        /* ── Exam Info Banner ────────────────────────────────── */
        .exam-banner {
            background: linear-gradient(135deg, rgb(var(--primary-600)), rgb(var(--primary-400)));
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }
        .exam-banner-icon {
            width: 52px;
            height: 52px;
            border-radius: 0.75rem;
            background: rgba(255,255,255,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .exam-banner-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: white;
        }
        .exam-banner-meta {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.8);
            margin-top: 0.2rem;
            display: flex;
            gap: 1.25rem;
            flex-wrap: wrap;
        }
        .exam-banner-meta span {
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        /* ── Table Card ──────────────────────────────────────── */
        .grade-card {
            background: var(--surface-bg, white);
            border: 1px solid var(--gray-200);
            border-radius: 1rem;
            overflow: hidden;
        }
        .dark .grade-card {
            background: rgba(255,255,255,0.03);
            border-color: var(--gray-700);
        }
        .grade-card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .dark .grade-card-header { border-color: var(--gray-700); }
        .grade-card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--gray-800);
        }
        .dark .grade-card-title { color: var(--gray-100); }

        /* ── Grade Table ─────────────────────────────────────── */
        .grade-table {
            width: 100%;
            border-collapse: collapse;
        }
        .grade-table th {
            background: rgba(var(--primary-500), 0.06);
            padding: 0.7rem 1rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgb(var(--primary-600));
            border-bottom: 1px solid var(--gray-200);
        }
        .dark .grade-table th { border-color: var(--gray-700); }
        .grade-table td {
            padding: 0.8rem 1rem;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--gray-100);
            color: var(--gray-700);
        }
        .dark .grade-table td {
            border-color: var(--gray-800);
            color: var(--gray-300);
        }
        .grade-table tr:last-child td { border-bottom: none; }
        .grade-table tr:hover td {
            background: rgba(var(--primary-500), 0.03);
        }

        /* ── Score Bar ───────────────────────────────────────── */
        .score-bar-wrap {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .score-bar-bg {
            flex: 1;
            height: 6px;
            border-radius: 9999px;
            background: var(--gray-100);
            overflow: hidden;
        }
        .dark .score-bar-bg { background: var(--gray-700); }
        .score-bar-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.4s ease;
        }
        .score-bar-num {
            font-size: 0.9rem;
            font-weight: 700;
            min-width: 40px;
            text-align: right;
        }

        /* ── Badges ──────────────────────────────────────────── */
        .badge-lulus {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            background: rgba(34,197,94,0.12);
            color: #16a34a;
        }
        .badge-tidak-lulus {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            background: rgba(239,68,68,0.12);
            color: #dc2626;
        }

        /* ── Rank Badge ──────────────────────────────────────── */
        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 800;
        }
        .rank-1 { background: rgba(234,179,8,0.15); color: #a16207; }
        .rank-2 { background: rgba(156,163,175,0.18); color: #4b5563; }
        .rank-3 { background: rgba(180,83,9,0.12); color: #92400e; }
        .rank-other { background: var(--gray-100); color: var(--gray-500); }
        .dark .rank-other { background: var(--gray-800); color: var(--gray-400); }

        /* ── Empty State ─────────────────────────────────────── */
        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
            color: var(--gray-400);
        }
        .empty-state svg {
            width: 3rem;
            height: 3rem;
            margin: 0 auto 1rem;
            opacity: 0.4;
        }

        /* ── Back Button ─────────────────────────────────────── */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid var(--gray-200);
            color: var(--gray-600);
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
        }
        .dark .btn-back {
            border-color: var(--gray-600);
            color: var(--gray-300);
        }
        .btn-back:hover {
            background: var(--gray-50);
            color: var(--gray-900);
        }
        .dark .btn-back:hover {
            background: rgba(255,255,255,0.06);
            color: white;
        }

        /* ── Print Button ────────────────────────────────────── */
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: none;
            background: linear-gradient(135deg, rgb(var(--primary-600)), rgb(var(--primary-500)));
            color: white;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.15s;
            text-decoration: none;
        }
        .btn-print:hover { opacity: 0.88; }
    </style>

    @php
        $exam    = $this->record;
        $results = $this->getResults();
        $stats   = $this->getStats();
    @endphp

    {{-- Exam Info Banner --}}
    <div class="exam-banner">
        <div class="exam-banner-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" style="width:26px;height:26px">
                <path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.949 49.949 0 00-9.902 3.912l-.003.002-.34.18a.75.75 0 01-.707 0A50.009 50.009 0 007.5 12.174v-.224c0-.131.067-.248.172-.311a54.614 54.614 0 014.653-2.52.75.75 0 00-.65-1.352 56.129 56.129 0 00-4.78 2.589 1.858 1.858 0 00-.859 1.228 49.803 49.803 0 00-4.634-1.527.75.75 0 01-.231-1.337A60.653 60.653 0 0111.7 2.805z"/>
                <path d="M13.06 15.473a48.45 48.45 0 017.666-3.282c.134 1.414.22 2.843.255 4.285a.75.75 0 01-.46.71 47.878 47.878 0 00-8.105 4.342.75.75 0 01-.832 0 47.877 47.877 0 00-8.104-4.342.75.75 0 01-.461-.71c.035-1.442.121-2.87.255-4.286A48.4 48.4 0 016 13.18v1.27a1.5 1.5 0 00-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.661a6.729 6.729 0 00.551-1.608 1.5 1.5 0 00.14-2.67v-.645a48.549 48.549 0 013.44 1.668 2.25 2.25 0 002.12 0z"/>
            </svg>
        </div>
        <div>
            <div class="exam-banner-title">{{ $exam->title }}</div>
            <div class="exam-banner-meta">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:13px;height:13px;opacity:.7">
                        <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2z" clip-rule="evenodd"/>
                    </svg>
                    {{ $exam->started_at?->translatedFormat('d F Y, H:i') ?? '—' }}
                </span>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:13px;height:13px;opacity:.7">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                    </svg>
                    KKM: {{ number_format((float)$exam->threshold, 0) }}
                </span>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:13px;height:13px;opacity:.7">
                        <path d="M7 8a3 3 0 100-6 3 3 0 000 6zM14.5 9a2.5 2.5 0 100-5 2.5 2.5 0 000 5zM1.615 16.428a1.224 1.224 0 01-.569-1.175 6.002 6.002 0 0111.908 0c.058.467-.172.92-.57 1.174A9.953 9.953 0 017 17a9.953 9.953 0 01-5.385-1.572zM14.5 16h-.106c.07-.297.088-.611.048-.933a7.47 7.47 0 00-1.588-3.755 4.502 4.502 0 015.874 2.853.818.818 0 01-.36.85A7.683 7.683 0 0114.5 16z"/>
                    </svg>
                    {{ $stats['total'] }} Peserta
                </span>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grade-stats">
        <div class="stat-card">
            <div class="stat-card-icon" style="background:rgba(var(--primary-500),0.1);color:rgb(var(--primary-600))">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:20px;height:20px">
                    <path d="M7 8a3 3 0 100-6 3 3 0 000 6zM14.5 9a2.5 2.5 0 100-5 2.5 2.5 0 000 5zM1.615 16.428a1.224 1.224 0 01-.569-1.175 6.002 6.002 0 0111.908 0c.058.467-.172.92-.57 1.174A9.953 9.953 0 017 17a9.953 9.953 0 01-5.385-1.572z"/>
                </svg>
            </div>
            <div class="stat-card-num" style="color:rgb(var(--primary-600))">{{ $stats['total'] }}</div>
            <div class="stat-card-lbl">Total Peserta</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:rgba(34,197,94,0.1);color:#16a34a">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:20px;height:20px">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="stat-card-num" style="color:#16a34a">{{ $stats['passed'] }}</div>
            <div class="stat-card-lbl">Lulus</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:rgba(239,68,68,0.1);color:#dc2626">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:20px;height:20px">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="stat-card-num" style="color:#dc2626">{{ $stats['failed'] }}</div>
            <div class="stat-card-lbl">Tidak Lulus</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:rgba(251,191,36,0.12);color:#d97706">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:20px;height:20px">
                    <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="stat-card-num" style="color:#d97706">{{ $stats['avg'] ?? '—' }}</div>
            <div class="stat-card-lbl">Rata-rata</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:rgba(16,185,129,0.1);color:#059669">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:20px;height:20px">
                    <path fill-rule="evenodd" d="M12.577 4.878a.75.75 0 01.919-.53l4.78 1.281a.75.75 0 01.531.919l-1.281 4.78a.75.75 0 01-1.449-.387l.81-3.022a19.407 19.407 0 00-5.594 5.203.75.75 0 01-1.139.093L7 10.06l-4.72 4.72a.75.75 0 01-1.06-1.061l5.25-5.25a.75.75 0 011.06 0l3.074 3.073a20.923 20.923 0 015.545-4.931l-3.042-.815a.75.75 0 01-.53-.918z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="stat-card-num" style="color:#059669">{{ $stats['highest'] ? number_format((float)$stats['highest'], 0) : '—' }}</div>
            <div class="stat-card-lbl">Nilai Tertinggi</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background:rgba(239,68,68,0.08);color:#dc2626">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:20px;height:20px">
                    <path fill-rule="evenodd" d="M1.22 5.222a.75.75 0 011.06 0L7 9.942l3.768-3.769a.75.75 0 011.113.058 20.908 20.908 0 015.545 4.931l3.042-.815a.75.75 0 01.388 1.449l-4.781 1.28a.75.75 0 01-.919-.53l-1.28-4.78a.75.75 0 011.448-.387l.81 3.022a19.407 19.407 0 00-5.594-5.203l-3.147 3.147a.75.75 0 01-1.06 0L1.22 6.282a.75.75 0 010-1.06z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="stat-card-num" style="color:#dc2626">{{ $stats['lowest'] ? number_format((float)$stats['lowest'], 0) : '—' }}</div>
            <div class="stat-card-lbl">Nilai Terendah</div>
        </div>
    </div>

    {{-- Results Table --}}
    <div class="grade-card">
        <div class="grade-card-header">
            <div class="grade-card-title">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;display:inline;vertical-align:middle;margin-right:0.3rem;color:rgb(var(--primary-500))">
                    <path fill-rule="evenodd" d="M6 4.75A.75.75 0 016.75 4h10.5a.75.75 0 010 1.5H6.75A.75.75 0 016 4.75zM6 10a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H6.75A.75.75 0 016 10zm0 5.25a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H6.75a.75.75 0 01-.75-.75zM1.99 4.75a1 1 0 011-1H3a1 1 0 010 2h-.01a1 1 0 01-1-1zM1.99 15.25a1 1 0 011-1H3a1 1 0 010 2h-.01a1 1 0 01-1-1zM1.99 10a1 1 0 011-1H3a1 1 0 010 2h-.01a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
                Daftar Nilai Siswa
                <span style="font-size:0.75rem;font-weight:500;color:var(--gray-400);margin-left:0.5rem;">{{ $results->count() }} siswa</span>
            </div>
            <a href="{{ \App\Filament\Resources\StudentGrades\StudentGradesResource::getUrl() }}" class="btn-back">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:15px;height:15px">
                    <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd"/>
                </svg>
                Kembali
            </a>
        </div>

        @if($results->isEmpty())
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                </svg>
                <p style="font-weight:600;color:var(--gray-500);margin:0;">Belum ada siswa yang mengerjakan ujian ini</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="grade-table">
                    <thead>
                        <tr>
                            <th style="width:50px">Rank</th>
                            <th>Nama Siswa</th>
                            <th>NIS</th>
                            <th style="text-align:center">Benar</th>
                            <th style="text-align:center">Total Soal</th>
                            <th style="min-width:200px">Nilai</th>
                            <th>Status</th>
                            <th>Waktu Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results as $i => $result)
                            @php
                                $score     = (float) $result->score;
                                $threshold = (float) $exam->threshold;
                                $passed    = $score >= $threshold;
                                $rank      = $i + 1;

                                // Bar color
                                $barColor = $passed
                                    ? 'linear-gradient(90deg,#22c55e,#16a34a)'
                                    : 'linear-gradient(90deg,#f97316,#dc2626)';

                                $rankClass = match($rank) {
                                    1 => 'rank-1',
                                    2 => 'rank-2',
                                    3 => 'rank-3',
                                    default => 'rank-other',
                                };

                                $studentName = $result->user?->student?->name ?? $result->user?->name ?? '—';
                                $nis = $result->user?->student?->nis ?? '—';
                            @endphp
                            <tr>
                                <td>
                                    <span class="rank-badge {{ $rankClass }}">
                                        @if($rank <= 3)🏆@else{{ $rank }}@endif
                                    </span>
                                </td>
                                <td style="font-weight:600;color:var(--gray-900)" class="dark:text-gray-100">
                                    {{ $studentName }}
                                </td>
                                <td style="font-family:monospace;font-size:0.85rem;">{{ $nis }}</td>
                                <td style="text-align:center;font-weight:700;color:#16a34a">
                                    {{ $result->correct_count }}
                                </td>
                                <td style="text-align:center;">{{ $result->total_questions }}</td>
                                <td>
                                    <div class="score-bar-wrap">
                                        <div class="score-bar-bg">
                                            <div class="score-bar-fill"
                                                 style="width:{{ min($score, 100) }}%; background:{{ $barColor }};"></div>
                                        </div>
                                        <span class="score-bar-num"
                                              style="color:{{ $passed ? '#16a34a' : '#dc2626' }}">
                                            {{ number_format($score, 0) }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @if($passed)
                                        <span class="badge-lulus">✓ Lulus</span>
                                    @else
                                        <span class="badge-tidak-lulus">✗ Tidak Lulus</span>
                                    @endif
                                </td>
                                <td style="font-size:0.82rem;color:var(--gray-400)">
                                    {{ $result->created_at->translatedFormat('d M Y, H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-filament-panels::page>
