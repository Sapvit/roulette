<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['history'])) $_SESSION['history'] = [];
if (!isset($_GET['sort'])) $_GET['sort'] = 'num'; // Сортировка по умолчанию

if (isset($_POST['number']) && $_POST['number'] !== '') {
    $num = (int)$_POST['number'];
    if ($num >= 0 && $num <= 36) array_unshift($_SESSION['history'], $num);
}
if (isset($_POST['clear'])) $_SESSION['history'] = [];

// Получаем данные
$probabilities = calculateProbabilities($_SESSION['history'], $groups);
$numberStats = getIndividualNumbersStats($_SESSION['history']);

// Логика сортировки чисел
if ($_GET['sort'] == 'prob') {
    usort($numberStats, function($a, $b) { return $a['prob'] <=> $b['prob']; });
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Roulette Analytics PRO</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="main-layout">
    <div class="panel">
        <h2>ВВОД ЧИСЛА</h2>
        <form method="POST">
            <input type="number" name="number" min="0" max="36" autofocus required>
            <button type="submit" class="btn btn-add">ДОБАВИТЬ (+)</button>
        </form>
        <form method="POST">
            <button type="submit" name="clear" class="btn btn-clear">СБРОС</button>
        </form>
        
        <div style="margin-top:20px;">
            <h3>ИСТОРИЯ</h3>
            <?php foreach (array_slice($_SESSION['history'], 0, 10) as $val): ?>
                <div class="history-item"><?php echo $val; ?></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="category-grid">
        <?php 
        // Группируем данные для вывода по блокам
        $displayGroups = [
            'Цвета' => ['Red', 'Black', 'Zero'],
            'Четность' => ['Even', 'Odd'],
            'Половины' => ['1-18', '19-36'],
            'Дюжины' => ['1st Dozen', '2nd Dozen', '3rd Dozen']
        ];

        foreach ($displayGroups as $title => $subKeys): ?>
            <div class="panel stat-group">
                <h2><?php echo $title; ?></h2>
                <?php foreach ($probabilities as $p): 
                    if (in_array($p['name'], $subKeys)): ?>
                    <div class="stat-row">
                        <span><?php echo $p['name']; ?> (S:<?php echo $p['streak']; ?>)</span>
                        <span class="val-prob"><?php echo $p['prob']; ?>%</span>
                    </div>
                <?php endif; endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="panel">
        <h2>ВСЕ ЧИСЛА (0-36)</h2>
        <div style="margin-bottom: 10px; font-size: 0.8rem;">
            Сортировка: 
            <a href="?sort=num" style="color:<?php echo $_GET['sort']=='num'?'#fff':'#888'?>">По числу</a> | 
            <a href="?sort=prob" style="color:<?php echo $_GET['sort']=='prob'?'#fff':'#888'?>">По вероятности</a>
        </div>
        <div style="max-height: 80vh; overflow-y: auto;">
            <table class="numbers-table">
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Серия</th>
                        <th>Вероятность %</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($numberStats as $s): ?>
                        <tr>
                            <td><span class="num-badge"><?php echo $s['number']; ?></span></td>
                            <td><?php echo $s['streak']; ?></td>
                            <td class="val-prob"><?php echo $s['prob']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>