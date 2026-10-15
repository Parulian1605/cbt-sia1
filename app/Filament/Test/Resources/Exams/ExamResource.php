<?php

namespace App\Filament\Test\Resources\Exams;

use App\Filament\Test\Resources\Exams\Pages\ManageExams;
use App\Filament\Test\Resources\Exams\Pages\StartingExam;
use App\Models\Exam;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'Sesi Ujian';

    protected static ?string $modelLabel = 'Ujian';

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $now = now();
                $query
                    ->where('is_available', true)
                    ->where(function (Builder $query) use ($now) {
                        $query
                            ->where(function (Builder $query) use ($now) {
                                $query
                                    ->where('exact_time', true)
                                    ->where('started_at', '<=', $now)
                                    ->whereRaw(
                                        'DATE_ADD(started_at, INTERVAL duration MINUTE) >= ?',
                                        [$now]
                                    );
                            })
                            ->orWhere(function (Builder $query) use ($now) {
                                $query
                                    ->whereNotNull('expired_at')
                                    ->where('expired_at', '>=', $now);
                            });
                    });
            })
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label('Nama Ujian')
                    ->description(fn (Exam $record): string =>
                        $record->subjects->map(fn ($s) =>
                            $s->name . ' (' . $s->pivot->qty . ' soal)'
                        )->join(' • ')
                    )
                    ->searchable()
                    ->wrap()
                    ->grow(true),

                TextColumn::make('duration')
                    ->label('Durasi')
                    ->suffix(' Menit')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('threshold')
                    ->label('KKM / Min. Skor')
                    ->numeric(2)
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('started_at')
                    ->label('Waktu Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('expired_at')
                    ->label('Batas Akhir')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('progres_mapel')
                    ->label('Progres Mapel')
                    ->badge()
                    ->getStateUsing(function (Exam $record): string {
                        $userId  = auth()->id();
                        $done    = $record->results()
                            ->where('user_id', $userId)
                            ->whereNotNull('subject_id')
                            ->pluck('subject_id')
                            ->unique()
                            ->count();
                        $total = $record->subjects->count();
                        if ($total === 0)     return 'Belum ada mapel';
                        if ($done >= $total)  return 'Semua mapel selesai';
                        if ($done === 0)      return 'Belum dikerjakan';
                        return "{$done}/{$total} mapel selesai";
                    })
                    ->color(function (Exam $record): string {
                        $userId  = auth()->id();
                        $done    = $record->results()
                            ->where('user_id', $userId)
                            ->whereNotNull('subject_id')
                            ->pluck('subject_id')
                            ->unique()
                            ->count();
                        $total = $record->subjects->count();
                        if ($total > 0 && $done >= $total) return 'success';
                        if ($done === 0)                    return 'gray';
                        return 'warning';
                    }),
            ])
            ->recordActions([
                Action::make('start')
                    ->label(function (Exam $record): string {
                        $userId = auth()->id();
                        $done   = $record->results()
                            ->where('user_id', $userId)
                            ->whereNotNull('subject_id')
                            ->pluck('subject_id')->unique()->count();
                        $total = $record->subjects->count();
                        return ($total > 0 && $done >= $total) ? 'Semua Selesai' : 'Pilih Mapel & Mulai';
                    })
                    ->icon(function (Exam $record): string {
                        $userId = auth()->id();
                        $done   = $record->results()
                            ->where('user_id', $userId)
                            ->whereNotNull('subject_id')
                            ->pluck('subject_id')->unique()->count();
                        $total = $record->subjects->count();
                        return ($total > 0 && $done >= $total) ? 'heroicon-o-lock-closed' : 'heroicon-o-paper-airplane';
                    })
                    ->color(function (Exam $record): string {
                        $userId = auth()->id();
                        $done   = $record->results()
                            ->where('user_id', $userId)
                            ->whereNotNull('subject_id')
                            ->pluck('subject_id')->unique()->count();
                        $total = $record->subjects->count();
                        return ($total > 0 && $done >= $total) ? 'gray' : 'primary';
                    })
                    ->button()
                    ->disabled(fn (Exam $record): bool => $record->started_at > now())
                    ->url(fn (Exam $record) =>
                        route(StartingExam::getRouteName(), ['exam' => $record])
                    ),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageExams::route('/'),
            'mulai' => StartingExam::route('/{exam}/mulai'),
        ];
    }
}
