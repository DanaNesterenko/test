<?php

include 'db.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Для добавления комментария необходимо авторизоваться";
    header("Location: login.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Неверный метод запроса";
    header("Location: main.php");
    exit;
}


if (!isset($_POST['article_id']) || !isset($_POST['content'])) {
    $_SESSION['error'] = "Не все обязательные поля заполнены";
    $redirect = $_SERVER['HTTP_REFERER'] ?? 'main.php';
    header("Location: " . $redirect);
    exit;
}


$article_id = intval($_POST['article_id']);
$content = trim($_POST['content']);
$user_id = $_SESSION['user_id'];


if (empty($content)) {
    $_SESSION['error'] = "Комментарий не может быть пустым";
    $redirect = $_SERVER['HTTP_REFERER'] ?? 'main.php';
    header("Location: " . $redirect);
    exit;
}

try {
    $checkStmt = $pdo->prepare("SELECT id FROM articles WHERE id = ?");
    $checkStmt->execute([$article_id]);
    
    if (!$checkStmt->fetch()) {
        $_SESSION['error'] = "Статья не найдена";
        $redirect = $_SERVER['HTTP_REFERER'] ?? 'main.php';
        header("Location: " . $redirect);
        exit;
    }
} catch (PDOException $e) {
    error_log("Ошибка при проверке статьи: " . $e->getMessage());
    $_SESSION['error'] = "Произошла ошибка при проверке статьи";
    header("Location: main.php");
    exit;
}


try {
   
    $stmt = $pdo->prepare("INSERT INTO comments (article_id, user_id, comment) VALUES (:article_id, :user_id, :content)");
    
    $stmt->execute([
        'article_id' => $article_id,
        'user_id' => $user_id,
        'content' => $content  
    ]);
    
    $_SESSION['success'] = "Комментарий успешно добавлен!";
  
    
} catch (PDOException $e) {
    error_log("Ошибка при добавлении комментария: " . $e->getMessage());
    $_SESSION['error'] = "Ошибка при добавлении комментария: " . $e->getMessage();
}


$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : ($_SERVER['HTTP_REFERER'] ?? 'main.php');


$redirect = preg_replace('/&?error=[^&]*/', '', $redirect);
$redirect = preg_replace('/&?success=[^&]*/', '', $redirect);


if (strpos($redirect, 'art.php') !== false) {
    $redirect .= '#comments';
}

header("Location: " . $redirect);
exit;
?>