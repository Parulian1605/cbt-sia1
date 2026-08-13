<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Subject;
use Database\Factories\Banks\IpaBank;
use Database\Factories\Banks\IpsBank;
use Database\Factories\Banks\PjokBank;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * QuestionFactory
 *
 * Menggunakan trait bank soal per mata pelajaran.
 * Setiap trait menyimpan 150 soal statis (array $pjokQuestions, $ipsQuestions, $ipaQuestions).
 * Method withAnswers() mengganti payload dummy dengan soal nyata + membuat Answer records.
 */
class QuestionFactory extends Factory
{
    use PjokBank, IpsBank, IpaBank;

    protected $model = Question::class;

    // ─── Static counters per mata pelajaran ───────────────────────────────────
    protected static ?int $pjokIndex = null;
    protected static ?int $ipsIndex  = null;
    protected static ?int $ipaIndex  = null;

    // ─── Deskripsi acak ───────────────────────────────────────────────────────
    protected static array $DESCRIPTIONS_FIXED = [
        'Pelajari materi ini dengan seksama',
        'Penting untuk ujian tengah semester',
        'Soal tingkat dasar',
        'Soal ini sering muncul dalam ujian',
        'Memahami konsep dasar sangat diperlukan',
        'Berdasarkan kurikulum terbaru',
        'Soal analisis tingkat menengah',
        'Perlu perhitungan teliti',
        'Pahami konsep sebelum menjawab',
        'Soal aplikasi dalam kehidupan sehari-hari',
        'Topik yang sering ditanyakan',
        'Melatih pemahaman konseptual',
        'Kombinasi teori dan praktik',
        'Soal evaluasi pemahaman siswa',
        'Referensi buku pelajaran kelas 8',
    ];

    // ─── Definition ───────────────────────────────────────────────────────────
    public function definition(): array
    {
        return [
            'subject_id'  => Subject::factory(),
            'payload'     => fake()->sentence(10) . '?',
            'score'       => fake()->randomElement([1, 2, 3]),
            'description' => fake()->boolean(70)
                ? fake()->randomElement(self::$DESCRIPTIONS_FIXED)
                : null,
            'is_active'   => true,
        ];
    }

    // ─── State: kaitkan ke subject tertentu ───────────────────────────────────
    public function forSubject(Subject $subject): static
    {
        return $this->state(fn (array $attributes) => [
            'subject_id' => $subject->id,
        ]);
    }

    // ─── State: buat jawaban nyata setelah Question dibuat ────────────────────
    public function withAnswers(): static
    {
        return $this->afterCreating(function (Question $question) {
            $questionData = $this->getQuestionForSubject($question);

            if ($questionData) {
                // Update payload dengan soal nyata
                $question->update(['payload' => $questionData['q']]);

                // Buat 4 pilihan jawaban
                foreach ($questionData['a'] as $index => $answerText) {
                    $question->answers()->create([
                        'text'       => $answerText,
                        'is_correct' => ($index === $questionData['c']),
                        'is_active'  => true,
                    ]);
                }

                // Update deskripsi
                $question->update([
                    'description' => fake()->boolean(70)
                        ? fake()->randomElement(self::$DESCRIPTIONS_FIXED)
                        : null,
                ]);
            }
        });
    }

    // ─── Ambil soal dari bank berdasarkan nama subject ────────────────────────
    protected function getQuestionForSubject(Question $question): ?array
    {
        $subject = $question->subject;
        if (! $subject) {
            return null;
        }

        return match ($subject->name) {
            'PJOK' => $this->getFromBank($this->pjokQuestions, self::$pjokIndex),
            'IPS'  => $this->getFromBank($this->ipsQuestions,  self::$ipsIndex),
            'IPA'  => $this->getFromBank($this->ipaQuestions,  self::$ipaIndex),
            default => null,
        };
    }

    /**
     * Ambil soal berikutnya dari bank menggunakan counter statis.
     * Reset ke 0 jika sudah habis (overflow protection).
     */
    private function getFromBank(array $bank, ?int &$index): ?array
    {
        if (empty($bank)) {
            return null;
        }

        if ($index === null) {
            $index = 0;
        }

        $item = $bank[$index % count($bank)];
        $index++;

        return $item;
    }
}
