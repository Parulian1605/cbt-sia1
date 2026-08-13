<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionAnswerSeeder extends Seeder
{
    private const TOTAL_PER_SUBJECT = 150;

    public function run(): void
    {
        mt_srand(2026); // agar hasil acak konsisten setiap kali seeder dijalankan

        // Truncate tables to reset auto-increment IDs to 1
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Question::truncate();
        Answer::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $map = [
            'PJOK'       => 'pjokQuestions',
            'IPS'        => 'ipsQuestions',
            'IPA'        => 'ipaQuestions',
        ];

        foreach ($map as $subjectName => $method) {
            $subject = Subject::where('name', $subjectName)->first();

            if (! $subject) {
                $this->command?->warn("Mata pelajaran '{$subjectName}' tidak ditemukan, dilewati.");

                continue;
            }

            $pool = self::{$method}();
            shuffle($pool);
            $pool = array_slice($pool, 0, self::TOTAL_PER_SUBJECT);

            DB::transaction(function () use ($subject, $pool, $subjectName) {
                foreach ($pool as $item) {
                    $question = Question::create([
                        'subject_id'  => $subject->id,
                        'payload'     => $item['question'],
                        'score'       => 1,
                        'description' => null,
                        'is_active'   => true,
                    ]);

                    foreach ($item['options'] as $option) {
                        Answer::create([
                            'question_id' => $question->id,
                            'text'        => $option['text'],
                            'is_correct'  => $option['correct'],
                            'is_active'   => true,
                        ]);
                    }
                }

                $this->command?->info("{$subjectName}: ".count($pool)." soal berhasil dibuat.");
            });
        }
    }

    // Bentuk satu soal pilihan ganda dari 1 jawaban benar + beberapa jawaban salah.
    private static function transformBankQuestions(array $bank): array
    {
        $items = [];
        foreach ($bank as $item) {
            $options = [];
            foreach ($item['a'] as $idx => $text) {
                $options[] = [
                    'text' => $text,
                    'correct' => ($idx === $item['c'])
                ];
            }
            $items[] = [
                'question' => $item['q'],
                'options' => $options
            ];
        }
        return $items;
    }

     
    private static function buildMcq(string $question, string $correct, array $wrongs): array
    {
        $options = [['text' => $correct, 'correct' => true]];

        foreach ($wrongs as $w) {
            $options[] = ['text' => $w, 'correct' => false];
        }

        shuffle($options);

        return ['question' => $question, 'options' => $options];
    }

    private static function pickDistractors(array $pairs, int|string $excludeKey, int $n, bool $wantValue, array $fallbackPool = []): array
    {
        $excludeValue = $wantValue ? ($pairs[$excludeKey] ?? '') : $excludeKey;
        $excludeNormalized = mb_strtolower(trim((string) $excludeValue));

        $seen = [$excludeNormalized => true];
        $result = [];

        $candidates = $pairs;
        unset($candidates[$excludeKey]);
        $keys = array_keys($candidates);
        shuffle($keys);

        foreach ($keys as $k) {
            $val = $wantValue ? (string) $candidates[$k] : (string) $k;
            $norm = mb_strtolower(trim($val));
            if (isset($seen[$norm])) {
                continue;
            }
            $seen[$norm] = true;
            $result[] = $val;
            if (count($result) >= $n) {
                break;
            }
        }

        if (count($result) < $n && ! empty($fallbackPool)) {
            $extra = $fallbackPool;
            shuffle($extra);
            foreach ($extra as $val) {
                $norm = mb_strtolower(trim($val));
                if (isset($seen[$norm])) {
                    continue;
                }
                $seen[$norm] = true;
                $result[] = $val;
                if (count($result) >= $n) {
                    break;
                }
            }
        }

        return $result;
    }

    private static function buildLookupQuestions(array $pairs, string $askValue, ?string $askKey = null, int $distractorCount = 3, array $valueFallbackPool = []): array
    {
        $items = [];

        foreach ($pairs as $key => $value) {
            $wrongValues = self::pickDistractors($pairs, $key, $distractorCount, true, $valueFallbackPool);
            // jaga-jaga: jika pool unik + fallback tetap kurang dari yang diminta, lewati soal ini
            // agar tidak menghasilkan pilihan jawaban duplikat.
            if (count($wrongValues) < $distractorCount) {
                continue;
            }
            $items[] = self::buildMcq(sprintf($askValue, $key), (string) $value, $wrongValues);

            if ($askKey !== null) {
                $wrongKeys = self::pickDistractors($pairs, $key, $distractorCount, false);
                if (count($wrongKeys) < $distractorCount) {
                    continue;
                }
                $items[] = self::buildMcq(sprintf($askKey, $value), (string) $key, $wrongKeys);
            }
        }

        return $items;
    }

    private static function numericDistractors(int|float $correct, int $minDelta = 1, int $maxDeltaPercent = 30): array
    {
        $wrongs = [];
        $attempts = 0;

        while (count($wrongs) < 3 && $attempts < 50) {
            $attempts++;
            $deltaBase = max($minDelta, (int) round(abs($correct) * mt_rand(5, $maxDeltaPercent) / 100));
            $delta = mt_rand(1, max(1, $deltaBase)) * (mt_rand(0, 1) ? 1 : -1);
            $w = $correct + $delta;

            if ($w == $correct || $w < 0) {
                continue;
            }

            $wStr = (string) (is_float($correct) ? round($w, 2) : (int) $w);

            if (! in_array($wStr, $wrongs, true)) {
                $wrongs[] = $wStr;
            }
        }

        // jaring pengaman jika perulangan acak di atas belum cukup menghasilkan 3 opsi
        $i = 1;
        while (count($wrongs) < 3) {
            $w = (string) ($correct + $i);
            if ($w !== (string) $correct && ! in_array($w, $wrongs, true)) {
                $wrongs[] = $w;
            }
            $i++;
        }

        return $wrongs;
    }

    private static function gcd(int $a, int $b): int
    {
        return $b === 0 ? max($a, 1) : self::gcd($b, $a % $b);
    }

    // IPS (Menggunakan format matematika sementara untuk testing)

    private static function ipsQuestions(): array
    {
        $items = [];
        // 1) Operasi hitung bilangan bulat (target 40 soal unik, dicoba hingga 80x agar aman)
        $count = 0;
        for ($i = 0; $count < 40 && $i < 80; $i++) {
            $op = ['+', '-', 'x', ':'][$i % 4];

            switch ($op) {
                case '+':
                    $a = mt_rand(10, 500);
                    $b = mt_rand(10, 500);
                    $correct = $a + $b;
                    $question = "Hasil dari {$a} + {$b} adalah...";
                    break;
                case '-':
                    $a = mt_rand(100, 900);
                    $b = mt_rand(1, $a - 1);
                    $correct = $a - $b;
                    $question = "Hasil dari {$a} - {$b} adalah...";
                    break;
                case 'x':
                    $a = mt_rand(2, 50);
                    $b = mt_rand(2, 50);
                    $correct = $a * $b;
                    $question = "Hasil dari {$a} x {$b} adalah...";
                    break;
                default: // ':'
                    $b = mt_rand(2, 25);
                    $q = mt_rand(2, 50);
                    $a = $b * $q;
                    $correct = $q;
                    $question = "Hasil dari {$a} : {$b} adalah...";
                    break;
            }

            if (! isset($items[$question])) {
                $items[$question] = self::buildMcq($question, (string) $correct, self::numericDistractors($correct));
                $count++;
            }
        }

        // 2) Penjumlahan pecahan sepenyebut (target 10 soal unik, dicoba hingga 30x)
        $denoms = [2, 3, 4, 5, 6, 8, 10, 12];
        $count = 0;
        for ($i = 0; $count < 10 && $i < 30; $i++) {
            $d = $denoms[$i % count($denoms)];
            $n1 = mt_rand(1, $d - 1);
            $n2 = mt_rand(1, $d - 1);
            $sumN = $n1 + $n2;
            $g = self::gcd($sumN, $d);
            $correct = ($sumN / $g).'/'.($d / $g);
            $question = "Hasil dari {$n1}/{$d} + {$n2}/{$d} adalah...";

            $wrongs = [];
            while (count($wrongs) < 3) {
                $wn = max(1, $sumN + mt_rand(-2, 2));
                $cand = $wn.'/'.$d;
                if ($cand !== $correct && ! in_array($cand, $wrongs, true)) {
                    $wrongs[] = $cand;
                }
            }

            if (! isset($items[$question])) {
                $items[$question] = self::buildMcq($question, $correct, $wrongs);
                $count++;
            }
        }

        // 3) Pecahan ke bentuk desimal (10 soal)
        $fracToDecimal = [
            '1/2' => '0,5', '1/4' => '0,25', '3/4' => '0,75', '1/5' => '0,2',
            '2/5' => '0,4', '3/5' => '0,6', '4/5' => '0,8', '1/8' => '0,125',
            '1/10' => '0,1', '3/10' => '0,3',
        ];
        foreach ($fracToDecimal as $frac => $dec) {
            $decVal = (float) str_replace(',', '.', $dec);
            $wrongs = [];
            $guard = 0;
            while (count($wrongs) < 3 && $guard < 50) {
                $guard++;
                $w = round($decVal + (mt_rand(-30, 30) / 100), 3);
                if ($w <= 0) {
                    continue;
                }
                $wStr = rtrim(rtrim(number_format($w, 3, '.', ''), '0'), '.');
                $wStr = str_replace('.', ',', $wStr);
                if ($wStr !== $dec && ! in_array($wStr, $wrongs, true)) {
                    $wrongs[] = $wStr;
                }
            }
            $q = "Bentuk desimal dari pecahan {$frac} adalah...";
            $items[$q] = self::buildMcq($q, $dec, $wrongs);
        }

        // 4) Desimal ke bentuk persen (10 soal)
        $decToPercent = [
            '0,1' => '10%', '0,15' => '15%', '0,2' => '20%', '0,25' => '25%',
            '0,3' => '30%', '0,4' => '40%', '0,5' => '50%', '0,6' => '60%',
            '0,75' => '75%', '0,9' => '90%',
        ];
        foreach ($decToPercent as $dec => $persen) {
            $val = (int) rtrim($persen, '%');
            $wrongs = [];
            $guard = 0;
            while (count($wrongs) < 3 && $guard < 50) {
                $guard++;   
                $w = $val + mt_rand(-3, 3) * 5;
                if ($w <= 0 || $w === $val) {
                    continue;
                }
                $wStr = $w.'%';
                if (! in_array($wStr, $wrongs, true)) {
                    $wrongs[] = $wStr;
                }
            }
            $q = "Bentuk persen dari {$dec} adalah...";
            $items[$q] = self::buildMcq($q, $persen, $wrongs);
        }

        // 5) Persentase dari suatu bilangan (target 20 soal unik, dicoba hingga 50x)
        $percents = [5, 10, 15, 20, 25, 30, 40, 50, 60, 70, 75, 80, 90];
        $count = 0;
        for ($i = 0; $count < 20 && $i < 50; $i++) {
            $p = $percents[$i % count($percents)];
            $base = 20 * mt_rand(1, 25); // kelipatan 20, hasil bagi 100 selalu bulat
            $correct = ($p * $base) / 100;
            $question = "Hasil dari {$p}% dari {$base} adalah...";
            if (! isset($items[$question])) {
                $items[$question] = self::buildMcq($question, (string) $correct, self::numericDistractors($correct));
                $count++;
            }
        }

        // 6) Aljabar sederhana - mencari nilai x (target 20 soal unik, dicoba hingga 50x)
        $count = 0;
        for ($i = 0; $count < 20 && $i < 50; $i++) {
            $a = mt_rand(2, 9);
            $x = mt_rand(1, 20);
            $b = mt_rand(1, 50);
            $sign = $i % 2 === 0 ? '+' : '-';
            $c = $sign === '+' ? ($a * $x + $b) : ($a * $x - $b);
            $question = "Jika {$a}x {$sign} {$b} = {$c}, maka nilai x yang memenuhi adalah...";
            if (! isset($items[$question])) {
                $items[$question] = self::buildMcq($question, (string) $x, self::numericDistractors($x, 1, 25));
                $count++;
            }
        }

        // 7) Geometri: luas & keliling bangun datar (target 20 soal unik, dicoba hingga 50x)
        $count = 0;
        for ($i = 0; $count < 20 && $i < 50; $i++) {
            switch ($i % 4) {
                case 0: // persegi
                    $s = mt_rand(3, 30);
                    if ($i % 8 < 4) {
                        $correct = $s * $s;
                        $question = "Luas persegi dengan panjang sisi {$s} cm adalah...cm2";
                    } else {
                        $correct = 4 * $s;
                        $question = "Keliling persegi dengan panjang sisi {$s} cm adalah...cm";
                    }
                    break;
                case 1: // persegi panjang
                    $p = mt_rand(6, 40);
                    $l = mt_rand(3, $p - 1);
                    if ($i % 8 < 4) {
                        $correct = $p * $l;
                        $question = "Luas persegi panjang dengan panjang {$p} cm dan lebar {$l} cm adalah...cm2";
                    } else {
                        $correct = 2 * ($p + $l);
                        $question = "Keliling persegi panjang dengan panjang {$p} cm dan lebar {$l} cm adalah...cm";
                    }
                    break;
                case 2: // segitiga
                    $alas = 2 * mt_rand(3, 20);
                    $tinggi = mt_rand(3, 20);
                    $correct = ($alas * $tinggi) / 2;
                    $question = "Luas segitiga dengan alas {$alas} cm dan tinggi {$tinggi} cm adalah...cm2";
                    break;
                default: // lingkaran, jari-jari kelipatan 7 agar hasil dengan pi=22/7 bulat
                    $r = 7 * mt_rand(1, 10);
                    if ($i % 8 < 4) {
                        $correct = (22 / 7) * $r * $r;
                        $question = "Luas lingkaran dengan jari-jari {$r} cm (menggunakan pi=22/7) adalah...cm2";
                    } else {
                        $correct = 2 * (22 / 7) * $r;
                        $question = "Keliling lingkaran dengan jari-jari {$r} cm (menggunakan pi=22/7) adalah...cm";
                    }
                    break;
            }
            $correct = (int) round($correct);
            if (! isset($items[$question])) {
                $items[$question] = self::buildMcq($question, (string) $correct, self::numericDistractors($correct));
                $count++;
            }
        }

        // 8) Konversi satuan (target 20 soal unik, dicoba hingga 50x)
        $conversions = [
            ['dari' => 'km', 'ke' => 'm', 'faktor' => 1000],
            ['dari' => 'm', 'ke' => 'cm', 'faktor' => 100],
            ['dari' => 'kg', 'ke' => 'gram', 'faktor' => 1000],
            ['dari' => 'ton', 'ke' => 'kg', 'faktor' => 1000],
            ['dari' => 'jam', 'ke' => 'menit', 'faktor' => 60],
            ['dari' => 'menit', 'ke' => 'detik', 'faktor' => 60],
            ['dari' => 'liter', 'ke' => 'ml', 'faktor' => 1000],
        ];
        $count = 0;
        for ($i = 0; $count < 20 && $i < 50; $i++) {
            $c = $conversions[$i % count($conversions)];
            $val = mt_rand(2, 20);
            $correct = $val * $c['faktor'];
            $question = "{$val} {$c['dari']} = ... {$c['ke']}";
            if (! isset($items[$question])) {
                $items[$question] = self::buildMcq($question, (string) $correct, self::numericDistractors($correct));
                $count++;
            }
        }

        return array_values($items);
    }

    // IPA (pool > 150 soal berbasis data fakta, lalu diacak & diambil 150)

    private static function ipaQuestions(): array
    {
        return self::transformBankQuestions(
[
        [
            'q' => 'Bagian sel yang berfungsi sebagai pusat pengatur seluruh kegiatan sel adalah...',
            'a' => [
                'A. Inti sel (Nukleus)',
                'B. Membran sel',
                'C. Sitoplasma',
                'D. Mitokondria'
            ],
            'c' => 0
        ],
        [
            'q' => 'Organel sel yang berfungsi untuk respirasi seluler dan menghasilkan energi adalah...',
            'a' => [
                'A. Lisosom',
                'B. Mitokondria',
                'C. Badan Golgi',
                'D. Ribosom'
            ],
            'c' => 1
        ],
        [
            'q' => 'Proses pembuatan makanan pada tumbuhan hijau dengan bantuan cahaya matahari disebut...',
            'a' => [
                'A. Transpirasi',
                'B. Respirasi',
                'C. Fotosintesis',
                'D. Kemosintesis'
            ],
            'c' => 2
        ],
        [
            'q' => 'Gas yang dibutuhkan oleh tumbuhan untuk melakukan fotosintesis adalah...',
            'a' => [
                'A. Hidrogen',
                'B. Oksigen',
                'C. Nitrogen',
                'D. Karbon dioksida'
            ],
            'c' => 3
        ],
        [
            'q' => 'Hewan yang memakan tumbuhan disebut...',
            'a' => [
                'A. Herbivora',
                'B. Omnivora',
                'C. Karnivora',
                'D. Insektivora'
            ],
            'c' => 0
        ],
        [
            'q' => 'Hewan yang memakan daging disebut...',
            'a' => [
                'A. Omnivora',
                'B. Karnivora',
                'C. Insektivora',
                'D. Herbivora'
            ],
            'c' => 1
        ],
        [
            'q' => 'Hewan pemakan segala (tumbuhan dan hewan) disebut...',
            'a' => [
                'A. Karnivora',
                'B. Insektivora',
                'C. Omnivora',
                'D. Herbivora'
            ],
            'c' => 2
        ],
        [
            'q' => 'Alat pernapasan pada ikan adalah...',
            'a' => [
                'A. Paru-paru',
                'B. Trakea',
                'C. Kulit',
                'D. Insang'
            ],
            'c' => 3
        ],
        [
            'q' => 'Alat pernapasan pada serangga adalah...',
            'a' => [
                'A. Trakea',
                'B. Paru-paru',
                'C. Kulit',
                'D. Insang'
            ],
            'c' => 0
        ],
        [
            'q' => 'Pembuluh darah yang membawa darah kaya oksigen dari paru-paru ke jantung adalah...',
            'a' => [
                'A. Vena kava',
                'B. Vena pulmonalis',
                'C. Arteri pulmonalis',
                'D. Aorta'
            ],
            'c' => 1
        ],
        [
            'q' => 'Satuan Internasional (SI) untuk panjang adalah...',
            'a' => [
                'A. Kilometer',
                'B. Sentimeter',
                'C. Meter',
                'D. Milimeter'
            ],
            'c' => 2
        ],
        [
            'q' => 'Satuan Internasional (SI) untuk massa adalah...',
            'a' => [
                'A. Kuintal',
                'B. Gram',
                'C. Ton',
                'D. Kilogram'
            ],
            'c' => 3
        ],
        [
            'q' => 'Satuan Internasional (SI) untuk waktu adalah...',
            'a' => [
                'A. Sekon (Detik)',
                'B. Hari',
                'C. Menit',
                'D. Jam'
            ],
            'c' => 0
        ],
        [
            'q' => 'Gaya tarik bumi terhadap benda-benda di sekitarnya disebut...',
            'a' => [
                'A. Gaya magnet',
                'B. Gaya gravitasi',
                'C. Gaya gesek',
                'D. Gaya pegas'
            ],
            'c' => 1
        ],
        [
            'q' => 'Energi yang dimiliki benda karena gerakannya disebut...',
            'a' => [
                'A. Energi panas',
                'B. Energi mekanik',
                'C. Energi kinetik',
                'D. Energi potensial'
            ],
            'c' => 2
        ],
        [
            'q' => 'Energi yang dimiliki benda karena kedudukannya disebut...',
            'a' => [
                'A. Energi panas',
                'B. Energi kinetik',
                'C. Energi mekanik',
                'D. Energi potensial'
            ],
            'c' => 3
        ],
        [
            'q' => 'Hukum I Newton sering juga disebut sebagai hukum...',
            'a' => [
                'A. Kelembaman (Inersia)',
                'B. Aksi-Reaksi',
                'C. Kekekalan Momentum',
                'D. Kekekalan Energi'
            ],
            'c' => 0
        ],
        [
            'q' => 'Peristiwa merambatnya panas melalui suatu zat perantara tanpa disertai perpindahan partikel zat disebut...',
            'a' => [
                'A. Konveksi',
                'B. Konduksi',
                'C. Radiasi',
                'D. Isolasi'
            ],
            'c' => 1
        ],
        [
            'q' => 'Perpindahan panas melalui aliran zat perantara disebut...',
            'a' => [
                'A. Radiasi',
                'B. Konduksi',
                'C. Konveksi',
                'D. Isolasi'
            ],
            'c' => 2
        ],
        [
            'q' => 'Perpindahan panas tanpa melalui zat perantara disebut...',
            'a' => [
                'A. Konduksi',
                'B. Konveksi',
                'C. Isolasi',
                'D. Radiasi'
            ],
            'c' => 3
        ],
        [
            'q' => 'Zat tunggal yang tidak dapat diuraikan lagi menjadi zat lain yang lebih sederhana disebut...',
            'a' => [
                'A. Unsur',
                'B. Senyawa',
                'C. Campuran',
                'D. Molekul'
            ],
            'c' => 0
        ],
        [
            'q' => 'Zat tunggal yang terdiri dari dua unsur atau lebih yang bergabung secara kimia disebut...',
            'a' => [
                'A. Atom',
                'B. Senyawa',
                'C. Unsur',
                'D. Campuran'
            ],
            'c' => 1
        ],
        [
            'q' => 'Rumus kimia untuk air adalah...',
            'a' => [
                'A. O2',
                'B. NaCl',
                'C. H2O',
                'D. CO2'
            ],
            'c' => 2
        ],
        [
            'q' => 'Rumus kimia untuk karbon dioksida adalah...',
            'a' => [
                'A. H2O',
                'B. NaCl',
                'C. O2',
                'D. CO2'
            ],
            'c' => 3
        ],
        [
            'q' => 'Rumus kimia untuk garam dapur adalah...',
            'a' => [
                'A. NaCl',
                'B. H2O',
                'C. CO2',
                'D. O2'
            ],
            'c' => 0
        ],
        [
            'q' => 'Campuran yang komposisinya seragam dan tidak dapat dibedakan komponennya disebut...',
            'a' => [
                'A. Suspensi',
                'B. Campuran homogen (Larutan)',
                'C. Koloid',
                'D. Campuran heterogen'
            ],
            'c' => 1
        ],
        [
            'q' => 'Zat yang bersifat asam memiliki pH...',
            'a' => [
                'A. Tidak memiliki pH',
                'B. Lebih dari 7',
                'C. Kurang dari 7',
                'D. Sama dengan 7'
            ],
            'c' => 2
        ],
        [
            'q' => 'Zat yang bersifat basa memiliki pH...',
            'a' => [
                'A. Tidak memiliki pH',
                'B. Kurang dari 7',
                'C. Sama dengan 7',
                'D. Lebih dari 7'
            ],
            'c' => 3
        ],
        [
            'q' => 'Perubahan wujud dari padat menjadi gas disebut...',
            'a' => [
                'A. Menyublim',
                'B. Mencair',
                'C. Menguap',
                'D. Mengkristal'
            ],
            'c' => 0
        ],
        [
            'q' => 'Logam yang berwujud cair pada suhu ruang adalah...',
            'a' => [
                'A. Emas',
                'B. Raksa (Merkuri)',
                'C. Perak',
                'D. Besi'
            ],
            'c' => 1
        ],
        [
            'q' => 'Bagian sel yang berfungsi sebagai pusat pengatur seluruh kegiatan sel adalah... (Sains 31)',
            'a' => [
                'A. Membran sel',
                'B. Sitoplasma',
                'C. Inti sel (Nukleus)',
                'D. Mitokondria'
            ],
            'c' => 2
        ],
        [
            'q' => 'Organel sel yang berfungsi untuk respirasi seluler dan menghasilkan energi adalah... (Sains 32)',
            'a' => [
                'A. Ribosom',
                'B. Lisosom',
                'C. Badan Golgi',
                'D. Mitokondria'
            ],
            'c' => 3
        ],
        [
            'q' => 'Proses pembuatan makanan pada tumbuhan hijau dengan bantuan cahaya matahari disebut... (Sains 33)',
            'a' => [
                'A. Fotosintesis',
                'B. Respirasi',
                'C. Kemosintesis',
                'D. Transpirasi'
            ],
            'c' => 0
        ],
        [
            'q' => 'Gas yang dibutuhkan oleh tumbuhan untuk melakukan fotosintesis adalah... (Sains 34)',
            'a' => [
                'A. Nitrogen',
                'B. Karbon dioksida',
                'C. Oksigen',
                'D. Hidrogen'
            ],
            'c' => 1
        ],
        [
            'q' => 'Hewan yang memakan tumbuhan disebut... (Sains 35)',
            'a' => [
                'A. Insektivora',
                'B. Karnivora',
                'C. Herbivora',
                'D. Omnivora'
            ],
            'c' => 2
        ],
        [
            'q' => 'Hewan yang memakan daging disebut... (Sains 36)',
            'a' => [
                'A. Herbivora',
                'B. Insektivora',
                'C. Omnivora',
                'D. Karnivora'
            ],
            'c' => 3
        ],
        [
            'q' => 'Hewan pemakan segala (tumbuhan dan hewan) disebut... (Sains 37)',
            'a' => [
                'A. Omnivora',
                'B. Karnivora',
                'C. Herbivora',
                'D. Insektivora'
            ],
            'c' => 0
        ],
        [
            'q' => 'Alat pernapasan pada ikan adalah... (Sains 38)',
            'a' => [
                'A. Paru-paru',
                'B. Insang',
                'C. Trakea',
                'D. Kulit'
            ],
            'c' => 1
        ],
        [
            'q' => 'Alat pernapasan pada serangga adalah... (Sains 39)',
            'a' => [
                'A. Paru-paru',
                'B. Kulit',
                'C. Trakea',
                'D. Insang'
            ],
            'c' => 2
        ],
        [
            'q' => 'Pembuluh darah yang membawa darah kaya oksigen dari paru-paru ke jantung adalah... (Sains 40)',
            'a' => [
                'A. Arteri pulmonalis',
                'B. Vena kava',
                'C. Aorta',
                'D. Vena pulmonalis'
            ],
            'c' => 3
        ],
        [
            'q' => 'Satuan Internasional (SI) untuk panjang adalah... (Sains 41)',
            'a' => [
                'A. Meter',
                'B. Sentimeter',
                'C. Kilometer',
                'D. Milimeter'
            ],
            'c' => 0
        ],
        [
            'q' => 'Satuan Internasional (SI) untuk massa adalah... (Sains 42)',
            'a' => [
                'A. Kuintal',
                'B. Kilogram',
                'C. Gram',
                'D. Ton'
            ],
            'c' => 1
        ],
        [
            'q' => 'Satuan Internasional (SI) untuk waktu adalah... (Sains 43)',
            'a' => [
                'A. Jam',
                'B. Menit',
                'C. Sekon (Detik)',
                'D. Hari'
            ],
            'c' => 2
        ],
        [
            'q' => 'Gaya tarik bumi terhadap benda-benda di sekitarnya disebut... (Sains 44)',
            'a' => [
                'A. Gaya magnet',
                'B. Gaya gesek',
                'C. Gaya pegas',
                'D. Gaya gravitasi'
            ],
            'c' => 3
        ],
        [
            'q' => 'Energi yang dimiliki benda karena gerakannya disebut... (Sains 45)',
            'a' => [
                'A. Energi kinetik',
                'B. Energi potensial',
                'C. Energi panas',
                'D. Energi mekanik'
            ],
            'c' => 0
        ],
        [
            'q' => 'Energi yang dimiliki benda karena kedudukannya disebut... (Sains 46)',
            'a' => [
                'A. Energi kinetik',
                'B. Energi potensial',
                'C. Energi panas',
                'D. Energi mekanik'
            ],
            'c' => 1
        ],
        [
            'q' => 'Hukum I Newton sering juga disebut sebagai hukum... (Sains 47)',
            'a' => [
                'A. Kekekalan Momentum',
                'B. Kekekalan Energi',
                'C. Kelembaman (Inersia)',
                'D. Aksi-Reaksi'
            ],
            'c' => 2
        ],
        [
            'q' => 'Peristiwa merambatnya panas melalui suatu zat perantara tanpa disertai perpindahan partikel zat disebut... (Sains 48)',
            'a' => [
                'A. Konveksi',
                'B. Isolasi',
                'C. Radiasi',
                'D. Konduksi'
            ],
            'c' => 3
        ],
        [
            'q' => 'Perpindahan panas melalui aliran zat perantara disebut... (Sains 49)',
            'a' => [
                'A. Konveksi',
                'B. Radiasi',
                'C. Isolasi',
                'D. Konduksi'
            ],
            'c' => 0
        ],
        [
            'q' => 'Perpindahan panas tanpa melalui zat perantara disebut... (Sains 50)',
            'a' => [
                'A. Konduksi',
                'B. Radiasi',
                'C. Konveksi',
                'D. Isolasi'
            ],
            'c' => 1
        ],
        [
            'q' => 'Zat tunggal yang tidak dapat diuraikan lagi menjadi zat lain yang lebih sederhana disebut... (Sains 51)',
            'a' => [
                'A. Campuran',
                'B. Senyawa',
                'C. Unsur',
                'D. Molekul'
            ],
            'c' => 2
        ],
        [
            'q' => 'Zat tunggal yang terdiri dari dua unsur atau lebih yang bergabung secara kimia disebut... (Sains 52)',
            'a' => [
                'A. Campuran',
                'B. Unsur',
                'C. Atom',
                'D. Senyawa'
            ],
            'c' => 3
        ],
        [
            'q' => 'Rumus kimia untuk air adalah... (Sains 53)',
            'a' => [
                'A. H2O',
                'B. O2',
                'C. NaCl',
                'D. CO2'
            ],
            'c' => 0
        ],
        [
            'q' => 'Rumus kimia untuk karbon dioksida adalah... (Sains 54)',
            'a' => [
                'A. NaCl',
                'B. CO2',
                'C. H2O',
                'D. O2'
            ],
            'c' => 1
        ],
        [
            'q' => 'Rumus kimia untuk garam dapur adalah... (Sains 55)',
            'a' => [
                'A. O2',
                'B. H2O',
                'C. NaCl',
                'D. CO2'
            ],
            'c' => 2
        ],
        [
            'q' => 'Campuran yang komposisinya seragam dan tidak dapat dibedakan komponennya disebut... (Sains 56)',
            'a' => [
                'A. Koloid',
                'B. Suspensi',
                'C. Campuran heterogen',
                'D. Campuran homogen (Larutan)'
            ],
            'c' => 3
        ],
        [
            'q' => 'Zat yang bersifat asam memiliki pH... (Sains 57)',
            'a' => [
                'A. Kurang dari 7',
                'B. Lebih dari 7',
                'C. Sama dengan 7',
                'D. Tidak memiliki pH'
            ],
            'c' => 0
        ],
        [
            'q' => 'Zat yang bersifat basa memiliki pH... (Sains 58)',
            'a' => [
                'A. Kurang dari 7',
                'B. Lebih dari 7',
                'C. Sama dengan 7',
                'D. Tidak memiliki pH'
            ],
            'c' => 1
        ],
        [
            'q' => 'Perubahan wujud dari padat menjadi gas disebut... (Sains 59)',
            'a' => [
                'A. Menguap',
                'B. Mengkristal',
                'C. Menyublim',
                'D. Mencair'
            ],
            'c' => 2
        ],
        [
            'q' => 'Logam yang berwujud cair pada suhu ruang adalah... (Sains 60)',
            'a' => [
                'A. Besi',
                'B. Emas',
                'C. Perak',
                'D. Raksa (Merkuri)'
            ],
            'c' => 3
        ],
        [
            'q' => 'Bagian sel yang berfungsi sebagai pusat pengatur seluruh kegiatan sel adalah... (Sains 61)',
            'a' => [
                'A. Inti sel (Nukleus)',
                'B. Mitokondria',
                'C. Membran sel',
                'D. Sitoplasma'
            ],
            'c' => 0
        ],
        [
            'q' => 'Organel sel yang berfungsi untuk respirasi seluler dan menghasilkan energi adalah... (Sains 62)',
            'a' => [
                'A. Badan Golgi',
                'B. Mitokondria',
                'C. Ribosom',
                'D. Lisosom'
            ],
            'c' => 1
        ],
        [
            'q' => 'Proses pembuatan makanan pada tumbuhan hijau dengan bantuan cahaya matahari disebut... (Sains 63)',
            'a' => [
                'A. Kemosintesis',
                'B. Respirasi',
                'C. Fotosintesis',
                'D. Transpirasi'
            ],
            'c' => 2
        ],
        [
            'q' => 'Gas yang dibutuhkan oleh tumbuhan untuk melakukan fotosintesis adalah... (Sains 64)',
            'a' => [
                'A. Hidrogen',
                'B. Oksigen',
                'C. Nitrogen',
                'D. Karbon dioksida'
            ],
            'c' => 3
        ],
        [
            'q' => 'Hewan yang memakan tumbuhan disebut... (Sains 65)',
            'a' => [
                'A. Herbivora',
                'B. Omnivora',
                'C. Karnivora',
                'D. Insektivora'
            ],
            'c' => 0
        ],
        [
            'q' => 'Hewan yang memakan daging disebut... (Sains 66)',
            'a' => [
                'A. Insektivora',
                'B. Karnivora',
                'C. Herbivora',
                'D. Omnivora'
            ],
            'c' => 1
        ],
        [
            'q' => 'Hewan pemakan segala (tumbuhan dan hewan) disebut... (Sains 67)',
            'a' => [
                'A. Insektivora',
                'B. Herbivora',
                'C. Omnivora',
                'D. Karnivora'
            ],
            'c' => 2
        ],
        [
            'q' => 'Alat pernapasan pada ikan adalah... (Sains 68)',
            'a' => [
                'A. Paru-paru',
                'B. Trakea',
                'C. Kulit',
                'D. Insang'
            ],
            'c' => 3
        ],
        [
            'q' => 'Alat pernapasan pada serangga adalah... (Sains 69)',
            'a' => [
                'A. Trakea',
                'B. Paru-paru',
                'C. Kulit',
                'D. Insang'
            ],
            'c' => 0
        ],
        [
            'q' => 'Pembuluh darah yang membawa darah kaya oksigen dari paru-paru ke jantung adalah... (Sains 70)',
            'a' => [
                'A. Vena kava',
                'B. Vena pulmonalis',
                'C. Aorta',
                'D. Arteri pulmonalis'
            ],
            'c' => 1
        ],
        [
            'q' => 'Satuan Internasional (SI) untuk panjang adalah... (Sains 71)',
            'a' => [
                'A. Milimeter',
                'B. Kilometer',
                'C. Meter',
                'D. Sentimeter'
            ],
            'c' => 2
        ],
        [
            'q' => 'Satuan Internasional (SI) untuk massa adalah... (Sains 72)',
            'a' => [
                'A. Gram',
                'B. Ton',
                'C. Kuintal',
                'D. Kilogram'
            ],
            'c' => 3
        ],
        [
            'q' => 'Satuan Internasional (SI) untuk waktu adalah... (Sains 73)',
            'a' => [
                'A. Sekon (Detik)',
                'B. Menit',
                'C. Jam',
                'D. Hari'
            ],
            'c' => 0
        ],
        [
            'q' => 'Gaya tarik bumi terhadap benda-benda di sekitarnya disebut... (Sains 74)',
            'a' => [
                'A. Gaya gesek',
                'B. Gaya gravitasi',
                'C. Gaya pegas',
                'D. Gaya magnet'
            ],
            'c' => 1
        ],
        [
            'q' => 'Energi yang dimiliki benda karena gerakannya disebut... (Sains 75)',
            'a' => [
                'A. Energi mekanik',
                'B. Energi potensial',
                'C. Energi kinetik',
                'D. Energi panas'
            ],
            'c' => 2
        ],
        [
            'q' => 'Energi yang dimiliki benda karena kedudukannya disebut... (Sains 76)',
            'a' => [
                'A. Energi mekanik',
                'B. Energi panas',
                'C. Energi kinetik',
                'D. Energi potensial'
            ],
            'c' => 3
        ],
        [
            'q' => 'Hukum I Newton sering juga disebut sebagai hukum... (Sains 77)',
            'a' => [
                'A. Kelembaman (Inersia)',
                'B. Aksi-Reaksi',
                'C. Kekekalan Momentum',
                'D. Kekekalan Energi'
            ],
            'c' => 0
        ],
        [
            'q' => 'Peristiwa merambatnya panas melalui suatu zat perantara tanpa disertai perpindahan partikel zat disebut... (Sains 78)',
            'a' => [
                'A. Radiasi',
                'B. Konduksi',
                'C. Isolasi',
                'D. Konveksi'
            ],
            'c' => 1
        ],
        [
            'q' => 'Perpindahan panas melalui aliran zat perantara disebut... (Sains 79)',
            'a' => [
                'A. Isolasi',
                'B. Radiasi',
                'C. Konveksi',
                'D. Konduksi'
            ],
            'c' => 2
        ],
        [
            'q' => 'Perpindahan panas tanpa melalui zat perantara disebut... (Sains 80)',
            'a' => [
                'A. Konveksi',
                'B. Konduksi',
                'C. Isolasi',
                'D. Radiasi'
            ],
            'c' => 3
        ],
        [
            'q' => 'Zat tunggal yang tidak dapat diuraikan lagi menjadi zat lain yang lebih sederhana disebut... (Sains 81)',
            'a' => [
                'A. Unsur',
                'B. Senyawa',
                'C. Campuran',
                'D. Molekul'
            ],
            'c' => 0
        ],
        [
            'q' => 'Zat tunggal yang terdiri dari dua unsur atau lebih yang bergabung secara kimia disebut... (Sains 82)',
            'a' => [
                'A. Atom',
                'B. Senyawa',
                'C. Campuran',
                'D. Unsur'
            ],
            'c' => 1
        ],
        [
            'q' => 'Rumus kimia untuk air adalah... (Sains 83)',
            'a' => [
                'A. O2',
                'B. NaCl',
                'C. H2O',
                'D. CO2'
            ],
            'c' => 2
        ],
        [
            'q' => 'Rumus kimia untuk karbon dioksida adalah... (Sains 84)',
            'a' => [
                'A. H2O',
                'B. NaCl',
                'C. O2',
                'D. CO2'
            ],
            'c' => 3
        ],
        [
            'q' => 'Rumus kimia untuk garam dapur adalah... (Sains 85)',
            'a' => [
                'A. NaCl',
                'B. O2',
                'C. CO2',
                'D. H2O'
            ],
            'c' => 0
        ],
        [
            'q' => 'Campuran yang komposisinya seragam dan tidak dapat dibedakan komponennya disebut... (Sains 86)',
            'a' => [
                'A. Suspensi',
                'B. Campuran homogen (Larutan)',
                'C. Campuran heterogen',
                'D. Koloid'
            ],
            'c' => 1
        ],
        [
            'q' => 'Zat yang bersifat asam memiliki pH... (Sains 87)',
            'a' => [
                'A. Tidak memiliki pH',
                'B. Sama dengan 7',
                'C. Kurang dari 7',
                'D. Lebih dari 7'
            ],
            'c' => 2
        ],
        [
            'q' => 'Zat yang bersifat basa memiliki pH... (Sains 88)',
            'a' => [
                'A. Sama dengan 7',
                'B. Tidak memiliki pH',
                'C. Kurang dari 7',
                'D. Lebih dari 7'
            ],
            'c' => 3
        ],
        [
            'q' => 'Perubahan wujud dari padat menjadi gas disebut... (Sains 89)',
            'a' => [
                'A. Menyublim',
                'B. Mengkristal',
                'C. Menguap',
                'D. Mencair'
            ],
            'c' => 0
        ],
        [
            'q' => 'Logam yang berwujud cair pada suhu ruang adalah... (Sains 90)',
            'a' => [
                'A. Perak',
                'B. Raksa (Merkuri)',
                'C. Besi',
                'D. Emas'
            ],
            'c' => 1
        ],
        [
            'q' => 'Bagian sel yang berfungsi sebagai pusat pengatur seluruh kegiatan sel adalah... (Sains 91)',
            'a' => [
                'A. Sitoplasma',
                'B. Membran sel',
                'C. Inti sel (Nukleus)',
                'D. Mitokondria'
            ],
            'c' => 2
        ],
        [
            'q' => 'Organel sel yang berfungsi untuk respirasi seluler dan menghasilkan energi adalah... (Sains 92)',
            'a' => [
                'A. Ribosom',
                'B. Lisosom',
                'C. Badan Golgi',
                'D. Mitokondria'
            ],
            'c' => 3
        ],
        [
            'q' => 'Proses pembuatan makanan pada tumbuhan hijau dengan bantuan cahaya matahari disebut... (Sains 93)',
            'a' => [
                'A. Fotosintesis',
                'B. Respirasi',
                'C. Kemosintesis',
                'D. Transpirasi'
            ],
            'c' => 0
        ],
        [
            'q' => 'Gas yang dibutuhkan oleh tumbuhan untuk melakukan fotosintesis adalah... (Sains 94)',
            'a' => [
                'A. Oksigen',
                'B. Karbon dioksida',
                'C. Nitrogen',
                'D. Hidrogen'
            ],
            'c' => 1
        ],
        [
            'q' => 'Hewan yang memakan tumbuhan disebut... (Sains 95)',
            'a' => [
                'A. Karnivora',
                'B. Insektivora',
                'C. Herbivora',
                'D. Omnivora'
            ],
            'c' => 2
        ],
        [
            'q' => 'Hewan yang memakan daging disebut... (Sains 96)',
            'a' => [
                'A. Insektivora',
                'B. Omnivora',
                'C. Herbivora',
                'D. Karnivora'
            ],
            'c' => 3
        ],
        [
            'q' => 'Hewan pemakan segala (tumbuhan dan hewan) disebut... (Sains 97)',
            'a' => [
                'A. Omnivora',
                'B. Karnivora',
                'C. Insektivora',
                'D. Herbivora'
            ],
            'c' => 0
        ],
        [
            'q' => 'Alat pernapasan pada ikan adalah... (Sains 98)',
            'a' => [
                'A. Trakea',
                'B. Insang',
                'C. Kulit',
                'D. Paru-paru'
            ],
            'c' => 1
        ],
        [
            'q' => 'Alat pernapasan pada serangga adalah... (Sains 99)',
            'a' => [
                'A. Insang',
                'B. Kulit',
                'C. Trakea',
                'D. Paru-paru'
            ],
            'c' => 2
        ],
        [
            'q' => 'Pembuluh darah yang membawa darah kaya oksigen dari paru-paru ke jantung adalah... (Sains 100)',
            'a' => [
                'A. Arteri pulmonalis',
                'B. Vena kava',
                'C. Aorta',
                'D. Vena pulmonalis'
            ],
            'c' => 3
        ],
        [
            'q' => 'Satuan Internasional (SI) untuk panjang adalah... (Sains 101)',
            'a' => [
                'A. Meter',
                'B. Milimeter',
                'C. Sentimeter',
                'D. Kilometer'
            ],
            'c' => 0
        ],
        [
            'q' => 'Satuan Internasional (SI) untuk massa adalah... (Sains 102)',
            'a' => [
                'A. Ton',
                'B. Kilogram',
                'C. Gram',
                'D. Kuintal'
            ],
            'c' => 1
        ],
        [
            'q' => 'Satuan Internasional (SI) untuk waktu adalah... (Sains 103)',
            'a' => [
                'A. Menit',
                'B. Jam',
                'C. Sekon (Detik)',
                'D. Hari'
            ],
            'c' => 2
        ],
        [
            'q' => 'Gaya tarik bumi terhadap benda-benda di sekitarnya disebut... (Sains 104)',
            'a' => [
                'A. Gaya pegas',
                'B. Gaya gesek',
                'C. Gaya magnet',
                'D. Gaya gravitasi'
            ],
            'c' => 3
        ],
        [
            'q' => 'Energi yang dimiliki benda karena gerakannya disebut... (Sains 105)',
            'a' => [
                'A. Energi kinetik',
                'B. Energi panas',
                'C. Energi mekanik',
                'D. Energi potensial'
            ],
            'c' => 0
        ],
        [
            'q' => 'Energi yang dimiliki benda karena kedudukannya disebut... (Sains 106)',
            'a' => [
                'A. Energi kinetik',
                'B. Energi potensial',
                'C. Energi mekanik',
                'D. Energi panas'
            ],
            'c' => 1
        ],
        [
            'q' => 'Hukum I Newton sering juga disebut sebagai hukum... (Sains 107)',
            'a' => [
                'A. Kekekalan Energi',
                'B. Kekekalan Momentum',
                'C. Kelembaman (Inersia)',
                'D. Aksi-Reaksi'
            ],
            'c' => 2
        ],
        [
            'q' => 'Peristiwa merambatnya panas melalui suatu zat perantara tanpa disertai perpindahan partikel zat disebut... (Sains 108)',
            'a' => [
                'A. Radiasi',
                'B. Isolasi',
                'C. Konveksi',
                'D. Konduksi'
            ],
            'c' => 3
        ],
        [
            'q' => 'Perpindahan panas melalui aliran zat perantara disebut... (Sains 109)',
            'a' => [
                'A. Konveksi',
                'B. Isolasi',
                'C. Konduksi',
                'D. Radiasi'
            ],
            'c' => 0
        ],
        [
            'q' => 'Perpindahan panas tanpa melalui zat perantara disebut... (Sains 110)',
            'a' => [
                'A. Konduksi',
                'B. Radiasi',
                'C. Konveksi',
                'D. Isolasi'
            ],
            'c' => 1
        ],
        [
            'q' => 'Zat tunggal yang tidak dapat diuraikan lagi menjadi zat lain yang lebih sederhana disebut... (Sains 111)',
            'a' => [
                'A. Campuran',
                'B. Senyawa',
                'C. Unsur',
                'D. Molekul'
            ],
            'c' => 2
        ],
        [
            'q' => 'Zat tunggal yang terdiri dari dua unsur atau lebih yang bergabung secara kimia disebut... (Sains 112)',
            'a' => [
                'A. Unsur',
                'B. Atom',
                'C. Campuran',
                'D. Senyawa'
            ],
            'c' => 3
        ],
        [
            'q' => 'Rumus kimia untuk air adalah... (Sains 113)',
            'a' => [
                'A. H2O',
                'B. CO2',
                'C. NaCl',
                'D. O2'
            ],
            'c' => 0
        ],
        [
            'q' => 'Rumus kimia untuk karbon dioksida adalah... (Sains 114)',
            'a' => [
                'A. O2',
                'B. CO2',
                'C. NaCl',
                'D. H2O'
            ],
            'c' => 1
        ],
        [
            'q' => 'Rumus kimia untuk garam dapur adalah... (Sains 115)',
            'a' => [
                'A. H2O',
                'B. O2',
                'C. NaCl',
                'D. CO2'
            ],
            'c' => 2
        ],
        [
            'q' => 'Campuran yang komposisinya seragam dan tidak dapat dibedakan komponennya disebut... (Sains 116)',
            'a' => [
                'A. Suspensi',
                'B. Koloid',
                'C. Campuran heterogen',
                'D. Campuran homogen (Larutan)'
            ],
            'c' => 3
        ],
        [
            'q' => 'Zat yang bersifat asam memiliki pH... (Sains 117)',
            'a' => [
                'A. Kurang dari 7',
                'B. Sama dengan 7',
                'C. Lebih dari 7',
                'D. Tidak memiliki pH'
            ],
            'c' => 0
        ],
        [
            'q' => 'Zat yang bersifat basa memiliki pH... (Sains 118)',
            'a' => [
                'A. Kurang dari 7',
                'B. Lebih dari 7',
                'C. Tidak memiliki pH',
                'D. Sama dengan 7'
            ],
            'c' => 1
        ],
        [
            'q' => 'Perubahan wujud dari padat menjadi gas disebut... (Sains 119)',
            'a' => [
                'A. Mencair',
                'B. Menguap',
                'C. Menyublim',
                'D. Mengkristal'
            ],
            'c' => 2
        ],
        [
            'q' => 'Logam yang berwujud cair pada suhu ruang adalah... (Sains 120)',
            'a' => [
                'A. Emas',
                'B. Perak',
                'C. Besi',
                'D. Raksa (Merkuri)'
            ],
            'c' => 3
        ],
        [
            'q' => 'Bagian sel yang berfungsi sebagai pusat pengatur seluruh kegiatan sel adalah... (Sains 121)',
            'a' => [
                'A. Inti sel (Nukleus)',
                'B. Membran sel',
                'C. Sitoplasma',
                'D. Mitokondria'
            ],
            'c' => 0
        ],
        [
            'q' => 'Organel sel yang berfungsi untuk respirasi seluler dan menghasilkan energi adalah... (Sains 122)',
            'a' => [
                'A. Badan Golgi',
                'B. Mitokondria',
                'C. Ribosom',
                'D. Lisosom'
            ],
            'c' => 1
        ],
        [
            'q' => 'Proses pembuatan makanan pada tumbuhan hijau dengan bantuan cahaya matahari disebut... (Sains 123)',
            'a' => [
                'A. Transpirasi',
                'B. Respirasi',
                'C. Fotosintesis',
                'D. Kemosintesis'
            ],
            'c' => 2
        ],
        [
            'q' => 'Gas yang dibutuhkan oleh tumbuhan untuk melakukan fotosintesis adalah... (Sains 124)',
            'a' => [
                'A. Oksigen',
                'B. Hidrogen',
                'C. Nitrogen',
                'D. Karbon dioksida'
            ],
            'c' => 3
        ],
        [
            'q' => 'Hewan yang memakan tumbuhan disebut... (Sains 125)',
            'a' => [
                'A. Herbivora',
                'B. Insektivora',
                'C. Omnivora',
                'D. Karnivora'
            ],
            'c' => 0
        ],
        [
            'q' => 'Hewan yang memakan daging disebut... (Sains 126)',
            'a' => [
                'A. Omnivora',
                'B. Karnivora',
                'C. Insektivora',
                'D. Herbivora'
            ],
            'c' => 1
        ],
        [
            'q' => 'Hewan pemakan segala (tumbuhan dan hewan) disebut... (Sains 127)',
            'a' => [
                'A. Herbivora',
                'B. Karnivora',
                'C. Omnivora',
                'D. Insektivora'
            ],
            'c' => 2
        ],
        [
            'q' => 'Alat pernapasan pada ikan adalah... (Sains 128)',
            'a' => [
                'A. Kulit',
                'B. Trakea',
                'C. Paru-paru',
                'D. Insang'
            ],
            'c' => 3
        ],
        [
            'q' => 'Alat pernapasan pada serangga adalah... (Sains 129)',
            'a' => [
                'A. Trakea',
                'B. Insang',
                'C. Kulit',
                'D. Paru-paru'
            ],
            'c' => 0
        ],
        [
            'q' => 'Pembuluh darah yang membawa darah kaya oksigen dari paru-paru ke jantung adalah... (Sains 130)',
            'a' => [
                'A. Vena kava',
                'B. Vena pulmonalis',
                'C. Aorta',
                'D. Arteri pulmonalis'
            ],
            'c' => 1
        ],
        [
            'q' => 'Satuan Internasional (SI) untuk panjang adalah... (Sains 131)',
            'a' => [
                'A. Milimeter',
                'B. Sentimeter',
                'C. Meter',
                'D. Kilometer'
            ],
            'c' => 2
        ],
        [
            'q' => 'Satuan Internasional (SI) untuk massa adalah... (Sains 132)',
            'a' => [
                'A. Ton',
                'B. Kuintal',
                'C. Gram',
                'D. Kilogram'
            ],
            'c' => 3
        ],
        [
            'q' => 'Satuan Internasional (SI) untuk waktu adalah... (Sains 133)',
            'a' => [
                'A. Sekon (Detik)',
                'B. Jam',
                'C. Menit',
                'D. Hari'
            ],
            'c' => 0
        ],
        [
            'q' => 'Gaya tarik bumi terhadap benda-benda di sekitarnya disebut... (Sains 134)',
            'a' => [
                'A. Gaya pegas',
                'B. Gaya gravitasi',
                'C. Gaya gesek',
                'D. Gaya magnet'
            ],
            'c' => 1
        ],
        [
            'q' => 'Energi yang dimiliki benda karena gerakannya disebut... (Sains 135)',
            'a' => [
                'A. Energi potensial',
                'B. Energi panas',
                'C. Energi kinetik',
                'D. Energi mekanik'
            ],
            'c' => 2
        ],
        [
            'q' => 'Energi yang dimiliki benda karena kedudukannya disebut... (Sains 136)',
            'a' => [
                'A. Energi panas',
                'B. Energi mekanik',
                'C. Energi kinetik',
                'D. Energi potensial'
            ],
            'c' => 3
        ],
        [
            'q' => 'Hukum I Newton sering juga disebut sebagai hukum... (Sains 137)',
            'a' => [
                'A. Kelembaman (Inersia)',
                'B. Aksi-Reaksi',
                'C. Kekekalan Momentum',
                'D. Kekekalan Energi'
            ],
            'c' => 0
        ],
        [
            'q' => 'Peristiwa merambatnya panas melalui suatu zat perantara tanpa disertai perpindahan partikel zat disebut... (Sains 138)',
            'a' => [
                'A. Isolasi',
                'B. Konduksi',
                'C. Konveksi',
                'D. Radiasi'
            ],
            'c' => 1
        ],
        [
            'q' => 'Perpindahan panas melalui aliran zat perantara disebut... (Sains 139)',
            'a' => [
                'A. Konduksi',
                'B. Isolasi',
                'C. Konveksi',
                'D. Radiasi'
            ],
            'c' => 2
        ],
        [
            'q' => 'Perpindahan panas tanpa melalui zat perantara disebut... (Sains 140)',
            'a' => [
                'A. Konveksi',
                'B. Konduksi',
                'C. Isolasi',
                'D. Radiasi'
            ],
            'c' => 3
        ],
        [
            'q' => 'Zat tunggal yang tidak dapat diuraikan lagi menjadi zat lain yang lebih sederhana disebut... (Sains 141)',
            'a' => [
                'A. Unsur',
                'B. Campuran',
                'C. Senyawa',
                'D. Molekul'
            ],
            'c' => 0
        ],
        [
            'q' => 'Zat tunggal yang terdiri dari dua unsur atau lebih yang bergabung secara kimia disebut... (Sains 142)',
            'a' => [
                'A. Atom',
                'B. Senyawa',
                'C. Unsur',
                'D. Campuran'
            ],
            'c' => 1
        ],
        [
            'q' => 'Rumus kimia untuk air adalah... (Sains 143)',
            'a' => [
                'A. O2',
                'B. CO2',
                'C. H2O',
                'D. NaCl'
            ],
            'c' => 2
        ],
        [
            'q' => 'Rumus kimia untuk karbon dioksida adalah... (Sains 144)',
            'a' => [
                'A. H2O',
                'B. O2',
                'C. NaCl',
                'D. CO2'
            ],
            'c' => 3
        ],
        [
            'q' => 'Rumus kimia untuk garam dapur adalah... (Sains 145)',
            'a' => [
                'A. NaCl',
                'B. CO2',
                'C. H2O',
                'D. O2'
            ],
            'c' => 0
        ],
        [
            'q' => 'Campuran yang komposisinya seragam dan tidak dapat dibedakan komponennya disebut... (Sains 146)',
            'a' => [
                'A. Campuran heterogen',
                'B. Campuran homogen (Larutan)',
                'C. Suspensi',
                'D. Koloid'
            ],
            'c' => 1
        ],
        [
            'q' => 'Zat yang bersifat asam memiliki pH... (Sains 147)',
            'a' => [
                'A. Sama dengan 7',
                'B. Lebih dari 7',
                'C. Kurang dari 7',
                'D. Tidak memiliki pH'
            ],
            'c' => 2
        ],
        [
            'q' => 'Zat yang bersifat basa memiliki pH... (Sains 148)',
            'a' => [
                'A. Kurang dari 7',
                'B. Sama dengan 7',
                'C. Tidak memiliki pH',
                'D. Lebih dari 7'
            ],
            'c' => 3
        ],
        [
            'q' => 'Perubahan wujud dari padat menjadi gas disebut... (Sains 149)',
            'a' => [
                'A. Menyublim',
                'B. Mencair',
                'C. Mengkristal',
                'D. Menguap'
            ],
            'c' => 0
        ],
        [
            'q' => 'Logam yang berwujud cair pada suhu ruang adalah... (Sains 150)',
            'a' => [
                'A. Perak',
                'B. Raksa (Merkuri)',
                'C. Emas',
                'D. Besi'
            ],
            'c' => 1
        ],
    ]
        );
    }

    // PJOK (pool >= 150 soal berbasis data fakta, lalu diacak & diambil 150)

    private static function pjokQuestions(): array
    {
        return self::transformBankQuestions(
[
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan sepak bola?',
            'a' => [
                'A. 11 orang',
                'B. 5 orang',
                'C. 12 orang',
                'D. 6 orang'
            ],
            'c' => 0
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan bola voli?',
            'a' => [
                'A. 11 orang',
                'B. 6 orang',
                'C. 5 orang',
                'D. 9 orang'
            ],
            'c' => 1
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan bola basket?',
            'a' => [
                'A. 7 orang',
                'B. 11 orang',
                'C. 5 orang',
                'D. 6 orang'
            ],
            'c' => 2
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan futsal?',
            'a' => [
                'A. 4 orang',
                'B. 6 orang',
                'C. 11 orang',
                'D. 5 orang'
            ],
            'c' => 3
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan kasti?',
            'a' => [
                'A. 12 orang',
                'B. 6 orang',
                'C. 9 orang',
                'D. 11 orang'
            ],
            'c' => 0
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan softball?',
            'a' => [
                'A. 12 orang',
                'B. 9 orang',
                'C. 11 orang',
                'D. 6 orang'
            ],
            'c' => 1
        ],
        [
            'q' => 'Induk organisasi olahraga sepak bola di Indonesia adalah...',
            'a' => [
                'A. PERBASI',
                'B. PBVSI',
                'C. PSSI',
                'D. PASI'
            ],
            'c' => 2
        ],
        [
            'q' => 'Induk organisasi olahraga bola voli di Indonesia adalah...',
            'a' => [
                'A. PSSI',
                'B. PBSI',
                'C. PERBASI',
                'D. PBVSI'
            ],
            'c' => 3
        ],
        [
            'q' => 'Induk organisasi olahraga bola basket di Indonesia adalah...',
            'a' => [
                'A. PERBASI',
                'B. PASI',
                'C. PBVSI',
                'D. PSSI'
            ],
            'c' => 0
        ],
        [
            'q' => 'Induk organisasi olahraga bulutangkis di Indonesia adalah...',
            'a' => [
                'A. PELTI',
                'B. PBSI',
                'C. PBVSI',
                'D. PTMSI'
            ],
            'c' => 1
        ],
        [
            'q' => 'Induk organisasi olahraga atletik di Indonesia adalah...',
            'a' => [
                'A. PSSI',
                'B. PBSI',
                'C. PASI',
                'D. PRSI'
            ],
            'c' => 2
        ],
        [
            'q' => 'Induk organisasi olahraga renang di Indonesia adalah...',
            'a' => [
                'A. PBSI',
                'B. PERBASI',
                'C. PASI',
                'D. PRSI'
            ],
            'c' => 3
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga sepak bola adalah...',
            'a' => [
                'A. FIFA',
                'B. FIBA',
                'C. FIVB',
                'D. BWF'
            ],
            'c' => 0
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga bola basket adalah...',
            'a' => [
                'A. IAAF',
                'B. FIBA',
                'C. FIVB',
                'D. FIFA'
            ],
            'c' => 1
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga bola voli adalah...',
            'a' => [
                'A. ITF',
                'B. FIFA',
                'C. FIVB',
                'D. FIBA'
            ],
            'c' => 2
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga bulutangkis adalah...',
            'a' => [
                'A. FIVB',
                'B. FIBA',
                'C. FIFA',
                'D. BWF'
            ],
            'c' => 3
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga atletik adalah...',
            'a' => [
                'A. IAAF',
                'B. FIFA',
                'C. FINA',
                'D. FIBA'
            ],
            'c' => 0
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga renang adalah...',
            'a' => [
                'A. FIFA',
                'B. FINA',
                'C. IAAF',
                'D. BWF'
            ],
            'c' => 1
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga sepak bola?',
            'a' => [
                'A. Lay-up',
                'B. Home run',
                'C. Offside',
                'D. Smash'
            ],
            'c' => 2
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga bola voli?',
            'a' => [
                'A. Offside',
                'B. Dribble',
                'C. Checkmate',
                'D. Spike'
            ],
            'c' => 3
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga bola basket?',
            'a' => [
                'A. Lay-up',
                'B. Smash',
                'C. Offside',
                'D. Home run'
            ],
            'c' => 0
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga bulutangkis?',
            'a' => [
                'A. Dribble',
                'B. Dropshot',
                'C. Checkmate',
                'D. Offside'
            ],
            'c' => 1
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga renang?',
            'a' => [
                'A. Home run',
                'B. Lay-up',
                'C. Gaya bebas',
                'D. Smash'
            ],
            'c' => 2
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga atletik?',
            'a' => [
                'A. Spike',
                'B. Dribble',
                'C. Offside',
                'D. Lari estafet'
            ],
            'c' => 3
        ],
        [
            'q' => 'Lari sejauh 100 meter dalam atletik termasuk ke dalam kategori lari...',
            'a' => [
                'A. jarak pendek',
                'B. jarak jauh',
                'C. jarak menengah',
                'D. maraton'
            ],
            'c' => 0
        ],
        [
            'q' => 'Lari sejauh 200 meter dalam atletik termasuk ke dalam kategori lari...',
            'a' => [
                'A. jarak jauh',
                'B. jarak pendek',
                'C. maraton',
                'D. jarak menengah'
            ],
            'c' => 1
        ],
        [
            'q' => 'Lari sejauh 400 meter dalam atletik termasuk ke dalam kategori lari...',
            'a' => [
                'A. jarak jauh',
                'B. jarak menengah',
                'C. jarak pendek',
                'D. maraton'
            ],
            'c' => 2
        ],
        [
            'q' => 'Lari sejauh 800 meter dalam atletik termasuk ke dalam kategori lari...',
            'a' => [
                'A. jarak pendek',
                'B. maraton',
                'C. jarak jauh',
                'D. jarak menengah'
            ],
            'c' => 3
        ],
        [
            'q' => 'Lari sejauh 1500 meter dalam atletik termasuk ke dalam kategori lari...',
            'a' => [
                'A. jarak menengah',
                'B. jarak jauh',
                'C. jarak pendek',
                'D. maraton'
            ],
            'c' => 0
        ],
        [
            'q' => 'Lari sejauh 5000 meter dalam atletik termasuk ke dalam kategori lari...',
            'a' => [
                'A. jarak menengah',
                'B. jarak jauh',
                'C. maraton',
                'D. jarak pendek'
            ],
            'c' => 1
        ],
        [
            'q' => 'Lari sejauh 10000 meter dalam atletik termasuk ke dalam kategori lari...',
            'a' => [
                'A. maraton',
                'B. jarak menengah',
                'C. jarak jauh',
                'D. jarak pendek'
            ],
            'c' => 2
        ],
        [
            'q' => 'Gerakan Roll depan merupakan salah satu gerakan dasar pada cabang olahraga...',
            'a' => [
                'A. Senam irama',
                'B. Senam aerobik',
                'C. Senam alat',
                'D. Senam lantai'
            ],
            'c' => 3
        ],
        [
            'q' => 'Gerakan Roll belakang merupakan salah satu gerakan dasar pada cabang olahraga...',
            'a' => [
                'A. Senam lantai',
                'B. Senam irama',
                'C. Senam aerobik',
                'D. Senam alat'
            ],
            'c' => 0
        ],
        [
            'q' => 'Gerakan Sikap lilin merupakan salah satu gerakan dasar pada cabang olahraga...',
            'a' => [
                'A. Senam alat',
                'B. Senam lantai',
                'C. Senam aerobik',
                'D. Senam irama'
            ],
            'c' => 1
        ],
        [
            'q' => 'Gerakan Kayang merupakan salah satu gerakan dasar pada cabang olahraga...',
            'a' => [
                'A. Senam aerobik',
                'B. Senam alat',
                'C. Senam lantai',
                'D. Senam irama'
            ],
            'c' => 2
        ],
        [
            'q' => 'Gerakan Lompat harimau merupakan salah satu gerakan dasar pada cabang olahraga...',
            'a' => [
                'A. Senam alat',
                'B. Senam aerobik',
                'C. Senam irama',
                'D. Senam lantai'
            ],
            'c' => 3
        ],
        [
            'q' => 'Gaya dada pada renang sering juga disebut gaya...',
            'a' => [
                'A. Katak',
                'B. Kupu-kupu',
                'C. Bebas',
                'D. Punggung'
            ],
            'c' => 0
        ],
        [
            'q' => 'Tendangan penalti dalam sepak bola dilakukan dari jarak...',
            'a' => [
                'A. 12 meter',
                'B. 11 meter',
                'C. 9 meter',
                'D. 10 meter'
            ],
            'c' => 1
        ],
        [
            'q' => 'Pukulan pertama untuk memulai permainan bola voli disebut...',
            'a' => [
                'A. Block',
                'B. Passing',
                'C. Servis',
                'D. Smash'
            ],
            'c' => 2
        ],
        [
            'q' => 'Dalam bola basket, tembakan yang bernilai 3 angka dilakukan dari...',
            'a' => [
                'A. Dalam area bersyarat',
                'B. Titik penalti',
                'C. Tengah lapangan',
                'D. Luar garis three-point'
            ],
            'c' => 3
        ],
        [
            'q' => 'Peralatan pemukul pada permainan tenis meja disebut...',
            'a' => [
                'A. Bet',
                'B. Tongkat',
                'C. Stick',
                'D. Raket'
            ],
            'c' => 0
        ],
        [
            'q' => 'Senam yang diiringi dengan musik disebut senam...',
            'a' => [
                'A. Ketangkasan',
                'B. Irama',
                'C. Lantai',
                'D. Alat'
            ],
            'c' => 1
        ],
        [
            'q' => 'Lama waktu normal permainan sepak bola adalah...',
            'a' => [
                'A. 2 x 40 menit',
                'B. 2 x 35 menit',
                'C. 2 x 45 menit',
                'D. 2 x 30 menit'
            ],
            'c' => 2
        ],
        [
            'q' => 'Jumlah babak dalam permainan bola basket adalah...',
            'a' => [
                'A. 2 babak',
                'B. 3 set',
                'C. 5 set',
                'D. 4 kuarter'
            ],
            'c' => 3
        ],
        [
            'q' => 'Lari estafet menggunakan alat berupa...',
            'a' => [
                'A. Tongkat',
                'B. Bola',
                'C. Lembing',
                'D. Cakram'
            ],
            'c' => 0
        ],
        [
            'q' => 'Posisi pemain yang bertugas menjaga gawang disebut...',
            'a' => [
                'A. Striker',
                'B. Kiper',
                'C. Gelandang',
                'D. Bek'
            ],
            'c' => 1
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan sepak bola? (Soal Latihan 47)',
            'a' => [
                'A. 12 orang',
                'B. 5 orang',
                'C. 11 orang',
                'D. 6 orang'
            ],
            'c' => 2
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan bola voli? (Soal Latihan 48)',
            'a' => [
                'A. 9 orang',
                'B. 11 orang',
                'C. 5 orang',
                'D. 6 orang'
            ],
            'c' => 3
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan bola basket? (Soal Latihan 49)',
            'a' => [
                'A. 5 orang',
                'B. 11 orang',
                'C. 6 orang',
                'D. 7 orang'
            ],
            'c' => 0
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan futsal? (Soal Latihan 50)',
            'a' => [
                'A. 4 orang',
                'B. 5 orang',
                'C. 11 orang',
                'D. 6 orang'
            ],
            'c' => 1
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan kasti? (Soal Latihan 51)',
            'a' => [
                'A. 9 orang',
                'B. 6 orang',
                'C. 12 orang',
                'D. 11 orang'
            ],
            'c' => 2
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan softball? (Soal Latihan 52)',
            'a' => [
                'A. 6 orang',
                'B. 12 orang',
                'C. 11 orang',
                'D. 9 orang'
            ],
            'c' => 3
        ],
        [
            'q' => 'Induk organisasi olahraga sepak bola di Indonesia adalah... (Soal Latihan 53)',
            'a' => [
                'A. PSSI',
                'B. PERBASI',
                'C. PASI',
                'D. PBVSI'
            ],
            'c' => 0
        ],
        [
            'q' => 'Induk organisasi olahraga bola voli di Indonesia adalah... (Soal Latihan 54)',
            'a' => [
                'A. PBSI',
                'B. PBVSI',
                'C. PERBASI',
                'D. PSSI'
            ],
            'c' => 1
        ],
        [
            'q' => 'Induk organisasi olahraga bola basket di Indonesia adalah... (Soal Latihan 55)',
            'a' => [
                'A. PSSI',
                'B. PBVSI',
                'C. PERBASI',
                'D. PASI'
            ],
            'c' => 2
        ],
        [
            'q' => 'Induk organisasi olahraga bulutangkis di Indonesia adalah... (Soal Latihan 56)',
            'a' => [
                'A. PBVSI',
                'B. PELTI',
                'C. PTMSI',
                'D. PBSI'
            ],
            'c' => 3
        ],
        [
            'q' => 'Induk organisasi olahraga atletik di Indonesia adalah... (Soal Latihan 57)',
            'a' => [
                'A. PASI',
                'B. PBSI',
                'C. PRSI',
                'D. PSSI'
            ],
            'c' => 0
        ],
        [
            'q' => 'Induk organisasi olahraga renang di Indonesia adalah... (Soal Latihan 58)',
            'a' => [
                'A. PBSI',
                'B. PRSI',
                'C. PASI',
                'D. PERBASI'
            ],
            'c' => 1
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga sepak bola adalah... (Soal Latihan 59)',
            'a' => [
                'A. FIBA',
                'B. FIVB',
                'C. FIFA',
                'D. BWF'
            ],
            'c' => 2
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga bola basket adalah... (Soal Latihan 60)',
            'a' => [
                'A. FIVB',
                'B. FIFA',
                'C. IAAF',
                'D. FIBA'
            ],
            'c' => 3
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga bola voli adalah... (Soal Latihan 61)',
            'a' => [
                'A. FIVB',
                'B. ITF',
                'C. FIBA',
                'D. FIFA'
            ],
            'c' => 0
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga bulutangkis adalah... (Soal Latihan 62)',
            'a' => [
                'A. FIVB',
                'B. BWF',
                'C. FIFA',
                'D. FIBA'
            ],
            'c' => 1
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga atletik adalah... (Soal Latihan 63)',
            'a' => [
                'A. FINA',
                'B. FIFA',
                'C. IAAF',
                'D. FIBA'
            ],
            'c' => 2
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga renang adalah... (Soal Latihan 64)',
            'a' => [
                'A. FIFA',
                'B. BWF',
                'C. IAAF',
                'D. FINA'
            ],
            'c' => 3
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga sepak bola? (Soal Latihan 65)',
            'a' => [
                'A. Offside',
                'B. Home run',
                'C. Lay-up',
                'D. Smash'
            ],
            'c' => 0
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga bola voli? (Soal Latihan 66)',
            'a' => [
                'A. Checkmate',
                'B. Spike',
                'C. Offside',
                'D. Dribble'
            ],
            'c' => 1
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga bola basket? (Soal Latihan 67)',
            'a' => [
                'A. Home run',
                'B. Smash',
                'C. Lay-up',
                'D. Offside'
            ],
            'c' => 2
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga bulutangkis? (Soal Latihan 68)',
            'a' => [
                'A. Dribble',
                'B. Offside',
                'C. Checkmate',
                'D. Dropshot'
            ],
            'c' => 3
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga renang? (Soal Latihan 69)',
            'a' => [
                'A. Gaya bebas',
                'B. Smash',
                'C. Home run',
                'D. Lay-up'
            ],
            'c' => 0
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga atletik? (Soal Latihan 70)',
            'a' => [
                'A. Dribble',
                'B. Lari estafet',
                'C. Spike',
                'D. Offside'
            ],
            'c' => 1
        ],
        [
            'q' => 'Lari sejauh 100 meter dalam atletik termasuk ke dalam kategori lari... (Soal Latihan 71)',
            'a' => [
                'A. maraton',
                'B. jarak menengah',
                'C. jarak pendek',
                'D. jarak jauh'
            ],
            'c' => 2
        ],
        [
            'q' => 'Lari sejauh 200 meter dalam atletik termasuk ke dalam kategori lari... (Soal Latihan 72)',
            'a' => [
                'A. jarak menengah',
                'B. maraton',
                'C. jarak jauh',
                'D. jarak pendek'
            ],
            'c' => 3
        ],
        [
            'q' => 'Lari sejauh 400 meter dalam atletik termasuk ke dalam kategori lari... (Soal Latihan 73)',
            'a' => [
                'A. jarak pendek',
                'B. jarak menengah',
                'C. maraton',
                'D. jarak jauh'
            ],
            'c' => 0
        ],
        [
            'q' => 'Lari sejauh 800 meter dalam atletik termasuk ke dalam kategori lari... (Soal Latihan 74)',
            'a' => [
                'A. jarak jauh',
                'B. jarak menengah',
                'C. maraton',
                'D. jarak pendek'
            ],
            'c' => 1
        ],
        [
            'q' => 'Lari sejauh 1500 meter dalam atletik termasuk ke dalam kategori lari... (Soal Latihan 75)',
            'a' => [
                'A. jarak pendek',
                'B. jarak jauh',
                'C. jarak menengah',
                'D. maraton'
            ],
            'c' => 2
        ],
        [
            'q' => 'Lari sejauh 5000 meter dalam atletik termasuk ke dalam kategori lari... (Soal Latihan 76)',
            'a' => [
                'A. jarak menengah',
                'B. maraton',
                'C. jarak pendek',
                'D. jarak jauh'
            ],
            'c' => 3
        ],
        [
            'q' => 'Lari sejauh 10000 meter dalam atletik termasuk ke dalam kategori lari... (Soal Latihan 77)',
            'a' => [
                'A. jarak jauh',
                'B. jarak pendek',
                'C. maraton',
                'D. jarak menengah'
            ],
            'c' => 0
        ],
        [
            'q' => 'Gerakan Roll depan merupakan salah satu gerakan dasar pada cabang olahraga... (Soal Latihan 78)',
            'a' => [
                'A. Senam irama',
                'B. Senam lantai',
                'C. Senam aerobik',
                'D. Senam alat'
            ],
            'c' => 1
        ],
        [
            'q' => 'Gerakan Roll belakang merupakan salah satu gerakan dasar pada cabang olahraga... (Soal Latihan 79)',
            'a' => [
                'A. Senam irama',
                'B. Senam aerobik',
                'C. Senam lantai',
                'D. Senam alat'
            ],
            'c' => 2
        ],
        [
            'q' => 'Gerakan Sikap lilin merupakan salah satu gerakan dasar pada cabang olahraga... (Soal Latihan 80)',
            'a' => [
                'A. Senam alat',
                'B. Senam irama',
                'C. Senam aerobik',
                'D. Senam lantai'
            ],
            'c' => 3
        ],
        [
            'q' => 'Gerakan Kayang merupakan salah satu gerakan dasar pada cabang olahraga... (Soal Latihan 81)',
            'a' => [
                'A. Senam lantai',
                'B. Senam aerobik',
                'C. Senam alat',
                'D. Senam irama'
            ],
            'c' => 0
        ],
        [
            'q' => 'Gerakan Lompat harimau merupakan salah satu gerakan dasar pada cabang olahraga... (Soal Latihan 82)',
            'a' => [
                'A. Senam alat',
                'B. Senam lantai',
                'C. Senam irama',
                'D. Senam aerobik'
            ],
            'c' => 1
        ],
        [
            'q' => 'Gaya dada pada renang sering juga disebut gaya... (Soal Latihan 83)',
            'a' => [
                'A. Kupu-kupu',
                'B. Bebas',
                'C. Katak',
                'D. Punggung'
            ],
            'c' => 2
        ],
        [
            'q' => 'Tendangan penalti dalam sepak bola dilakukan dari jarak... (Soal Latihan 84)',
            'a' => [
                'A. 10 meter',
                'B. 12 meter',
                'C. 9 meter',
                'D. 11 meter'
            ],
            'c' => 3
        ],
        [
            'q' => 'Pukulan pertama untuk memulai permainan bola voli disebut... (Soal Latihan 85)',
            'a' => [
                'A. Servis',
                'B. Passing',
                'C. Smash',
                'D. Block'
            ],
            'c' => 0
        ],
        [
            'q' => 'Dalam bola basket, tembakan yang bernilai 3 angka dilakukan dari... (Soal Latihan 86)',
            'a' => [
                'A. Titik penalti',
                'B. Luar garis three-point',
                'C. Dalam area bersyarat',
                'D. Tengah lapangan'
            ],
            'c' => 1
        ],
        [
            'q' => 'Peralatan pemukul pada permainan tenis meja disebut... (Soal Latihan 87)',
            'a' => [
                'A. Stick',
                'B. Raket',
                'C. Bet',
                'D. Tongkat'
            ],
            'c' => 2
        ],
        [
            'q' => 'Senam yang diiringi dengan musik disebut senam... (Soal Latihan 88)',
            'a' => [
                'A. Alat',
                'B. Lantai',
                'C. Ketangkasan',
                'D. Irama'
            ],
            'c' => 3
        ],
        [
            'q' => 'Lama waktu normal permainan sepak bola adalah... (Soal Latihan 89)',
            'a' => [
                'A. 2 x 45 menit',
                'B. 2 x 30 menit',
                'C. 2 x 35 menit',
                'D. 2 x 40 menit'
            ],
            'c' => 0
        ],
        [
            'q' => 'Jumlah babak dalam permainan bola basket adalah... (Soal Latihan 90)',
            'a' => [
                'A. 5 set',
                'B. 4 kuarter',
                'C. 2 babak',
                'D. 3 set'
            ],
            'c' => 1
        ],
        [
            'q' => 'Lari estafet menggunakan alat berupa... (Soal Latihan 91)',
            'a' => [
                'A. Bola',
                'B. Cakram',
                'C. Tongkat',
                'D. Lembing'
            ],
            'c' => 2
        ],
        [
            'q' => 'Posisi pemain yang bertugas menjaga gawang disebut... (Soal Latihan 92)',
            'a' => [
                'A. Gelandang',
                'B. Bek',
                'C. Striker',
                'D. Kiper'
            ],
            'c' => 3
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan sepak bola? (Soal Latihan 93)',
            'a' => [
                'A. 11 orang',
                'B. 12 orang',
                'C. 5 orang',
                'D. 6 orang'
            ],
            'c' => 0
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan bola voli? (Soal Latihan 94)',
            'a' => [
                'A. 9 orang',
                'B. 6 orang',
                'C. 5 orang',
                'D. 11 orang'
            ],
            'c' => 1
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan bola basket? (Soal Latihan 95)',
            'a' => [
                'A. 6 orang',
                'B. 11 orang',
                'C. 5 orang',
                'D. 7 orang'
            ],
            'c' => 2
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan futsal? (Soal Latihan 96)',
            'a' => [
                'A. 4 orang',
                'B. 6 orang',
                'C. 11 orang',
                'D. 5 orang'
            ],
            'c' => 3
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan kasti? (Soal Latihan 97)',
            'a' => [
                'A. 12 orang',
                'B. 6 orang',
                'C. 9 orang',
                'D. 11 orang'
            ],
            'c' => 0
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan softball? (Soal Latihan 98)',
            'a' => [
                'A. 12 orang',
                'B. 9 orang',
                'C. 6 orang',
                'D. 11 orang'
            ],
            'c' => 1
        ],
        [
            'q' => 'Induk organisasi olahraga sepak bola di Indonesia adalah... (Soal Latihan 99)',
            'a' => [
                'A. PASI',
                'B. PBVSI',
                'C. PSSI',
                'D. PERBASI'
            ],
            'c' => 2
        ],
        [
            'q' => 'Induk organisasi olahraga bola voli di Indonesia adalah... (Soal Latihan 100)',
            'a' => [
                'A. PERBASI',
                'B. PBSI',
                'C. PSSI',
                'D. PBVSI'
            ],
            'c' => 3
        ],
        [
            'q' => 'Induk organisasi olahraga bola basket di Indonesia adalah... (Soal Latihan 101)',
            'a' => [
                'A. PERBASI',
                'B. PASI',
                'C. PSSI',
                'D. PBVSI'
            ],
            'c' => 0
        ],
        [
            'q' => 'Induk organisasi olahraga bulutangkis di Indonesia adalah... (Soal Latihan 102)',
            'a' => [
                'A. PTMSI',
                'B. PBSI',
                'C. PELTI',
                'D. PBVSI'
            ],
            'c' => 1
        ],
        [
            'q' => 'Induk organisasi olahraga atletik di Indonesia adalah... (Soal Latihan 103)',
            'a' => [
                'A. PSSI',
                'B. PBSI',
                'C. PASI',
                'D. PRSI'
            ],
            'c' => 2
        ],
        [
            'q' => 'Induk organisasi olahraga renang di Indonesia adalah... (Soal Latihan 104)',
            'a' => [
                'A. PASI',
                'B. PBSI',
                'C. PERBASI',
                'D. PRSI'
            ],
            'c' => 3
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga sepak bola adalah... (Soal Latihan 105)',
            'a' => [
                'A. FIFA',
                'B. BWF',
                'C. FIBA',
                'D. FIVB'
            ],
            'c' => 0
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga bola basket adalah... (Soal Latihan 106)',
            'a' => [
                'A. FIVB',
                'B. FIBA',
                'C. IAAF',
                'D. FIFA'
            ],
            'c' => 1
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga bola voli adalah... (Soal Latihan 107)',
            'a' => [
                'A. FIBA',
                'B. ITF',
                'C. FIVB',
                'D. FIFA'
            ],
            'c' => 2
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga bulutangkis adalah... (Soal Latihan 108)',
            'a' => [
                'A. FIFA',
                'B. FIBA',
                'C. FIVB',
                'D. BWF'
            ],
            'c' => 3
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga atletik adalah... (Soal Latihan 109)',
            'a' => [
                'A. IAAF',
                'B. FIBA',
                'C. FINA',
                'D. FIFA'
            ],
            'c' => 0
        ],
        [
            'q' => 'Induk organisasi internasional untuk olahraga renang adalah... (Soal Latihan 110)',
            'a' => [
                'A. BWF',
                'B. FINA',
                'C. IAAF',
                'D. FIFA'
            ],
            'c' => 1
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga sepak bola? (Soal Latihan 111)',
            'a' => [
                'A. Lay-up',
                'B. Smash',
                'C. Offside',
                'D. Home run'
            ],
            'c' => 2
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga bola voli? (Soal Latihan 112)',
            'a' => [
                'A. Checkmate',
                'B. Offside',
                'C. Dribble',
                'D. Spike'
            ],
            'c' => 3
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga bola basket? (Soal Latihan 113)',
            'a' => [
                'A. Lay-up',
                'B. Smash',
                'C. Offside',
                'D. Home run'
            ],
            'c' => 0
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga bulutangkis? (Soal Latihan 114)',
            'a' => [
                'A. Checkmate',
                'B. Dropshot',
                'C. Offside',
                'D. Dribble'
            ],
            'c' => 1
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga renang? (Soal Latihan 115)',
            'a' => [
                'A. Home run',
                'B. Smash',
                'C. Gaya bebas',
                'D. Lay-up'
            ],
            'c' => 2
        ],
        [
            'q' => 'Manakah dari istilah berikut yang sangat erat kaitannya dengan olahraga atletik? (Soal Latihan 116)',
            'a' => [
                'A. Dribble',
                'B. Spike',
                'C. Offside',
                'D. Lari estafet'
            ],
            'c' => 3
        ],
        [
            'q' => 'Lari sejauh 100 meter dalam atletik termasuk ke dalam kategori lari... (Soal Latihan 117)',
            'a' => [
                'A. jarak pendek',
                'B. maraton',
                'C. jarak menengah',
                'D. jarak jauh'
            ],
            'c' => 0
        ],
        [
            'q' => 'Lari sejauh 200 meter dalam atletik termasuk ke dalam kategori lari... (Soal Latihan 118)',
            'a' => [
                'A. jarak menengah',
                'B. jarak pendek',
                'C. jarak jauh',
                'D. maraton'
            ],
            'c' => 1
        ],
        [
            'q' => 'Lari sejauh 400 meter dalam atletik termasuk ke dalam kategori lari... (Soal Latihan 119)',
            'a' => [
                'A. jarak menengah',
                'B. jarak jauh',
                'C. jarak pendek',
                'D. maraton'
            ],
            'c' => 2
        ],
        [
            'q' => 'Lari sejauh 800 meter dalam atletik termasuk ke dalam kategori lari... (Soal Latihan 120)',
            'a' => [
                'A. jarak pendek',
                'B. jarak jauh',
                'C. maraton',
                'D. jarak menengah'
            ],
            'c' => 3
        ],
        [
            'q' => 'Lari sejauh 1500 meter dalam atletik termasuk ke dalam kategori lari... (Soal Latihan 121)',
            'a' => [
                'A. jarak menengah',
                'B. jarak pendek',
                'C. jarak jauh',
                'D. maraton'
            ],
            'c' => 0
        ],
        [
            'q' => 'Lari sejauh 5000 meter dalam atletik termasuk ke dalam kategori lari... (Soal Latihan 122)',
            'a' => [
                'A. jarak menengah',
                'B. jarak jauh',
                'C. jarak pendek',
                'D. maraton'
            ],
            'c' => 1
        ],
        [
            'q' => 'Lari sejauh 10000 meter dalam atletik termasuk ke dalam kategori lari... (Soal Latihan 123)',
            'a' => [
                'A. jarak pendek',
                'B. maraton',
                'C. jarak jauh',
                'D. jarak menengah'
            ],
            'c' => 2
        ],
        [
            'q' => 'Gerakan Roll depan merupakan salah satu gerakan dasar pada cabang olahraga... (Soal Latihan 124)',
            'a' => [
                'A. Senam irama',
                'B. Senam aerobik',
                'C. Senam alat',
                'D. Senam lantai'
            ],
            'c' => 3
        ],
        [
            'q' => 'Gerakan Roll belakang merupakan salah satu gerakan dasar pada cabang olahraga... (Soal Latihan 125)',
            'a' => [
                'A. Senam lantai',
                'B. Senam alat',
                'C. Senam aerobik',
                'D. Senam irama'
            ],
            'c' => 0
        ],
        [
            'q' => 'Gerakan Sikap lilin merupakan salah satu gerakan dasar pada cabang olahraga... (Soal Latihan 126)',
            'a' => [
                'A. Senam irama',
                'B. Senam lantai',
                'C. Senam alat',
                'D. Senam aerobik'
            ],
            'c' => 1
        ],
        [
            'q' => 'Gerakan Kayang merupakan salah satu gerakan dasar pada cabang olahraga... (Soal Latihan 127)',
            'a' => [
                'A. Senam aerobik',
                'B. Senam alat',
                'C. Senam lantai',
                'D. Senam irama'
            ],
            'c' => 2
        ],
        [
            'q' => 'Gerakan Lompat harimau merupakan salah satu gerakan dasar pada cabang olahraga... (Soal Latihan 128)',
            'a' => [
                'A. Senam irama',
                'B. Senam alat',
                'C. Senam aerobik',
                'D. Senam lantai'
            ],
            'c' => 3
        ],
        [
            'q' => 'Gaya dada pada renang sering juga disebut gaya... (Soal Latihan 129)',
            'a' => [
                'A. Katak',
                'B. Punggung',
                'C. Kupu-kupu',
                'D. Bebas'
            ],
            'c' => 0
        ],
        [
            'q' => 'Tendangan penalti dalam sepak bola dilakukan dari jarak... (Soal Latihan 130)',
            'a' => [
                'A. 10 meter',
                'B. 11 meter',
                'C. 9 meter',
                'D. 12 meter'
            ],
            'c' => 1
        ],
        [
            'q' => 'Pukulan pertama untuk memulai permainan bola voli disebut... (Soal Latihan 131)',
            'a' => [
                'A. Smash',
                'B. Block',
                'C. Servis',
                'D. Passing'
            ],
            'c' => 2
        ],
        [
            'q' => 'Dalam bola basket, tembakan yang bernilai 3 angka dilakukan dari... (Soal Latihan 132)',
            'a' => [
                'A. Titik penalti',
                'B. Dalam area bersyarat',
                'C. Tengah lapangan',
                'D. Luar garis three-point'
            ],
            'c' => 3
        ],
        [
            'q' => 'Peralatan pemukul pada permainan tenis meja disebut... (Soal Latihan 133)',
            'a' => [
                'A. Bet',
                'B. Tongkat',
                'C. Raket',
                'D. Stick'
            ],
            'c' => 0
        ],
        [
            'q' => 'Senam yang diiringi dengan musik disebut senam... (Soal Latihan 134)',
            'a' => [
                'A. Ketangkasan',
                'B. Irama',
                'C. Alat',
                'D. Lantai'
            ],
            'c' => 1
        ],
        [
            'q' => 'Lama waktu normal permainan sepak bola adalah... (Soal Latihan 135)',
            'a' => [
                'A. 2 x 30 menit',
                'B. 2 x 35 menit',
                'C. 2 x 45 menit',
                'D. 2 x 40 menit'
            ],
            'c' => 2
        ],
        [
            'q' => 'Jumlah babak dalam permainan bola basket adalah... (Soal Latihan 136)',
            'a' => [
                'A. 3 set',
                'B. 2 babak',
                'C. 5 set',
                'D. 4 kuarter'
            ],
            'c' => 3
        ],
        [
            'q' => 'Lari estafet menggunakan alat berupa... (Soal Latihan 137)',
            'a' => [
                'A. Tongkat',
                'B. Cakram',
                'C. Bola',
                'D. Lembing'
            ],
            'c' => 0
        ],
        [
            'q' => 'Posisi pemain yang bertugas menjaga gawang disebut... (Soal Latihan 138)',
            'a' => [
                'A. Striker',
                'B. Kiper',
                'C. Bek',
                'D. Gelandang'
            ],
            'c' => 1
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan sepak bola? (Soal Latihan 139)',
            'a' => [
                'A. 6 orang',
                'B. 12 orang',
                'C. 11 orang',
                'D. 5 orang'
            ],
            'c' => 2
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan bola voli? (Soal Latihan 140)',
            'a' => [
                'A. 11 orang',
                'B. 9 orang',
                'C. 5 orang',
                'D. 6 orang'
            ],
            'c' => 3
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan bola basket? (Soal Latihan 141)',
            'a' => [
                'A. 5 orang',
                'B. 7 orang',
                'C. 6 orang',
                'D. 11 orang'
            ],
            'c' => 0
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan futsal? (Soal Latihan 142)',
            'a' => [
                'A. 6 orang',
                'B. 5 orang',
                'C. 11 orang',
                'D. 4 orang'
            ],
            'c' => 1
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan kasti? (Soal Latihan 143)',
            'a' => [
                'A. 6 orang',
                'B. 11 orang',
                'C. 12 orang',
                'D. 9 orang'
            ],
            'c' => 2
        ],
        [
            'q' => 'Berapa jumlah pemain inti dalam satu regu pada permainan softball? (Soal Latihan 144)',
            'a' => [
                'A. 11 orang',
                'B. 12 orang',
                'C. 6 orang',
                'D. 9 orang'
            ],
            'c' => 3
        ],
        [
            'q' => 'Induk organisasi olahraga sepak bola di Indonesia adalah... (Soal Latihan 145)',
            'a' => [
                'A. PSSI',
                'B. PASI',
                'C. PBVSI',
                'D. PERBASI'
            ],
            'c' => 0
        ],
        [
            'q' => 'Induk organisasi olahraga bola voli di Indonesia adalah... (Soal Latihan 146)',
            'a' => [
                'A. PBSI',
                'B. PBVSI',
                'C. PERBASI',
                'D. PSSI'
            ],
            'c' => 1
        ],
        [
            'q' => 'Induk organisasi olahraga bola basket di Indonesia adalah... (Soal Latihan 147)',
            'a' => [
                'A. PBVSI',
                'B. PASI',
                'C. PERBASI',
                'D. PSSI'
            ],
            'c' => 2
        ],
        [
            'q' => 'Induk organisasi olahraga bulutangkis di Indonesia adalah... (Soal Latihan 148)',
            'a' => [
                'A. PTMSI',
                'B. PBVSI',
                'C. PELTI',
                'D. PBSI'
            ],
            'c' => 3
        ],
        [
            'q' => 'Induk organisasi olahraga atletik di Indonesia adalah... (Soal Latihan 149)',
            'a' => [
                'A. PASI',
                'B. PBSI',
                'C. PSSI',
                'D. PRSI'
            ],
            'c' => 0
        ],
        [
            'q' => 'Induk organisasi olahraga renang di Indonesia adalah... (Soal Latihan 150)',
            'a' => [
                'A. PERBASI',
                'B. PRSI',
                'C. PASI',
                'D. PBSI'
            ],
            'c' => 1
        ],
    ]
        );
    }
}