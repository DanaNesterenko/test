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
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
  
    if (strlen($username) < 3) {
        $error = "Имя пользователя должно содержать минимум 3 символа";
    } elseif (strlen($password) < 6) {
        $error = "Пароль должен содержать минимум 6 символов";
    } elseif ($password !== $confirm_password) {
        $error = "Пароли не совпадают";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Введите корректный email адрес";
    } else {
      
        $check_stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $check_stmt->execute(['username' => $username, 'email' => $email]);
        
        if ($check_stmt->fetch()) {
            $error = "Пользователь с таким именем или email уже существует";
        } else {
    
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
        
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
            $stmt->execute([
                'username' => $username, 
                'email' => $email, 
                'password' => $hashed_password
            ]);
            
       
            $userId = $pdo->lastInsertId();
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'user';
            
            header("Location: main.php?success=Регистрация успешна");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Регистрация | Спортивный портал</title>
</head>
<body>
    <div style="background: #2c3e50; display: flex; justify-content: center; align-items: center; min-height: 100vh;">
        <div style="width: 100%; max-width: 500px; padding: 20px;">
            <div class="content-box">
                <header style="background: #34495e; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
                    <h1 style="margin: 0; font-size: 22px;">Регистрация</h1>
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
                                   placeholder="Придумайте имя пользователя" 
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                   required>
                            <small class="form-hint">Минимум 3 символа</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email:</label>
                            <input type="email" name="email" 
                                   class="form-input" 
                                   placeholder="Введите ваш email"
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Пароль:</label>
                            <input type="password" name="password" 
                                   class="form-input" 
                                   placeholder="Придумайте пароль" 
                                   required>
                            <small class="form-hint">Минимум 6 символов</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Подтвердите пароль:</label>
                            <input type="password" name="confirm_password" 
                                   class="form-input" 
                                   placeholder="Повторите пароль" 
                                   required>
                        </div>
                        
                        <div style="margin-top: 25px;">
                            <button type="submit" class="btn btn-success" style="width: 100%; padding: 15px; font-size: 18px; min-height: 60px;">
                                Зарегистрироваться
                            </button>
                        </div>
                    </form>
                    
                    <div style="margin-top: 25px; text-align: center; padding-top: 20px; border-top: 1px solid #eee;">
                        <a href="login.php" style="color: #3498db; font-weight: bold; display: block; margin-bottom: 10px; padding: 10px; text-decoration: none;">
                            Уже есть аккаунт? Войти
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
    </script>
</body>
</html>