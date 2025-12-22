<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Таблица умножения</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f9;
            color: #333;
        }
        h1 {
            margin: 20px 0;
        }
        table {
            border-collapse: collapse;
            margin: 20px auto;
            width: 80%;
            max-width: 800px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <h1>Таблица умножения</h1>
    <table>
        <?php
        for ($i = 0; $i <= 10; $i++) {
            echo "<tr>";
            for ($j = 0; $j <= 10; $j++) {
                echo "<td>{$i} x {$j} = " . ($i * $j) . "</td>";
            }
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>