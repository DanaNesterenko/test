<?php
// Функция для сохранения данных пользователя
function saveUser($data) {
    $filename = 'users.txt';
    $line = implode('|', array_map('trim', $data)) . PHP_EOL;
    file_put_contents($filename, $line, FILE_APPEND | LOCK_EX);
}

// Обработка формы
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение и очистка данных
    $fio = trim($_POST['fio'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $birthdate = trim($_POST['birthdate'] ?? '');
    
    // Валидация
    if (empty($fio)) {
        $errors[] = 'ФИО обязательно для заполнения';
    } elseif (strlen($fio) < 3) {
        $errors[] = 'ФИО должно содержать не менее 3 символов';
    }
    
    if (empty($login)) {
        $errors[] = 'Логин обязателен для заполнения';
    } elseif (strlen($login) < 3) {
        $errors[] = 'Логин должен содержать не менее 3 символов';
    }
    
    if (empty($password)) {
        $errors[] = 'Пароль обязателен для заполнения';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Пароль должен содержать не менее 6 символов';
    }
    
    if (empty($birthdate)) {
        $errors[] = 'Дата рождения обязательна';
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $birthdate);
        if (!$date || $date->format('Y-m-d') !== $birthdate) {
            $errors[] = 'Некорректная дата рождения';
        } else {
            $today = new DateTime();
            $age = $today->diff($date)->y;
            if ($age < 0) {
                $errors[] = 'Дата рождения не может быть в будущем';
            }
        }
    }
    
    // Если ошибок нет - сохраняем
    if (empty($errors)) {
        $userData = [
            'fio' => $fio,
            'login' => $login,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'birthdate' => $birthdate,
            'registered' => date('Y-m-d H:i:s')
        ];
        
        saveUser($userData);
        $success = true;
        
        // Очищаем поля после успешной регистрации
        $fio = $login = $password = $birthdate = '';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация пользователя</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 500px;
        }
        
        .form-box {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
        
        .title {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        
        .title h1 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .title p {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 15px rgba(102, 126, 234, 0.4);
        }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .error {
            background: #ffebee;
            color: #d32f2f;
            border-left: 4px solid #d32f2f;
        }
        
        .success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #4caf50;
        }
        
        .error-list {
            list-style: none;
            padding: 0;
        }
        
        .error-list li {
            padding: 5px 0;
            color: #d32f2f;
        }
        
        .required {
            color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-box">
            <div class="title">
                <h1>Регистрация пользователя</h1>
                <p>Заполните все поля для создания аккаунта</p>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="message error">
                    <ul class="error-list">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="message success">
                    Регистрация успешно завершена!<br>
                    Данные сохранены в системе.
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="fio">
                        ФИО <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="fio" 
                           name="fio" 
                           class="form-control" 
                           value="<?php echo isset($fio) ? htmlspecialchars($fio) : ''; ?>" 
                           placeholder="Иванов Иван Иванович"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="login">
                        Логин <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="login" 
                           name="login" 
                           class="form-control" 
                           value="<?php echo isset($login) ? htmlspecialchars($login) : ''; ?>" 
                           placeholder="Придумайте логин"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        Пароль <span class="required">*</span>
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           placeholder="Не менее 6 символов"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="birthdate">
                        Дата рождения <span class="required">*</span>
                    </label>
                    <input type="date" 
                           id="birthdate" 
                           name="birthdate" 
                           class="form-control" 
                           value="<?php echo isset($birthdate) ? htmlspecialchars($birthdate) : ''; ?>"
                           max="<?php echo date('Y-m-d'); ?>"
                           required>
                </div>
                
                <button type="submit" class="btn">Зарегистрироваться</button>
            </form>
            
            <div style="margin-top: 20px; text-align: center; font-size: 12px; color: #7f8c8d;">
                Поля, отмеченные <span class="required">*</span>, обязательны для заполнения
            </div>
        </div>
    </div>
</body>
</html>