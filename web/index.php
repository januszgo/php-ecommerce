<?php
require 'config.php';

// Dodawanie do koszyka
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    if (!isset($_SESSION['basket'])) $_SESSION['basket'] = [];
    if (isset($_SESSION['basket'][$product_id])) {
        $_SESSION['basket'][$product_id] += $quantity;
    } else {
        $_SESSION['basket'][$product_id] = $quantity;
    }
    $message = "Dodano do koszyka!";
}

// Pobranie produktów
$stmt = $pdo->query("SELECT * FROM ecommerce.products ORDER BY id");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Sklep</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Produkty</h1>
<?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>
<table>
<tr><th>Zdjęcie</th><th>Nazwa</th><th>Kategoria</th><th>Cena</th><th>Dostępność</th><th>Koszyk</th></tr>
<?php foreach ($products as $p): ?>
<tr>
    <td><img src="<?= htmlspecialchars($p['photo']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" width="100"></td>
    <td><?= htmlspecialchars($p['name']) ?></td>
    <td><?= htmlspecialchars($p['category']) ?></td>
    <td><?= number_format($p['price'],2) ?> zł</td>
    <td><?= ($p['amount'] > 0 && $p['available']) ? 'Dostępny' : 'Niedostępny' ?></td>
    <td>
        <?php if ($p['amount'] > 0 && $p['available']): ?>
        <form method="post">
            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
            <input type="number" name="quantity" value="1" min="1" max="<?= $p['amount'] ?>">
            <button type="submit">Dodaj do koszyka</button>
        </form>
        <?php else: ?>
        -
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
<p><a href="basket.php">Przejdź do koszyka</a> | <a href="log.php">Logowanie</a></p>
</body>
</html>
