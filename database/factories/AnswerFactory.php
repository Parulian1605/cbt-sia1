<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    public function definition(): array
    {
        $letters = ['A', 'B', 'C', 'D'];

        return [
            'question_id' => Question::factory(),
            'text'        => $letters[fake()->numberBetween(0, 3)] . '. ' . fake()->sentence(3),
            'is_correct'  => false,
            'is_active'   => true,
        ];
    }

    /** Tandai jawaban sebagai benar */
    public function correct(): static
    {
        return $this->state(fn (array $attributes) => ['is_correct' => true]);
    }

    /** Tandai jawaban sebagai salah */
    public function incorrect(): static
    {
        return $this->state(fn (array $attributes) => ['is_correct' => false]);
    }
}
