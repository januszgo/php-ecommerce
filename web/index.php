<?php
require 'config.php';

if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['basket'])) $_SESSION['basket'] = [];

// Dodawanie do koszyka
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    if (isset($_SESSION['basket'][$product_id])) {
        $_SESSION['basket'][$product_id] += $quantity;
    } else {
        $_SESSION['basket'][$product_id] = $quantity;
    }
    $message = "Dodano do koszyka!";
}

// Paginacja
$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Filtracja po kategorii
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';

// Pobranie dostępnych kategorii
$categories = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);

// Całkowita liczba produktów
if ($selectedCategory) {
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category = :category");
    $stmtCount->execute([':category' => $selectedCategory]);
    $totalProducts = (int)$stmtCount->fetchColumn();
} else {
    $totalProducts = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
}

// Pobranie produktów z LIMIT i OFFSET
$sql = "SELECT * FROM products";
$params = [];
if ($selectedCategory) {
    $sql .= " WHERE category = :category";
    $params[':category'] = $selectedCategory;
}
$sql .= " ORDER BY id LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPages = ceil($totalProducts / $limit);
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

<!-- Filtracja po kategorii -->
<form method="get">
    <label>Kategoria:
        <select name="category">
            <option value="">Wszystkie</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= $cat === $selectedCategory ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <button type="submit">Filtruj</button>
</form>

<table>
<tr><th>Zdjęcie</th><th>Nazwa</th><th>Kategoria</th><th>Cena</th><th>Dostępność</th><th>Koszyk</th></tr>
<?php foreach ($products as $p): ?>
<tr>
    <td><img src="<?= htmlspecialchars($p['photo']) ?>" alt="<?= htmlspecialchars($p['name']) ?>"></td>
    <td><?= htmlspecialchars($p['name']) ?></td>
    <td><?= htmlspecialchars($p['category']) ?></td>
    <td><?= number_format($p['price'], 2) ?> zł</td>
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

<?php if ($totalPages > 1): ?>
<div class="pagination">
Strony:
<?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <?php if ($i === $page): ?>
        <strong><?= $i ?></strong>
    <?php else: ?>
        <a href="?page=<?= $i ?><?= $selectedCategory ? '&category=' . urlencode($selectedCategory) : '' ?>"><?= $i ?></a>
    <?php endif; ?>
<?php endfor; ?>
</div>
<?php endif; ?>

<p><a href="basket.php">Przejdź do koszyka</a> | <a href="log.php">Moje konto</a></p>
</body>
</html>
