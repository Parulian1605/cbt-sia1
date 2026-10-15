<?php

namespace App\Filament\Test\Pages;

use App\Models\ExamResult;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use BackedEnum;

class ProfilePage extends Page
{
    protected string $view = 'filament.test.pages.profile-page';

    protected static ?string $navigationLabel = 'Profil Saya';

    protected static ?string $title = 'Profil Saya';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static ?int $navigationSort = 3;

    // Password form fields
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    // Photo upload
    public $photo = null;

    // Notification messages
    public ?string $passwordSuccess = null;
    public ?string $passwordError = null;
    public ?string $photoSuccess = null;

    public function getUser()
    {
        return Auth::user()->load('student');
    }

    public function getStats(): array
    {
        $userId = Auth::id();
        $results = ExamResult::where('user_id', $userId)->with('exam')->get();

        $total = $results->count();
        $passed = $results->filter(fn($r) => $r->score >= ($r->exam->threshold ?? 75))->count();
        $avgScore = $total > 0 ? $results->avg('score') : 0;

        return [
            'total'     => $total,
            'passed'    => $passed,
            'failed'    => $total - $passed,
            'avg_score' => round($avgScore, 1),
        ];
    }

    public function updatePassword(): void
    {
        $this->passwordSuccess = null;
        $this->passwordError = null;

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->passwordError = 'Password saat ini tidak sesuai.';
            return;
        }

        $validator = Validator::make(
            [
                'new_password'              => $this->new_password,
                'new_password_confirmation' => $this->new_password_confirmation,
            ],
            [
                'new_password' => ['required', 'min:8', 'confirmed'],
            ],
            [
                'new_password.required'  => 'Password baru wajib diisi.',
                'new_password.min'       => 'Password baru minimal 8 karakter.',
                'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
            ]
        );

        if ($validator->fails()) {
            $this->passwordError = $validator->errors()->first();
            return;
        }

        $user->update(['password' => Hash::make($this->new_password)]);

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';
        $this->passwordSuccess = 'Password berhasil diubah!';
    }

    public function uploadPhoto(): void
    {
        $this->photoSuccess = null;

        if (!$this->photo) {
            return;
        }

        $user = Auth::user();

        // Delete old photo if exists
        if ($user->photo_path && Storage::disk('public')->exists($user->photo_path)) {
            Storage::disk('public')->delete($user->photo_path);
        }

        $path = $this->photo->store('avatars', 'public');
        $user->update(['photo_path' => $path]);

        $this->photo = null;
        $this->photoSuccess = 'Foto profil berhasil diperbarui!';
    }
}
