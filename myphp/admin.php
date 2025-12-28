<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("SELECT id, username, email, role, created_at FROM users");
$users = $stmt->fetchAll();

if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $deleteId]);
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пользователями</title>
   
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="nav-main">
        <div class="container nav-container">
            <div class="nav-logo">Админ-панель</div>
            <ul class="nav-menu">
                <li><a href="main.php">Главная</a></li>
                <li><a href="admin.php" class="active">Пользователи</a></li>
                <li><a href="logout.php">Выход</a></li>
                <li class="user-info-nav">
                    <?= htmlspecialchars($_SESSION['username']); ?> (<?= htmlspecialchars($_SESSION['role']); ?>)
                </li>
            </ul>
        </div>
    </nav>


    <div class="container">
        <header class="page-header">
            <h1>Управление пользователями</h1>
        </header>

     
        <div class="action-buttons">
            <a href="admin_add_user.php" class="btn btn-success">+ Добавить пользователя</a>
            <a href="main.php" class="btn btn-secondary">На главную</a>
        </div>


        <div class="content-box admin-panel">
            <h2 class="mb-20">Список пользователей</h2>
            
            <?php if (empty($users)): ?>
                <div class="info-message">Пользователи не найдены</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Имя пользователя</th>
                                <th>Email</th>
                                <th>Роль</th>
                                <th>Дата регистрации</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id']); ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($user['username']); ?></strong>
                                        <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                            <span class="badge">Вы</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="role-badge role-<?= htmlspecialchars($user['role']); ?>">
                                            <?= htmlspecialchars($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?= date('d.m.Y H:i', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="admin-actions">
                                            <a href="admin_edit_user.php?id=<?= $user['id']; ?>" class="btn btn-sm btn-secondary">Редактировать</a>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <a href="admin.php?delete_id=<?= $user['id']; ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Удалить пользователя <?= htmlspecialchars(addslashes($user['username'])); ?>?');">Удалить</a>
                                            <?php else: ?>
                                                <span class="btn btn-sm btn-disabled">Нельзя удалить</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <footer class="footer">
        <div class="container">
            <p>Админ-панель &copy; <?= date('Y'); ?></p>
        </div>
    </footer>
</body>
</html>