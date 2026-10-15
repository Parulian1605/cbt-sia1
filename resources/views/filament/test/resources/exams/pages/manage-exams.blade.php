<x-filament-panels::page>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        .exam-page-wrap {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #e2e8f0;
        }

        /* ── Top Stats Bar ── */
        .exam-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .exam-stat-card {
            background: linear-gradient(135deg, #1e2433 0%, #161b27 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1rem;
            padding: 1.15rem 1.35rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.25);
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .exam-stat-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.16);
        }

        .stat-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon--blue {
            background: rgba(59, 130, 246, 0.16);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .stat-icon--amber {
            background: rgba(245, 158, 11, 0.16);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .stat-icon--green {
            background: rgba(34, 197, 94, 0.16);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .stat-content {
            display: flex;
            flex-direction: column;
        }

        .stat-number {
            font-size: 1.45rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.1;
        }

        .stat-text {
            font-size: 0.75rem;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.2rem;
        }

        /* ── Controls Bar: Search & Filter ── */
        .controls-bar {
            background: linear-gradient(135deg, #1a202c 0%, #151922 100%);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 1rem;
            padding: 0.85rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.25rem;
        }

        .search-wrap {
            position: relative;
            flex: 1;
            min-width: 240px;
            max-width: 420px;
        }

        .search-icon {
            position: absolute;
            left: 0.95rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            pointer-events: none;
            width: 18px;
            height: 18px;
        }

        .search-input {
            width: 100%;
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.65rem;
            padding: 0.6rem 0.9rem 0.6rem 2.4rem;
            color: #f1f5f9;
            font-size: 0.875rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .search-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            background: rgba(15, 23, 42, 0.9);
        }

        .filter-tabs {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(0, 0, 0, 0.25);
            padding: 0.3rem;
            border-radius: 0.65rem;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .filter-btn {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.45rem 0.85rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }

        .filter-btn:hover {
            color: #f8fafc;
            background: rgba(255, 255, 255, 0.05);
        }

        .filter-btn.active {
            background: #3b82f6;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.35);
        }

        .filter-count {
            background: rgba(255, 255, 255, 0.15);
            padding: 0.1rem 0.45rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        /* ── Modern Table Container ── */
        .table-container {
            background: linear-gradient(135deg, #1a1f2e 0%, #141821 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1.25rem;
            overflow: hidden;
            box-shadow: 0 8px 32px -4px rgba(0, 0, 0, 0.35);
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .exam-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            text-align: left;
        }

        .exam-table thead {
            background: rgba(255, 255, 255, 0.03);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .exam-table th {
            padding: 1rem 1.25rem;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #64748b;
            white-space: nowrap;
        }

        .exam-table th.col-center {
            text-align: center;
        }

        .exam-table th.col-right {
            text-align: right;
        }

        .exam-table tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            transition: background 0.15s ease;
        }

        .exam-table tbody tr:last-child {
            border-bottom: none;
        }

        .exam-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.025);
        }

        .exam-table td {
            padding: 1.15rem 1.25rem;
            vertical-align: middle;
            color: #cbd5e1;
        }

        .exam-table td.col-center {
            text-align: center;
        }

        .exam-table td.col-right {
            text-align: right;
        }

        /* ── Col: Number ── */
        .row-num {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            color: #64748b;
        }

        /* ── Col: Nama Ujian & Subject Pills ── */
        .exam-name-cell {
            min-width: 280px;
            max-width: 440px;
        }

        .exam-title-text {
            font-size: 0.98rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.45rem;
            line-height: 1.35;
        }

        .subject-chips-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
        }

        .subject-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.6rem;
            border-radius: 0.45rem;
            font-size: 0.74rem;
            font-weight: 600;
            line-height: 1.2;
        }

        .subject-chip--done {
            background: rgba(34, 197, 94, 0.12);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.25);
        }

        .subject-chip--pending {
            background: rgba(255, 255, 255, 0.05);
            color: #94a3b8;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .chip-qty {
            opacity: 0.75;
            font-size: 0.68rem;
        }

        /* ── Col: Durasi & KKM ── */
        .pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .pill-badge--duration {
            background: rgba(59, 130, 246, 0.1);
            color: #93c5fd;
            border: 1px solid rgba(59, 130, 246, 0.25);
        }

        .pill-badge--kkm {
            background: rgba(245, 158, 11, 0.1);
            color: #fcd34d;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        /* ── Col: Dates ── */
        .date-box {
            font-size: 0.82rem;
            white-space: nowrap;
        }

        .date-box strong {
            display: block;
            color: #e2e8f0;
            font-weight: 700;
            font-size: 0.84rem;
        }

        .date-box span {
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* ── Col: Progres Mapel ── */
        .progress-col-wrap {
            min-width: 150px;
        }

        .progress-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            white-space: nowrap;
            margin-bottom: 0.4rem;
        }

        .progress-pill--done {
            background: rgba(34, 197, 94, 0.12);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.28);
        }

        .progress-pill--progress {
            background: rgba(245, 158, 11, 0.12);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.28);
        }

        .progress-pill--idle {
            background: rgba(148, 163, 184, 0.1);
            color: #94a3b8;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .mini-track {
            height: 5px;
            width: 100%;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 9999px;
            overflow: hidden;
        }

        .mini-fill {
            height: 100%;
            border-radius: 9999px;
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
            transition: width 0.3s ease;
        }

        .mini-fill--done {
            background: linear-gradient(90deg, #22c55e, #4ade80);
        }

        /* ── Col: Aksi Button ── */
        .btn-table-action {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.55rem 1.05rem;
            border-radius: 0.6rem;
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-action--start {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            box-shadow: 0 3px 12px rgba(37, 99, 235, 0.35);
        }

        .btn-action--start:hover {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            transform: translateY(-1px);
            box-shadow: 0 5px 16px rgba(37, 99, 235, 0.5);
            color: #ffffff;
        }

        .btn-action--continue {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            box-shadow: 0 3px 12px rgba(217, 119, 6, 0.35);
        }

        .btn-action--continue:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            transform: translateY(-1px);
            box-shadow: 0 5px 16px rgba(217, 119, 6, 0.5);
            color: #ffffff;
        }

        .btn-action--done {
            background: rgba(34, 197, 94, 0.12);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #4ade80;
            box-shadow: none;
            cursor: default;
        }

        .btn-action--locked {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #64748b;
            box-shadow: none;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* ── Empty State ── */
        .empty-table-box {
            padding: 3.5rem 2rem;
            text-align: center;
            color: #64748b;
        }

        .empty-table-box svg {
            width: 48px;
            height: 48px;
            margin: 0 auto 1rem;
            opacity: 0.5;
        }
    </style>

    <div class="exam-page-wrap">
        @php
            $stats = $this->getStats();
            $exams = $this->getExams();
        @endphp

        {{-- ── Stats Cards Bar ── --}}
        <div class="exam-stats-grid">
            <div class="exam-stat-card">
                <div class="stat-icon-box stat-icon--blue">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <span class="stat-number">{{ $stats['total'] }}</span>
                    <span class="stat-text">Total Sesi Ujian</span>
                </div>
            </div>

            <div class="exam-stat-card">
                <div class="stat-icon-box stat-icon--amber">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <span class="stat-number">{{ $stats['in_progress'] + $stats['not_started'] }}</span>
                    <span class="stat-text">Perlu Dikerjakan</span>
                </div>
            </div>

            <div class="exam-stat-card">
                <div class="stat-icon-box stat-icon--green">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <span class="stat-number">{{ $stats['completed'] }}</span>
                    <span class="stat-text">Semua Mapel Selesai</span>
                </div>
            </div>
        </div>

        {{-- ── Search & Filter Tabs ── --}}
        <div class="controls-bar">
            <div class="search-wrap">
                <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input 
                    type="text" 
                    class="search-input" 
                    placeholder="Cari nama ujian..." 
                    wire:model.live.debounce.300ms="search"
                >
            </div>

            <div class="filter-tabs">
                <button 
                    type="button" 
                    wire:click="$set('filter', 'all')" 
                    class="filter-btn {{ $filter === 'all' ? 'active' : '' }}"
                >
                    Semua
                    <span class="filter-count">{{ $stats['total'] }}</span>
                </button>
                <button 
                    type="button" 
                    wire:click="$set('filter', 'in_progress')" 
                    class="filter-btn {{ $filter === 'in_progress' ? 'active' : '' }}"
                >
                    Belum Selesai
                    <span class="filter-count">{{ $stats['in_progress'] + $stats['not_started'] }}</span>
                </button>
                <button 
                    type="button" 
                    wire:click="$set('filter', 'completed')" 
                    class="filter-btn {{ $filter === 'completed' ? 'active' : '' }}"
                >
                    Selesai
                    <span class="filter-count">{{ $stats['completed'] }}</span>
                </button>
            </div>
        </div>

        {{-- ── Tabel Ujian Modern ── --}}
        <div class="table-container">
            <div class="table-responsive">
                <table class="exam-table">
                    <thead>
                        <tr>
                            <th class="col-center" style="width: 50px;">No</th>
                            <th>Nama Ujian & Mata Pelajaran</th>
                            <th class="col-center">Durasi</th>
                            <th class="col-center">KKM / Min. Skor</th>
                            <th>Waktu Mulai</th>
                            <th>Batas Akhir</th>
                            <th>Progres Mapel</th>
                            <th class="col-right" style="min-width: 190px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $index => $exam)
                            @php
                                $isComplete = $exam->_allDone;
                                $isInProgress = $exam->_done > 0 && !$exam->_allDone;
                                $isLocked = $exam->_isLocked;
                            @endphp
                            <tr>
                                {{-- No --}}
                                <td class="col-center">
                                    <span class="row-num">{{ $index + 1 }}</span>
                                </td>

                                {{-- Nama Ujian & Subject Chips --}}
                                <td class="exam-name-cell">
                                    <div class="exam-title-text">{{ $exam->title }}</div>
                                    <div class="subject-chips-wrap">
                                        @forelse($exam->subjects as $subject)
                                            @php
                                                $subjectDone = in_array($subject->id, $exam->_completedSubjectIds ?? []);
                                            @endphp
                                            <span class="subject-chip {{ $subjectDone ? 'subject-chip--done' : 'subject-chip--pending' }}" title="{{ $subjectDone ? 'Sudah selesai' : 'Belum dikerjakan' }}">
                                                @if($subjectDone)
                                                    <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @else
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                @endif
                                                <span>{{ $subject->name }}</span>
                                                <span class="chip-qty">({{ $subject->pivot->qty ?? '0' }} soal)</span>
                                            </span>
                                        @empty
                                            <span class="text-xs text-slate-500 italic">Belum ada mapel</span>
                                        @endforelse
                                    </div>
                                </td>

                                {{-- Durasi --}}
                                <td class="col-center">
                                    <span class="pill-badge pill-badge--duration">
                                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $exam->duration }} Menit
                                    </span>
                                </td>

                                {{-- KKM / Min Skor --}}
                                <td class="col-center">
                                    <span class="pill-badge pill-badge--kkm">
                                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ number_format($exam->threshold, 2, ',', '.') }}
                                    </span>
                                </td>

                                {{-- Waktu Mulai --}}
                                <td>
                                    <div class="date-box">
                                        @if($exam->started_at)
                                            <strong>{{ $exam->started_at->format('d M Y') }}</strong>
                                            <span>{{ $exam->started_at->format('H:i') }} WIB</span>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Batas Akhir --}}
                                <td>
                                    <div class="date-box">
                                        @if($exam->expired_at)
                                            <strong>{{ $exam->expired_at->format('d M Y') }}</strong>
                                            <span>{{ $exam->expired_at->format('H:i') }} WIB</span>
                                        @else
                                            <span style="color: #64748b;">Tanpa Batas</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Progres Mapel --}}
                                <td>
                                    <div class="progress-col-wrap">
                                        <span class="progress-pill {{ $isComplete ? 'progress-pill--done' : ($isInProgress ? 'progress-pill--progress' : 'progress-pill--idle') }}">
                                            @if($isComplete)
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @elseif($isInProgress)
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                            {{ $exam->_progress }}
                                        </span>
                                        <div class="mini-track">
                                            <div class="mini-fill {{ $isComplete ? 'mini-fill--done' : '' }}" style="width: {{ $exam->_percentage }}%;"></div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td class="col-right">
                                    @if($isLocked)
                                        <button type="button" class="btn-table-action btn-action--locked" disabled>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                            Belum Dibuka
                                        </button>
                                    @elseif($isComplete)
                                        <span class="btn-table-action btn-action--done">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Semua Selesai
                                        </span>
                                    @elseif($isInProgress)
                                        <a href="{{ $this->getExamUrl($exam) }}" class="btn-table-action btn-action--continue">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg>
                                            Lanjutkan Ujian
                                        </a>
                                    @else
                                        <a href="{{ $this->getExamUrl($exam) }}" class="btn-table-action btn-action--start">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                            Pilih Mapel & Mulai
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-table-box">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div style="font-weight: 700; color: #f1f5f9; margin-bottom: 0.25rem;">Tidak Ada Data Sesi Ujian</div>
                                        <div style="font-size: 0.8rem;">
                                            @if($search)
                                                Tidak ditemukan sesi ujian dengan kata kunci "{{ $search }}".
                                            @else
                                                Saat ini belum ada sesi ujian yang tersedia.
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
