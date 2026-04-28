<?php
// Группы чисел рулетки
$groups = [
    'Colors' => [
        'Red' => [1,3,5,7,9,12,14,16,18,19,21,23,25,27,30,32,34,36],
        'Black' => [2,4,6,8,10,11,13,15,17,20,22,24,26,28,29,31,33,35],
        'Zero' => [0]
    ],
    'Parity' => [
        'Even' => range(2, 36, 2),
        'Odd' => range(1, 35, 2)
    ],
    'Ranges' => [
        '1-18' => range(1, 18),
        '19-36' => range(19, 36)
    ],
    'Dozens' => [
        '1st Dozen' => range(1, 12),
        '2nd Dozen' => range(13, 24),
        '3rd Dozen' => range(25, 36)
    ]
];

function calculateProbabilities($history, $groups) {
    $stats = [];
    $totalSpins = count($history);

    foreach ($groups as $category => $subgroups) {
        foreach ($subgroups as $name => $numbers) {
            // Считаем текущую серию (сколько раз подряд это событие идет сейчас)
            $currentStreak = 0;
            foreach ($history as $num) {
                if (in_array($num, $numbers)) {
                    $currentStreak++;
                } else {
                    break; 
                }
            }

            // Вероятность события (p)
            $p = count($numbers) / 37;
            
            // Вероятность, что серия из (Streak + 1) произойдет
            // Формула: p ^ (n + 1) * 100
            $probNext = pow($p, $currentStreak + 1) * 100;

            $stats[] = [
                'category' => $category,
                'name' => $name,
                'streak' => $currentStreak,
                'prob' => round($probNext, 4)
            ];
        }
    }

    // Сортировка: чем меньше вероятность продолжения серии, тем "интереснее" нам это событие
    usort($stats, function($a, $b) {
        return $a['prob'] <=> $b['prob'];
    });

    return $stats;
}
