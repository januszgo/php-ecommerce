<?php
require 'config.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'], $_POST['password'])) {
        $stmt = $pdo->prepare("SELECT * FROM ecommerce.users WHERE login = ?");
        $stmt->execute([$_POST['login']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $message = "Zalogowano!";
        } else {
            $message = "Nieprawidłowy login lub hasło";
        }
    } elseif (isset($_POST['new_password'], $_POST['confirm_password'], $_SESSION['user_id'])) {
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            $hash = password($_POST['new_password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE ecommerce.users SET password=? WHERE id=?");
            $stmt->execute([$hash, $_SESSION['user_id']]);
            $message = "Hasło zmienione!";
        } else {
            $message = "Hasła nie zgadzają się";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Logowanie</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Logowanie / zmiana hasła</h1>
<?php if ($message) echo "<p class='message'>$message</p>"; ?>
<?php if (!isset($_SESSION['user_id'])): ?>
<form method="post">
    <label>Login: <input type="text" name="login" required></label><br>
    <label>Hasło: <input type="password" name="password" required></label><br>
    <button type="submit">Zaloguj</button>
</form>
<?php else: ?>
<form method="post">
    <label>Nowe hasło: <input type="password" name="new_password" required></label><br>
    <label>Potwierdź hasło: <input type="password" name="confirm_password" required></label><br>
    <button type="submit">Zmień hasło</button>
</form>
<p><a href="index.php">Powrót do sklepu</a></p>
<?php endif; ?>
</body>
</html>
