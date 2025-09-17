<?php
require 'config.php';
$message = '';

// Wylogowanie
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: log.php');
    exit;
}

// Zmiana danych / hasła jeśli zalogowany
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    if (isset($_POST['new_password'], $_POST['confirm_password'])) {
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            $hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->execute([$hash, $userId]);
            $message = "Hasło zmienione!";
        } else {
            $message = "Hasła nie zgadzają się!";
        }
    }

    if (isset($_POST['phone'], $_POST['address'])) {
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $stmt = $pdo->prepare("UPDATE users SET phone=?, address=? WHERE id=?");
        $stmt->execute([$phone, $address, $userId]);
        $message = "Dane użytkownika zaktualizowane!";
    }
}

// Logowanie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'], $_POST['password']) && !isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->execute([$_POST['login']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $message = "Zalogowano!";
    } else {
        $message = "Nieprawidłowy login lub hasło";
    }
}

$currentUser = null;
$userOrders = [];
$totalOrders = 0;
$ordersPerPage = 5;
$totalPages = 1;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$_SESSION['user_id']]);
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

    // Liczenie wszystkich zamówień
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id=?");
    $stmt->execute([$currentUser['id']]);
    $totalOrders = (int)$stmt->fetchColumn();
    $totalPages = max(1, ceil($totalOrders / $ordersPerPage));
    $offset = ($page - 1) * $ordersPerPage;

    // Pobranie zamówień z LIMIT i OFFSET
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY date_of_order DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $currentUser['id'], PDO::PARAM_INT);
    $stmt->bindValue(2, $ordersPerPage, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $userOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Logowanie / Profil</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Logowanie / Profil</h1>

<?php if ($message) echo "<p class='message'>$message</p>"; ?>

<?php if (!$currentUser): ?>
<form method="post">
    <label>Login: <input type="text" name="login" required></label><br>
    <label>Hasło: <input type="password" name="password" required></label><br>
    <button type="submit">Zaloguj</button>
</form>
<p><a href="register.php">Rejestracja nowego użytkownika</a></p>

<?php else: ?>
<p>Zalogowany jako <strong><?= htmlspecialchars($currentUser['login']) ?></strong> | <a href="?action=logout">Wyloguj</a></p>

<h2>Zmiana hasła</h2>
<form method="post">
    <label>Nowe hasło: <input type="password" name="new_password" required></label><br>
    <label>Potwierdź hasło: <input type="password" name="confirm_password" required></label><br>
    <button type="submit">Zmień hasło</button>
</form>

<h2>Aktualizacja danych</h2>
<form method="post">
    <label>Telefon: <input type="text" name="phone" value="<?= htmlspecialchars($currentUser['phone']) ?>"></label><br>
    <label>Adres: <input type="text" name="address" value="<?= htmlspecialchars($currentUser['address']) ?>"></label><br>
    <button type="submit">Aktualizuj dane</button>
</form>

<h2>Twoje zamówienia</h2>
<?php if ($totalOrders === 0): ?>
    <p>Nie złożyłeś jeszcze żadnego zamówienia.</p>
<?php else: ?>
    <table border="1" cellpadding="5">
        <tr>
            <th>UID</th>
            <th>Data zamówienia</th>
            <th>Status</th>
            <th>Produkty</th>
        </tr>
        <?php foreach ($userOrders as $order): ?>
        <tr>
            <td><?= htmlspecialchars($order['uid']) ?></td>
            <td><?= htmlspecialchars($order['date_of_order']) ?></td>
            <td><?= htmlspecialchars($order['status']) ?></td>
            <td>
                <table border="0" cellpadding="3">
                    <tr><th>Nazwa</th><th>Ilość</th></tr>
                    <?php 
                    $products = json_decode($order['products_list'], true);
                    foreach ($products as $p):
                        $stmt = $pdo->prepare("SELECT name FROM products WHERE id=?");
                        $stmt->execute([$p['id']]);
                        $productName = $stmt->fetchColumn();
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($productName) ?></td>
                        <td><?= (int)$p['quantity'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- PAGINACJA -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" <?= $i === $page ? 'style="font-weight:bold;"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<p><a href="index.php">Powrót do sklepu</a></p>
<?php endif; ?>
</body>
</html>
