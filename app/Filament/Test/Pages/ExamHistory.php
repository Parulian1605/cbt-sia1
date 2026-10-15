<?php

namespace App\Filament\Test\Pages;

use App\Models\ExamResult;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class ExamHistory extends Page
{
    protected string $view = 'filament.test.pages.exam-history';

    protected static ?string $navigationLabel = 'Riwayat Ujian';

    protected static ?string $title = 'Riwayat Ujian';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?int $navigationSort = 2;

    public function getResults()
    {
        return ExamResult::with(['exam', 'subject'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
    }
}
