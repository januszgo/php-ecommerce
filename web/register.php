<?php
require 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $email = trim($_POST['email']);
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "Hasła nie zgadzają się!";
    } else {
        // Sprawdzenie czy login lub email już istnieje
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE login=? OR email=?");
        $stmt->execute([$login, $email]);
        if ($stmt->fetchColumn() > 0) {
            $message = "Login lub email już istnieje!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (login, email, name, phone, address, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$login, $email, $name, $phone, $address, $hash]);
            $message = "Rejestracja zakończona sukcesem! Możesz się zalogować.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Rejestracja</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Rejestracja nowego użytkownika</h1>
<?php if ($message) echo "<p class='message'>$message</p>"; ?>
<form method="post">
    <label>Login: <input type="text" name="login" required></label><br>
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Imię i nazwisko: <input type="text" name="name" required></label><br>
    <label>Telefon: <input type="text" name="phone"></label><br>
    <label>Adres: <input type="text" name="address"></label><br>
    <label>Hasło: <input type="password" name="password" required></label><br>
    <label>Potwierdź hasło: <input type="password" name="confirm_password" required></label><br>
    <button type="submit">Zarejestruj się</button>
</form>
<p><a href="index.php">Powrót do sklepu</a></p>
</body>
</html>
