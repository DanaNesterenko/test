<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$successMessage = $errorMessage = "";


$stmt = $pdo->prepare("SELECT username, email, role, created_at FROM users WHERE id = :id");
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch();

if (!$user) {
    echo "Ошибка: данные пользователя не найдены.";
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $passwordConfirm = trim($_POST['password_confirm']);

   
    if (empty($username) || empty($email)) {
        $errorMessage = "Имя пользователя и email обязательны для заполнения.";
    } elseif ($password !== $passwordConfirm) {
        $errorMessage = "Пароли не совпадают.";
    } else {
        try {
            $updateQuery = "UPDATE users SET username = :username, email = :email";
            $params = ['username' => $username, 'email' => $email, 'id' => $userId];

            
            if (!empty($password)) {
                $updateQuery .= ", password = :password";
                $params['password'] = password_hash($password, PASSWORD_BCRYPT);
            }

            $updateQuery .= " WHERE id = :id";
            $stmt = $pdo->prepare($updateQuery);
            $stmt->execute($params);

           
            $_SESSION['username'] = $username;
            
       
            $_SESSION['success'] = "Данные профиля успешно обновлены!";
            header("Location: profile.php");
            exit;
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { 
                if (strpos($e->getMessage(), 'username')) {
                    $errorMessage = "Это имя пользователя уже занято.";
                } elseif (strpos($e->getMessage(), 'email')) {
                    $errorMessage = "Этот email уже используется.";
                } else {
                    $errorMessage = "Ошибка обновления: " . $e->getMessage();
                }
            } else {
                $errorMessage = "Ошибка обновления: " . $e->getMessage();
            }
        }
    }
}


if (isset($_SESSION['success'])) {
    $successMessage = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование профиля</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="nav-main">
        <div class="container nav-container">
            <div class="nav-logo">Редактирование профиля</div>
            <ul class="nav-menu">
                <li><a href="main.php">Главная</a></li>
                <li><a href="profile.php">Профиль</a></li>
                <li><a href="logout.php">Выход</a></li>
                <li class="user-info-nav">
                    <?= htmlspecialchars($_SESSION['username']); ?>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <header class="page-header">
            <h1>Редактирование профиля</h1>
        </header>

        
        <?php if ($successMessage): ?>
            <div class="success-message"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>
        
        <?php if ($errorMessage): ?>
            <div class="error-message"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>

        <div class="content-box">
         
            <div style="background: #e8f4fc; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                <p style="margin: 0; color: #2c3e50;">
                    Текущая роль: 
                    <span class="role-badge role-<?= $user['role']; ?>">
                        <?= $user['role'] === 'admin' ? 'Администратор' : 'Пользователь'; ?>
                    </span>
                    <br>
                    Дата регистрации: <strong><?= date('d.m.Y', strtotime($user['created_at'])); ?></strong>
                </p>
            </div>

         
            <form method="POST" action="" class="edit-form">
                <div class="form-group">
                    <label class="form-label">Имя пользователя *</label>
                    <input type="text" 
                           name="username" 
                           class="form-input" 
                           value="<?= htmlspecialchars($user['username']); ?>" 
                           required 
                           maxlength="50"
                           placeholder="Введите новое имя пользователя">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" 
                           name="email" 
                           class="form-input" 
                           value="<?= htmlspecialchars($user['email']); ?>" 
                           required 
                           maxlength="100"
                           placeholder="Введите новый email">
                </div>
                
           
                <div style="background: #fff3cd; color: #856404; padding: 12px 15px; border-radius: 4px; margin: 15px 0; border: 1px solid #ffeaa7;">
                    ⓘ Заполняйте поля ниже только если хотите изменить пароль. Оставьте пустыми, если не хотите менять пароль.
                </div>
                
                <div class="form-group">
                    <label class="form-label">Новый пароль</label>
                    <input type="password" 
                           name="password" 
                           class="form-input" 
                           placeholder="Введите новый пароль"
                           minlength="6">
                    <small class="form-hint">Минимум 6 символов</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Подтвердите пароль</label>
                    <input type="password" 
                           name="password_confirm" 
                           class="form-input" 
                           placeholder="Повторите новый пароль"
                           minlength="6">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Сохранить изменения</button>
                    <a href="profile.php" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        </div>
    </div>

  
    <footer class="footer">
        <div class="container">
            <p>Спортивный портал &copy; <?= date('Y'); ?></p>
        </div>
    </footer>

   
    <script>
        document.querySelector('.btn-secondary').addEventListener('click', function(e) {
            if (document.querySelector('input[name="username"]').value !== "<?= htmlspecialchars($user['username']); ?>" ||
                document.querySelector('input[name="email"]').value !== "<?= htmlspecialchars($user['email']); ?>" ||
                document.querySelector('input[name="password"]').value !== "") {
                
                if (!confirm('У вас есть несохраненные изменения. Вы уверены, что хотите отменить редактирование?')) {
                    e.preventDefault();
                }
            }
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            const passwordConfirm = document.querySelector('input[name="password_confirm"]').value;
            
            if (password !== passwordConfirm) {
                alert('Пароли не совпадают!');
                e.preventDefault();
                return false;
            }
            
            if (password.length > 0 && password.length < 6) {
                alert('Пароль должен содержать минимум 6 символов!');
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>