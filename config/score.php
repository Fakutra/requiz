<?php
return [
    // Maksimum masing-masing komponen
    'max' => [
        'quiz'      => 150, // MCQ + Essay (mis: MCQ 100 + Essay 50)
        'practice'  => 300, // Technical Test
        'interview' => 10,  // Rata-rata 4 poin (1-10)
    ],

    // Bobot komposit final (jumlah = 1.0)
    // CATATAN: Kalau mau diganti, cukup ubah di sini.
    'weights' => [
        'quiz'      => 0.35,
        'practice'  => 0.50,
        'interview' => 0.15,
    ],
];
