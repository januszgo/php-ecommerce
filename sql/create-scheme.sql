-- Tabela użytkowników
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    login TEXT UNIQUE NOT NULL,
    name TEXT NOT NULL,
    password TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    phone TEXT,
    address TEXT
);

-- Tabela produktów
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    photo TEXT, -- link URL do zdjęcia
    category TEXT,
    price NUMERIC NOT NULL CHECK (price >= 0),
    amount INTEGER DEFAULT 0 CHECK (amount >= 0),
    available INTEGER DEFAULT 1 -- 1 = true, 0 = false
);

-- Tabela zamówień
CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    uid TEXT, -- można wygenerować UUID w PHP
    user_id INTEGER NOT NULL,
    products_list TEXT NOT NULL, -- JSON w formie tekstu
    date_of_order TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_of_shipping TIMESTAMP,
    date_of_delivery TIMESTAMP,
    status TEXT DEFAULT 'pending',
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Indeks po loginie użytkownika
CREATE INDEX IF NOT EXISTS idx_users_login ON users(login);

-- Indeks po id produktów
CREATE INDEX IF NOT EXISTS idx_products_id ON products(id);
