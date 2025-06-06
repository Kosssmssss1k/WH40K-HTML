<?php
$host = 'localhost';
$db   = 'whst';
$user = 'student';
$pass = '123';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $factions = $pdo->query("SELECT DISTINCT faction FROM products ORDER BY faction ASC")->fetchAll(PDO::FETCH_COLUMN);
    $categories = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category ASC")->fetchAll(PDO::FETCH_COLUMN);
} catch (\PDOException $e) {
    $factions = [];
    $categories = [];
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Мініатюри - Warhammer UA Community</title>
  <link rel="stylesheet" href="./css/styles.css">
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>
<body>
  <div id="vue-cart-app" class="grid-container">
    <nav id="horizontal-menu">
      <ul>
        <li class="logo">
          <a href="index.html">
            <img src="./images/style/WhLogo.png" alt="WARHAMMER UA COMMUNITY">
          </a>
        </li>
        <li><a href="miniatures.php">Мініатюри</a></li>
        <li><a href="#" @click.prevent="openCart">Кошик ({{ cartItemCount }})</a></li>
      </ul>
    </nav>
    <header>
      <img src="./images/style/logo.png" alt="WARHAMMER UA COMMUNITY">
    </header>
    <main>
      <div class="page-title">НОВІ МІНІАТЮРИ</div>

      <div id="filters">
        <form class="filter-form">
            <div class="filter-group">
                <label for="faction-filter">Фракція:</label>
                <select id="faction-filter" v-model="selectedFaction">
                    <option value="all">Всі фракції</option>
                    <?php foreach ($factions as $faction): ?>
                        <option value="<?= htmlspecialchars($faction) ?>"><?= htmlspecialchars($faction) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="category-filter">Категорія:</label>
                <select id="category-filter" v-model="selectedCategory">
                    <option value="all">Всі категорії</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
      </div>

      <div id="catalog">
        <div v-if="products.length === 0" class="no-results">
            За вашим запитом товарів не знайдено.
        </div>
        <div class="item" v-for="product in products" :key="product.id">
          <img :src="product.image" :alt="product.name">
          <div class="item-details">
            <h3>{{ product.name }}</h3>
            <p class="price">{{ product.price }} грн</p>
            <button class="add-to-cart-btn" @click="addToCart(product)">Додати в кошик</button>
          </div>
        </div>
      </div>
    </main>
    <footer>
      <p>&copy; 2025 WARHAMMER UA COMMUNITY</p>
    </footer>

    <div id="cart-modal" class="modal" :class="{ 'is-active': isCartVisible }">
      <div class="modal-content">
        <span class="close-button" @click="closeCart">&times;</span>
        <h2>Ваш Кошик</h2>
        <div id="cart-items-container">
          <div v-if="cart.length === 0" class="empty-cart-message">
            Ваш кошик порожній.
          </div>
          <div v-else class="cart-item" v-for="item in cart" :key="item.id">
            <img :src="item.image" :alt="item.name">
            <div class="cart-item-details">
              <h4>{{ item.name }}</h4>
              <p>Ціна: {{ item.price }} грн</p>
            </div>
            <div class="cart-item-quantity">
              <input type="number" :value="item.quantity" @change="updateQuantity(item.id, $event.target.value)" min="1" :aria-label="'Кількість для ' + item.name">
            </div>
            <p class="cart-item-subtotal">{{ item.price * item.quantity }} грн</p>
            <button class="cart-item-remove-btn" @click="removeFromCart(item.id)" :aria-label="'Видалити ' + item.name + ' з кошика'">&times;</button>
          </div>
        </div>
        <div class="cart-summary" v-if="cart.length > 0">
          <p>Разом: <span id="cart-total-display">{{ cartTotal }}</span> грн</p> <button id="checkout-button">Оформити замовлення</button>
        </div>
      </div>
    </div>
  </div> 
  <div id="cart-notification"></div>
  <script src="./js/scripts.js"></script>
</body>
</html>