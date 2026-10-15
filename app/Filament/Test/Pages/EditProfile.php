<?php

namespace App\Filament\Test\Pages;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    protected static ?string $title = 'Ubah Password';

    public static function getLabel(): string
    {
        return 'Ubah Password';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getCurrentPasswordFormComponent()
                    ->label('Password saat ini')
                    ->visible(fn (Get $get): bool => true)
                    ->required(),

                $this->getPasswordFormComponent()
                    ->label('Password baru'),

                $this->getPasswordConfirmationFormComponent()
                    ->label('Ulangi password baru'),
            ]);
    }
}
