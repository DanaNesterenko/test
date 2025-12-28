<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: admin.php");
    exit;
}

$stmt = $pdo->prepare("SELECT username, email, role FROM users WHERE id = :id");
$stmt->execute(['id' => $id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id");
    $stmt->execute([
        'username' => $username,
        'email' => $email,
        'role' => $role,
        'id' => $id
    ]);

    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать пользователя</title>
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
            <h1>Редактировать пользователя</h1>
            <p>ID пользователя: <?= htmlspecialchars($id); ?></p>
        </header>

        <div class="content-box">
            <form action="admin_edit_user.php?id=<?= $id; ?>" method="post" class="edit-form">
                <div class="form-group">
                    <label for="username" class="form-label">Имя пользователя:</label>
                    <input type="text" id="username" name="username" 
                           value="<?= htmlspecialchars($user['username']); ?>" 
                           class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" 
                           value="<?= htmlspecialchars($user['email']); ?>" 
                           class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="role" class="form-label">Роль:</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="user" <?= $user['role'] === 'user' ? 'selected' : ''; ?>>Пользователь</option>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : ''; ?>>Администратор</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Сохранить изменения</button>
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
</body>
</html>