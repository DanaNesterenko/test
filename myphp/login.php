<?php
include 'db.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: main.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: main.php");
        exit;
    } else {
        $error = "Неверное имя пользователя или пароль.";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Вход | Спортивный портал</title>
</head>
<body>
    <div style="background: #2c3e50; display: flex; justify-content: center; align-items: center; min-height: 100vh;">
        <div style="width: 100%; max-width: 400px; padding: 20px;">
            <div class="content-box">
                <header style="background: #34495e; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
                    <h1 style="margin: 0; font-size: 22px;">Вход в систему</h1>
                </header>
                
                <?php if (!empty($error)): ?>
                    <div class="error-message" style="margin: 0;"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <div style="padding: 30px;">
                    <form method="POST" class="edit-form">
                        <div class="form-group">
                            <label class="form-label">Имя пользователя:</label>
                            <input type="text" name="username" 
                                   class="form-input" 
                                   placeholder="Введите имя пользователя" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Пароль:</label>
                            <input type="password" name="password" 
                                   class="form-input" 
                                   placeholder="Введите пароль" 
                                   required>
                        </div>
                        
                        <div style="margin-top: 20px;">
                            <button type="submit" class="btn" style="width: 100%; padding: 14px;">Войти в систему</button>
                        </div>
                    </form>
                    
                    <div style="margin-top: 25px; text-align: center; padding-top: 20px; border-top: 1px solid #eee;">
                        <a href="register.php" style="color: #27ae60; font-weight: bold; display: block; margin-bottom: 10px; padding: 8px; text-decoration: none;">
                            Создать новый аккаунт
                        </a>
                        <a href="main.php" style="color: #7f8c8d; text-decoration: none;">
                            ← Вернуться на главную
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('input[name="username"]').focus();
        });
        
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.querySelector('input[name="username"]').value.trim();
            const password = document.querySelector('input[name="password"]').value.trim();
            
            if (username.length < 3) {
                e.preventDefault();
                alert('Имя пользователя должно содержать минимум 3 символа');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Пароль должен содержать минимум 6 символов');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>