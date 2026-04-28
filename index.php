<?php
session_start();
require_once 'functions.php';

// Инициализация истории
if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}

// Обработка ввода
if (isset($_POST['number']) && $_POST['number'] !== '') {
    $num = (int)$_POST['number'];
    if ($num >= 0 && $num <= 36) {
        array_unshift($_SESSION['history'], $num); // Добавляем в начало
    }
}

// Очистка
if (isset($_POST['clear'])) {
    $_SESSION['history'] = [];
}

$probabilities = calculateProbabilities($_SESSION['history'], $groups);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Roulette Stat Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="input-section">
            <h2>Ввод числа</h2>
            <form method="POST">
                <input type="number" name="number" min="0" max="36" autofocus required>
                <button type="submit">Добавить (+)</button>
            </form>
            
            <form method="POST" style="margin-top: 10px;">
                <button type="submit" name="clear" style="background: #dc3545;">Очистить всё</button>
            </form>

            <div class="history-list">
                <h3>История:</h3>
                <?php foreach ($_SESSION['history'] as $val): ?>
                    <div class="history-item"><?php echo $val; ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="results-section">
            <h2>Вероятность продолжения серии (%)</h2>
            <p><small>Чем меньше %, тем меньше шанс, что событие повторится еще раз подряд.</small></p>
            
            <?php foreach ($probabilities as $item): ?>
                <div class="stat-card">
                    <span>
                        <strong><?php echo $item['name']; ?></strong> 
                        <small>(<?php echo $item['category']; ?>)</small>
                    </span>
                    <span>
                        Серия: <?php echo $item['streak']; ?> | 
                        След. шаг: <span class="prob-low"><?php echo $item['prob']; ?>%</span>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>