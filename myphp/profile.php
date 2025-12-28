<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT username, email, role, created_at FROM users WHERE id = :id");
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch();

if (!$user) {
    echo "Ошибка: данные пользователя не найдены.";
    exit;
}


$articles_stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE author_id = :id");
$articles_stmt->execute(['id' => $userId]);
$articles_count = $articles_stmt->fetchColumn();

$comments_stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE user_id = :id");
$comments_stmt->execute(['id' => $userId]);
$comments_count = $comments_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мой профиль</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <nav class="nav-main">
        <div class="container nav-container">
            <div class="nav-logo">Мой профиль</div>
            <ul class="nav-menu">
                <li><a href="main.php">Главная</a></li>
                <li><a href="profile.php" class="active">Профиль</a></li>
                <li><a href="logout.php">Выход</a></li>
                <li class="user-info-nav">
                    <?= htmlspecialchars($_SESSION['username']); ?> (<?= htmlspecialchars($_SESSION['role']); ?>)
                </li>
            </ul>
        </div>
    </nav>

   
    <div class="container">
        <header class="page-header">
            <h1>Мой профиль</h1>
        </header>

        
        <div class="content-box">
            <div style="background: #f8f9fa; padding: 20px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #3498db;">
                <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #dee2e6;">
                    <div style="color: #2c3e50; font-weight: bold; margin-bottom: 5px;">Имя пользователя:</div>
                    <div style="color: #333; font-size: 16px;"><?= htmlspecialchars($user['username']); ?></div>
                </div>
                
                <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #dee2e6;">
                    <div style="color: #2c3e50; font-weight: bold; margin-bottom: 5px;">Email:</div>
                    <div style="color: #333; font-size: 16px;"><?= htmlspecialchars($user['email']); ?></div>
                </div>
                
                <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #dee2e6;">
                    <div style="color: #2c3e50; font-weight: bold; margin-bottom: 5px;">Роль:</div>
                    <div style="color: #333; font-size: 16px;">
                        <span class="role-badge role-<?= $user['role']; ?>">
                            <?= $user['role'] === 'admin' ? 'Администратор' : 'Пользователь'; ?>
                        </span>
                    </div>
                </div>
                
                <div>
                    <div style="color: #2c3e50; font-weight: bold; margin-bottom: 5px;">Дата регистрации:</div>
                    <div style="color: #333; font-size: 16px;"><?= date('d.m.Y H:i', strtotime($user['created_at'])); ?></div>
                </div>
            </div>

          
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-top: 20px;">
                <div style="background: #e8f4fc; padding: 15px; border-radius: 6px; text-align: center;">
                    <div style="font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 5px;"><?= $articles_count; ?></div>
                    <div style="color: #6c757d; font-size: 14px;">Статей</div>
                </div>
                
                <div style="background: #e8f4fc; padding: 15px; border-radius: 6px; text-align: center;">
                    <div style="font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 5px;"><?= $comments_count; ?></div>
                    <div style="color: #6c757d; font-size: 14px;">Комментариев</div>
                </div>
            </div>

            
            <div style="display: flex; gap: 15px; margin-top: 25px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                <a href="profile_edit.php" class="btn btn-success">Редактировать профиль</a>
                <a href="logout.php" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите выйти?');">Выйти</a>
                <a href="main.php" class="btn btn-secondary">На главную</a>
            </div>
        </div>
    </div>

   
    <footer class="footer">
        <div class="container">
            <p>Спортивный портал &copy; <?= date('Y'); ?></p>
        </div>
    </footer>

    <script>
  
        document.querySelector('.btn-danger').addEventListener('click', function(e) {
            if (!confirm('Вы уверены, что хотите выйти из профиля?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>