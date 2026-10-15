<?php

namespace App\Filament\Test\Pages;

use App\Models\ExamResult;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ExamResultDetail extends Page
{
    protected string $view = 'filament.test.pages.exam-result-detail';

    protected static bool $shouldRegisterNavigation = false;

    public ?ExamResult $result = null;

    public function mount()
    {
        $id = request()->query('id');

        $this->result = ExamResult::with(['exam', 'subject', 'answers.question', 'answers.answer'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }
}
