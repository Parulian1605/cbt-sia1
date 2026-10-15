<?php

namespace App\Filament\Resources\StudentGrades;

use App\Filament\Resources\StudentGrades\Pages\ListStudentGrades;
use App\Models\ExamResult;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentGradesResource extends Resource
{
    protected static ?string $model = ExamResult::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Nilai Siswa';

    protected static ?string $pluralModelLabel = 'Nilai Siswa';

    protected static ?string $modelLabel = 'Nilai Siswa';

    protected static ?string $slug = 'nilai-siswa';

    protected static string|UnitEnum|null $navigationGroup = 'Monitoring';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('user.student.nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('exam.title')
                    ->label('Ujian')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('exam.subjects.name')
                    ->label('Mapel')
                    ->badge()
                    ->separator(',')
                    ->searchable(),

                TextColumn::make('exam.duration')
                    ->label('Durasi')
                    ->suffix(' Menit')
                    ->alignCenter(),

                TextColumn::make('exam.threshold')
                    ->label('KKM')
                    ->alignCenter()
                    ->numeric(0),

                TextColumn::make('correct_count')
                    ->label('Benar')
                    ->alignCenter()
                    ->color('success')
                    ->weight('bold'),

                TextColumn::make('salah')
                    ->label('Salah')
                    ->alignCenter()
                    ->color('danger')
                    ->weight('bold')
                    ->getStateUsing(fn (ExamResult $record) => $record->total_questions - $record->correct_count),

                TextColumn::make('score')
                    ->label('Nilai Siswa')
                    ->alignCenter()
                    ->weight('bold')
                    ->numeric(2),

                TextColumn::make('kelulusan')
                    ->label('Kelulusan')
                    ->alignCenter()
                    ->badge()
                    ->color(fn (ExamResult $record) => $record->score >= $record->exam->threshold ? 'success' : 'danger')
                    ->getStateUsing(fn (ExamResult $record) => $record->score >= $record->exam->threshold ? 'Lulus' : 'Tidak Lulus'),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->alignCenter()
                    ->getStateUsing(fn (ExamResult $record) => $record->score >= $record->exam->threshold ? 'Tuntas' : 'Belum Tuntas'),

                TextColumn::make('created_at')
                    ->label('Waktu Selesai')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([])
            ->emptyStateHeading('Belum ada nilai')
            ->emptyStateDescription('Nilai ujian siswa akan muncul di sini.')
            ->emptyStateIcon(Heroicon::OutlinedChartBar);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentGrades::route('/'),
        ];
    }
}
