-- Utworzenie schematu
CREATE SCHEMA IF NOT EXISTS ecommerce;

-- Tabela użytkowników
CREATE TABLE ecommerce.users (
    id SERIAL PRIMARY KEY,
    login VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    password TEXT NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    address TEXT
);

-- Tabela produktów
CREATE TABLE ecommerce.products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    photo TEXT, -- link URL do zdjęcia
    category VARCHAR(100),
    price NUMERIC(10,2) NOT NULL CHECK (price >= 0),
    amount INT DEFAULT 0 CHECK (amount >= 0),
    available BOOLEAN DEFAULT TRUE
);

-- Tabela zamówień
CREATE TABLE ecommerce.orders (
    id SERIAL PRIMARY KEY,
    uid UUID DEFAULT gen_random_uuid(), -- unikalny identyfikator zamówienia
    user_id INT NOT NULL REFERENCES ecommerce.users(id) ON DELETE CASCADE,
    products_list JSONB NOT NULL, -- lista produktów i ilości w formacie JSON
    date_of_order TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_of_shipping TIMESTAMP,
    date_of_delivery TIMESTAMP,
    status VARCHAR(50) DEFAULT 'pending'
);

-- Dodatkowo: indeks po loginie użytkownika (często wyszukiwane)
CREATE INDEX idx_users_login ON ecommerce.users(login);

-- Dodatkowo: indeks po id produktów
CREATE INDEX idx_products_id ON ecommerce.products(id);