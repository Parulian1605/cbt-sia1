<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Override;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;

#[Fillable([
    'name',
    'email',
    'password',
    'username',
    'phone',
    'is_staff',
    'photo_path',
])]
#[Hidden([
    'password',
    'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    //autorisasi user agar dapat login ke filament panel
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_staff;
    }

    /**
     * Hapus foto lama ketika foto profil diganti
     * dan hapus foto ketika user dihapus.
     */
    protected static function booted(): void
    {
        static::updated(function (User $user) {
            if ($user->wasChanged('photo_path')) {

                $oldPhoto = $user->getOriginal('photo_path');

                if (
                    $oldPhoto &&
                    Storage::disk('public')->exists($oldPhoto)
                ) {
                    Storage::disk('public')->delete($oldPhoto);
                }
            }
        });

        static::deleted(function (User $user) {
            if (
                $user->photo_path &&
                Storage::disk('public')->exists($user->photo_path)
            ) {
                Storage::disk('public')->delete($user->photo_path);
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_staff' => 'boolean',
        ];
    }
    // protected $appends = [
    //     'avatar_url',
    // ];

    // user avatar URL
    public function getFilamentAvatarUrl(): ?string
    {
        // Check if the user has a photo_path
        if (
            $this->photo_path && 
            Storage::disk('public')->exists($this->photo_path)
            ) {
                /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
                $disk = Storage::disk('public');

                return $disk->url($this->photo_path);
            }
            return null; // Return null if no photo_path is set
    }
}