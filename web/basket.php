<?php
require 'config.php';

if (!isset($_SESSION['basket'])) $_SESSION['basket'] = [];

// Aktualizacja koszyka
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['quantities'] as $product_id => $qty) {
        $qty = (int)$qty;
        if ($qty <= 0) {
            unset($_SESSION['basket'][$product_id]);
        } else {
            $_SESSION['basket'][$product_id] = $qty;
        }
    }
    $message = "Koszyk zaktualizowany!";
}

// Pobranie danych produktów
$products_in_basket = [];
if (!empty($_SESSION['basket'])) {
    $ids = array_keys($_SESSION['basket']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM ecommerce.products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products_in_basket = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Koszyk</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Koszyk</h1>
<?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>
<?php if (empty($products_in_basket)): ?>
<p>Koszyk jest pusty.</p>
<?php else: ?>
<form method="post">
<table>
<tr><th>Nazwa</th><th>Cena</th><th>Ilość</th><th>Razem</th></tr>
<?php $total = 0; foreach ($products_in_basket as $p): 
    $qty = $_SESSION['basket'][$p['id']];
    $line_total = $p['price'] * $qty;
    $total += $line_total;
?>
<tr>
    <td><?= htmlspecialchars($p['name']) ?></td>
    <td><?= number_format($p['price'],2) ?> zł</td>
    <td><input type="number" name="quantities[<?= $p['id'] ?>]" value="<?= $qty ?>" min="0" max="<?= $p['amount'] ?>"></td>
    <td><?= number_format($line_total,2) ?> zł</td>
</tr>
<?php endforeach; ?>
<tr><td colspan="3"><strong>Razem:</strong></td><td><?= number_format($total,2) ?> zł</td></tr>
</table>
<button type="submit">Aktualizuj koszyk</button>
</form>
<p><button disabled>Przejdź do płatności (mock)</button></p>
<?php endif; ?>
<p><a href="index.php">Powrót do sklepu</a></p>
</body>
</html>
