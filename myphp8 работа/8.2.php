<?php
function showCalendar($m = null, $y = null, $special = []): void
{
    $m = $m ?? (int)date('n');
    $y = $y ?? (int)date('Y');
    
    $special = $special ?: ['01-01', '07-01', '23-02', '08-03', '09-05'];
    
    $monthName = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'][$m-1];
    
    $first = new DateTime("$y-$m-01");
    $days = (int)$first->format('t');
    $start = (int)$first->format('N');
    
    echo '<div class="cal-box">';
    echo '<div class="cal-title">'.$monthName.' '.$y.'</div>';
    echo '<div class="cal-days">';
    // Дни недели в одну строку
    $weekDays = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
    foreach ($weekDays as $day) {
        echo '<div class="weekday">'.$day.'</div>';
    }
    echo '</div>';
    echo '<div class="cal-grid">';
    
    for ($i=1; $i<$start; $i++) echo '<div></div>';
    
    for ($d=1; $d<=$days; $d++) {
        $w = (int)date('N', strtotime("$y-$m-$d"));
        $key = sprintf('%02d-%02d', $d, $m);
        $cls = 'day';
        
        if (in_array($key, $special)) $cls .= ' red';
        elseif ($w >= 6) $cls .= ' blue';
        if ($d == date('j') && $m == date('n') && $y == date('Y')) $cls .= ' now';
        
        echo '<div class="'.$cls.'">'.$d.'</div>';
    }
    
    echo '</div></div>';
}

$m = $_GET['m'] ?? null;
$y = $_GET['y'] ?? null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Календарь</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .cal-box { background: white; padding: 20px; border-radius: 10px; max-width: 400px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .cal-title { text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 15px; color: #333; }
        .cal-days { 
            display: grid; 
            grid-template-columns: repeat(7, 1fr); 
            text-align: center; 
            font-weight: bold; 
            margin-bottom: 10px; 
            color: #666;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .weekday {
            padding: 5px;
        }
        .cal-grid { 
            display: grid; 
            grid-template-columns: repeat(7, 1fr); 
            gap: 5px; 
        }
        .cal-grid > div { 
            height: 40px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            border-radius: 5px; 
        }
        .day { background: #f8f8f8; }
        .day.blue { background: #e3f2fd; color: #1976d2; }
        .day.red { background: #ffebee; color: #d32f2f; font-weight: bold; }
        .day.now { border: 2px solid #4caf50; background: #e8f5e9; }
        .form { text-align: center; margin-bottom: 20px; }
        input { padding: 8px; margin: 5px; border: 1px solid #ddd; border-radius: 4px; width: 100px; }
        button { padding: 8px 15px; background: #2196f3; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="form">
        <form method="get">
            <input type="number" name="m" min="1" max="12" value="<?= $m ?? date('n') ?>" placeholder="Месяц">
            <input type="number" name="y" value="<?= $y ?? date('Y') ?>" placeholder="Год">
            <button>Показать</button>
        </form>
    </div>
    <?php showCalendar($m, $y); ?>
</body>
</html>