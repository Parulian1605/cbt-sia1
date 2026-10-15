<x-filament-panels::page>
    <style>
        /* ─── Layout Grid ─────────────────────────────────────── */
        .profile-grid {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 1.5rem;
            align-items: start;
        }
        @media (max-width: 900px) {
            .profile-grid { grid-template-columns: 1fr; }
        }

        /* ─── Cards ───────────────────────────────────────────── */
        .profile-card {
            background: var(--surface-bg, white);
            border: 1px solid var(--gray-200);
            border-radius: 1rem;
            overflow: hidden;
        }
        .dark .profile-card {
            background: rgba(255,255,255,0.03);
            border-color: var(--gray-700);
        }

        /* ─── Avatar Section ──────────────────────────────────── */
        .avatar-banner {
            background: linear-gradient(135deg, rgb(var(--primary-600)), rgb(var(--primary-400)));
            height: 90px;
            position: relative;
        }
        .avatar-wrap {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 1.5rem 1.5rem;
        }
        .avatar-circle {
            width: 96px;
            height: 96px;
            border-radius: 9999px;
            border: 4px solid white;
            background: rgb(var(--primary-100));
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: -48px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        .dark .avatar-circle {
            border-color: var(--gray-800);
        }
        .avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .avatar-initials {
            font-size: 2.2rem;
            font-weight: 700;
            color: rgb(var(--primary-600));
        }
        .avatar-name {
            margin-top: 0.75rem;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--gray-900);
            text-align: center;
        }
        .dark .avatar-name { color: var(--gray-100); }
        .avatar-nis {
            font-size: 0.82rem;
            color: var(--gray-500);
            margin-top: 0.2rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        /* ─── Info List ───────────────────────────────────────── */
        .info-divider {
            border: none;
            border-top: 1px solid var(--gray-100);
            margin: 0.75rem 0;
        }
        .dark .info-divider { border-color: var(--gray-700); }
        .info-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.55rem 1.5rem;
        }
        .info-icon {
            width: 32px;
            height: 32px;
            border-radius: 0.5rem;
            background: rgba(var(--primary-500), 0.1);
            color: rgb(var(--primary-600));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .info-label {
            font-size: 0.75rem;
            color: var(--gray-400);
            line-height: 1;
        }
        .info-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--gray-800);
        }
        .dark .info-value { color: var(--gray-100); }

        /* ─── Stats Row ───────────────────────────────────────── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
        }
        @media (max-width: 640px) {
            .stats-row { grid-template-columns: repeat(2, 1fr); }
        }
        .stat-box {
            padding: 1.1rem 0.75rem;
            text-align: center;
            border-right: 1px solid var(--gray-100);
        }
        .dark .stat-box { border-color: var(--gray-700); }
        .stat-box:last-child { border-right: none; }
        .stat-num {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, rgb(var(--primary-600)), rgb(var(--primary-400)));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stat-lbl {
            font-size: 0.72rem;
            color: var(--gray-400);
            margin-top: 0.2rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        /* ─── Upload Photo ────────────────────────────────────── */
        .upload-zone {
            border: 2px dashed var(--gray-200);
            border-radius: 0.75rem;
            padding: 1.2rem;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            margin: 1rem 1.5rem;
        }
        .upload-zone:hover {
            border-color: rgb(var(--primary-400));
            background: rgba(var(--primary-500), 0.04);
        }
        .dark .upload-zone { border-color: var(--gray-600); }
        .upload-zone input[type="file"] {
            display: none;
        }
        .upload-zone label {
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.4rem;
        }
        .upload-zone svg {
            width: 28px;
            height: 28px;
            color: var(--gray-400);
        }
        .upload-hint {
            font-size: 0.78rem;
            color: var(--gray-400);
        }

        /* ─── Password Card ───────────────────────────────────── */
        .section-header {
            padding: 1.25rem 1.5rem 0.75rem;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .dark .section-header { border-color: var(--gray-700); }
        .section-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--gray-900);
        }
        .dark .section-title { color: var(--gray-100); }
        .section-icon {
            width: 36px;
            height: 36px;
            border-radius: 0.6rem;
            background: rgba(var(--primary-500), 0.1);
            color: rgb(var(--primary-600));
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .section-body { padding: 1.25rem 1.5rem; }

        /* ─── Form Input ──────────────────────────────────────── */
        .form-group {
            margin-bottom: 1rem;
        }
        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--gray-600);
            margin-bottom: 0.4rem;
        }
        .dark .form-label { color: var(--gray-400); }
        .form-input {
            width: 100%;
            padding: 0.6rem 0.9rem;
            border: 1px solid var(--gray-200);
            border-radius: 0.5rem;
            font-size: 0.9rem;
            background: var(--gray-50);
            color: var(--gray-900);
            transition: border-color 0.15s, box-shadow 0.15s;
            box-sizing: border-box;
        }
        .dark .form-input {
            background: rgba(255,255,255,0.05);
            border-color: var(--gray-600);
            color: var(--gray-100);
        }
        .form-input:focus {
            outline: none;
            border-color: rgb(var(--primary-500));
            box-shadow: 0 0 0 3px rgba(var(--primary-500), 0.12);
        }

        /* ─── Buttons ─────────────────────────────────────────── */
        .btn-primary {
            background: linear-gradient(135deg, rgb(var(--primary-600)), rgb(var(--primary-500)));
            color: white;
            border: none;
            padding: 0.6rem 1.4rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.1s, box-shadow 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            box-shadow: 0 2px 8px rgba(var(--primary-600), 0.3);
        }
        .btn-primary:hover {
            opacity: 0.92;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(var(--primary-600), 0.35);
        }
        .btn-primary:active { transform: translateY(0); }
        .btn-sm {
            padding: 0.45rem 1rem;
            font-size: 0.82rem;
        }

        /* ─── Alerts ──────────────────────────────────────────── */
        .alert-success {
            background: rgba(34,197,94,0.1);
            color: #16a34a;
            border: 1px solid rgba(34,197,94,0.25);
            padding: 0.6rem 0.9rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 1rem;
        }
        .alert-error {
            background: rgba(239,68,68,0.1);
            color: #dc2626;
            border: 1px solid rgba(239,68,68,0.25);
            padding: 0.6rem 0.9rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 1rem;
        }

        /* ─── Gender Badge ────────────────────────────────────── */
        .gender-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.15rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .gender-male {
            background: rgba(59,130,246,0.12);
            color: #2563eb;
        }
        .gender-female {
            background: rgba(236,72,153,0.12);
            color: #db2777;
        }

        /* ─── Status Badge ────────────────────────────────────── */
        .status-active {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.15rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(34,197,94,0.12);
            color: #16a34a;
        }
        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 9999px;
            background: currentColor;
            display: inline-block;
        }
    </style>

    @php
        $user    = $this->getUser();
        $student = $user->student;
        $stats   = $this->getStats();

        $initials = collect(explode(' ', $user->name ?? $student->name ?? 'S'))
            ->take(2)
            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
            ->join('');

        $avatarUrl = $user->getFilamentAvatarUrl();

        $statusLabel = match($student?->status?->value ?? $student?->status) {
            1 => 'Aktif',
            2 => 'Lulus',
            3 => 'Keluar',
            default => 'Aktif',
        };
    @endphp

    <div class="profile-grid">

        {{-- ── LEFT: Identity Card ── --}}
        <div>
            {{-- Avatar & Info --}}
            <div class="profile-card">
                <div class="avatar-banner"></div>
                <div class="avatar-wrap">
                    <div class="avatar-circle">
                        @if($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="Avatar">
                        @else
                            <span class="avatar-initials">{{ $initials }}</span>
                        @endif
                    </div>
                    <div class="avatar-name">{{ $student?->name ?? $user->name }}</div>
                    <div class="avatar-nis">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;color:var(--gray-400)">
                            <path fill-rule="evenodd" d="M10 2a.75.75 0 01.75.75v.258a33.186 33.186 0 016.668 1.372.75.75 0 01-.336 1.461 31.28 31.28 0 00-1.103-.232l1.702 7.545a.75.75 0 01-.387.832A4.981 4.981 0 0115 14c-.825 0-1.606-.2-2.294-.556a.75.75 0 01-.387-.832l1.77-7.849A31.743 31.743 0 0010 4.5c-1.37 0-2.71.131-4.006.381l1.703 7.61a.75.75 0 01-.387.832A4.98 4.98 0 015 14a4.98 4.98 0 01-2.294-.556.75.75 0 01-.387-.832l1.77-7.849a31.743 31.743 0 00-1.176.254.75.75 0 01-.336-1.46 33.186 33.186 0 016.668-1.373V2.75A.75.75 0 0110 2z" clip-rule="evenodd"/>
                        </svg>
                        NIS: {{ $student?->nis ?? '—' }}
                    </div>
                </div>

                <hr class="info-divider">

                {{-- Stats --}}
                <div class="stats-row">
                    <div class="stat-box">
                        <div class="stat-num">{{ $stats['total'] }}</div>
                        <div class="stat-lbl">Ujian</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-num" style="color: #16a34a; -webkit-text-fill-color: #16a34a;">{{ $stats['passed'] }}</div>
                        <div class="stat-lbl">Lulus</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-num" style="color: #dc2626; -webkit-text-fill-color: #dc2626;">{{ $stats['failed'] }}</div>
                        <div class="stat-lbl">Tidak Lulus</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-num">{{ $stats['avg_score'] }}</div>
                        <div class="stat-lbl">Rata-rata</div>
                    </div>
                </div>

                <hr class="info-divider">

                {{-- Info Rows --}}
                <div style="padding-bottom:1rem;">
                    <div class="info-row">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px">
                                <path d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 00-13.074.003z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value">{{ $student?->name ?? $user->name }}</div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px">
                                <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 003 3.5v13A1.5 1.5 0 004.5 18h11a1.5 1.5 0 001.5-1.5V7.621a1.5 1.5 0 00-.44-1.06l-4.12-4.122A1.5 1.5 0 0011.378 2H4.5zm2.25 8.5a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5zm0 3a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <div class="info-label">NIS</div>
                            <div class="info-value">{{ $student?->nis ?? '—' }}</div>
                        </div>
                    </div>

                    @if($student)
                    <div class="info-row">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <div class="info-label">Jenis Kelamin</div>
                            <div class="info-value">
                                @if($student->gender)
                                    <span class="gender-badge gender-male">♂ Laki-laki</span>
                                @else
                                    <span class="gender-badge gender-female">♀ Perempuan</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px">
                                <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <div class="info-label">Tahun Masuk</div>
                            <div class="info-value">{{ $student->reg_year }}</div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                <span class="status-active">
                                    <span class="status-dot"></span>
                                    {{ $statusLabel }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="info-row">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px">
                                <path d="M3 4a2 2 0 00-2 2v1.161l8.438 4.686a1.75 1.75 0 001.124 0L19 7.162V6a2 2 0 00-2-2H3z"/><path d="M19 8.839l-7.77 4.317a3.25 3.25 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="info-label">Email</div>
                            <div class="info-value">{{ $user->email ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Upload Foto --}}
            <div class="profile-card" style="margin-top:1.25rem;">
                <div class="section-header">
                    <div class="section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:18px;height:18px">
                            <path fill-rule="evenodd" d="M1 8a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 018.07 3h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0016.07 6H17a2 2 0 012 2v7a2 2 0 01-2 2H3a2 2 0 01-2-2V8zm13.5 3a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM10 14a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="section-title">Foto Profil</span>
                </div>
                <div class="section-body">
                    @if($photoSuccess)
                        <div class="alert-success">✓ {{ $photoSuccess }}</div>
                    @endif
                    <form wire:submit.prevent="uploadPhoto">
                        <div class="upload-zone">
                            <label for="photo-upload">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z"/>
                                </svg>
                                <span style="font-size:0.85rem;font-weight:600;color:rgb(var(--primary-600))">
                                    Pilih foto dari perangkat
                                </span>
                                <span class="upload-hint">PNG, JPG, WEBP (maks 2MB)</span>
                                <input id="photo-upload" type="file" wire:model="photo" accept="image/*">
                            </label>
                        </div>
                        @if($photo)
                            <div style="font-size:0.8rem;color:var(--gray-500);margin:-0.25rem 0 0.75rem;text-align:center;">
                                File dipilih: {{ $photo->getClientOriginalName() }}
                            </div>
                        @endif
                        <button type="submit" class="btn-primary btn-sm" style="width:100%;justify-content:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:15px;height:15px">
                                <path fill-rule="evenodd" d="M10 1a.75.75 0 01.75.75v6.638l1.96-2.158a.75.75 0 111.08 1.04l-3.25 3.5a.75.75 0 01-1.08 0l-3.25-3.5a.75.75 0 111.08-1.04l1.96 2.158V1.75A.75.75 0 0110 1zM2 13a.75.75 0 01.75.75 2.75 2.75 0 002.75 2.75h9a2.75 2.75 0 002.75-2.75.75.75 0 011.5 0 4.25 4.25 0 01-4.25 4.25h-9A4.25 4.25 0 011.25 13.75.75.75 0 012 13z" clip-rule="evenodd"/>
                            </svg>
                            Upload Foto
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── RIGHT: Password Change ── --}}
        <div>
            {{-- Greeting Banner --}}
            <div class="profile-card" style="background: linear-gradient(135deg, rgb(var(--primary-600)), rgb(var(--primary-400))); border:none; padding: 1.75rem; margin-bottom: 1.5rem;">
                <div style="display:flex;align-items:center;gap:1.25rem;">
                    <div style="width:56px;height:56px;border-radius:0.8rem;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="white" style="width:28px;height:28px">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-5.5-2.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM10 12a5.99 5.99 0 00-4.793 2.39A6.483 6.483 0 0010 16.5a6.483 6.483 0 004.793-2.11A5.99 5.99 0 0010 12z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size:0.8rem;color:rgba(255,255,255,0.75);text-transform:uppercase;letter-spacing:0.06em;font-weight:600;">Selamat datang</div>
                        <div style="font-size:1.4rem;font-weight:800;color:white;line-height:1.2;">
                            {{ $student?->name ?? $user->name }}
                        </div>
                        <div style="font-size:0.82rem;color:rgba(255,255,255,0.75);margin-top:0.2rem;">
                            @{{ $user->username }}  •  Siswa
                        </div>
                    </div>
                </div>
            </div>

            {{-- Password Form --}}
            <div class="profile-card">
                <div class="section-header">
                    <div class="section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:18px;height:18px">
                            <path fill-rule="evenodd" d="M8 7a5 5 0 114.546 4.985L12 12.5l1 1-1 1 .5.5-1.5 1.5-1-1-3.5 3.5H4v-2.5L8.015 12A5.002 5.002 0 018 7zm5-1.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <div class="section-title">Ubah Password</div>
                        <div style="font-size:0.75rem;color:var(--gray-400);margin-top:0.1rem;">Pastikan password baru minimal 8 karakter</div>
                    </div>
                </div>
                <div class="section-body">
                    @if($passwordSuccess)
                        <div class="alert-success">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;flex-shrink:0">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                            </svg>
                            {{ $passwordSuccess }}
                        </div>
                    @endif
                    @if($passwordError)
                        <div class="alert-error">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;flex-shrink:0">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                            </svg>
                            {{ $passwordError }}
                        </div>
                    @endif

                    <form wire:submit.prevent="updatePassword">
                        <div class="form-group">
                            <label class="form-label" for="curr-pwd">Password Saat Ini</label>
                            <input
                                id="curr-pwd"
                                type="password"
                                class="form-input"
                                wire:model="current_password"
                                placeholder="Masukkan password saat ini"
                                autocomplete="current-password"
                            >
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                            <div class="form-group">
                                <label class="form-label" for="new-pwd">Password Baru</label>
                                <input
                                    id="new-pwd"
                                    type="password"
                                    class="form-input"
                                    wire:model="new_password"
                                    placeholder="Min. 8 karakter"
                                    autocomplete="new-password"
                                >
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="new-pwd-confirm">Konfirmasi Password Baru</label>
                                <input
                                    id="new-pwd-confirm"
                                    type="password"
                                    class="form-input"
                                    wire:model="new_password_confirmation"
                                    placeholder="Ulangi password baru"
                                    autocomplete="new-password"
                                >
                            </div>
                        </div>

                        <div style="margin-top:0.5rem;">
                            <button type="submit" class="btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px">
                                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/>
                                </svg>
                                Simpan Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tips Card --}}
            <div class="profile-card" style="margin-top:1.25rem;">
                <div class="section-header">
                    <div class="section-icon" style="background:rgba(251,191,36,0.12);color:#d97706;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:18px;height:18px">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="section-title">Tips Keamanan</span>
                </div>
                <div class="section-body">
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.6rem;">
                        @foreach([
                            'Gunakan minimal 8 karakter untuk password',
                            'Kombinasikan huruf besar, kecil, angka & simbol',
                            'Jangan gunakan tanggal lahir sebagai password',
                            'Jangan bagikan password kepada siapa pun',
                        ] as $tip)
                        <li style="display:flex;align-items:flex-start;gap:0.6rem;font-size:0.85rem;color:var(--gray-600);">
                            <span style="color:#d97706;font-weight:700;flex-shrink:0;margin-top:1px;">•</span>
                            {{ $tip }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
