<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'];

 
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Все поля обязательны для заполнения';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен содержать минимум 6 символов';
    } elseif ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } else {
       
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR username = :username");
        $stmt->execute(['email' => $email, 'username' => $username]);
        
        if ($stmt->fetch()) {
            $error = 'Пользователь с таким email или логином уже существует';
        } else {
           
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, created_at) 
                                   VALUES (:username, :email, :password, :role, NOW())");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashed_password,
                'role' => $role
            ]);
            
            $success = 'Пользователь успешно добавлен!';
            header("refresh:2;url=admin.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить пользователя</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
  
    <nav class="nav-main">
        <div class="container nav-container">
            <div class="nav-logo">Админ-панель</div>
            <ul class="nav-menu">
                <li><a href="main.php">Главная</a></li>
                <li><a href="admin.php">Пользователи</a></li>
                <li><a href="logout.php">Выход</a></li>
                <li class="user-info-nav">
                    <?= htmlspecialchars($_SESSION['username']); ?> (<?= htmlspecialchars($_SESSION['role']); ?>)
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <header class="page-header">
            <h1>Добавить нового пользователя</h1>
            <p>Заполните все поля для создания нового аккаунта</p>
        </header>

      
        <?php if (!empty($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success-message">
                <?= htmlspecialchars($success); ?>
                <br><small>Перенаправление на страницу пользователей через 2 секунды...</small>
            </div>
        <?php endif; ?>

        <div class="content-box">
            <form action="admin_add_user.php" method="post" class="edit-form" id="addUserForm">
                <div class="form-group">
                    <label for="username" class="form-label">Имя пользователя *</label>
                    <input type="text" id="username" name="username" 
                           class="form-input" required
                           placeholder="Введите имя пользователя"
                           minlength="3" maxlength="50">
                    <small class="form-hint">Минимум 3 символа</small>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" id="email" name="email" 
                           class="form-input" required
                           placeholder="Введите email">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Пароль *</label>
                    <input type="password" id="password" name="password" 
                           class="form-input" required
                           placeholder="Введите пароль"
                           minlength="6">
                    <small class="form-hint">Минимум 6 символов</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Подтвердите пароль *</label>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           class="form-input" required
                           placeholder="Повторите пароль">
                </div>

                <div class="form-group">
                    <label for="role" class="form-label">Роль *</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="user">Пользователь</option>
                        <option value="admin">Администратор</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        <span> Добавить пользователя</span>
                    </button>
                    <a href="admin.php" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        </div>
    </div>


    <footer class="footer">
        <div class="container">
            <p>Админ-панель &copy; <?= date('Y'); ?></p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('addUserForm');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');

            document.getElementById('username').focus();
            
            function validatePasswords() {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Пароли не совпадают');
                    return false;
                } else {
                    confirmPassword.setCustomValidity('');
                    return true;
                }
            }
            
            password.addEventListener('input', validatePasswords);
            confirmPassword.addEventListener('input', validatePasswords);
            
         
            form.addEventListener('submit', function(e) {
                if (!validatePasswords()) {
                    e.preventDefault();
                    alert('Пожалуйста, убедитесь что пароли совпадают');
                    return false;
                }
                
                if (password.value.length < 6) {
                    e.preventDefault();
                    alert('Пароль должен содержать минимум 6 символов');
                    return false;
                }
                
                return true;
            });
        });
    </script>
</body>
</html>