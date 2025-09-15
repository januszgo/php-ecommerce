-- Włączamy rozszerzenie pgcrypto (do haszowania haseł)
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- Wstawianie produktów
INSERT INTO ecommerce.products (name, photo, category, price, amount, available)
VALUES
('iPhone 15', 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-17-pro-finish-select-cosmicorange-202509_AV2_FMT_WHH', 'smartfony', 3999.99, 5, TRUE),
('iPhone 15 Pro', 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-17-pro-finish-select-cosmicorange-202509_AV2_FMT_WHH', 'smartfony', 2999.99, 5, TRUE),
('iPhone 14', 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-17-pro-finish-select-cosmicorange-202509_AV2_FMT_WHH', 'smartfony', 999.99, 15, TRUE),
('iPhone 17 Pro', 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-17-pro-finish-select-cosmicorange-202509_AV2_FMT_WHH', 'smartfony', 4999.99, 1, TRUE),
('iPhone 17 Pro Plus', 'https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/iphone-17-pro-finish-select-cosmicorange-202509_AV2_FMT_WHH', 'smartfony', 5999.99, 0, FALSE);

-- Wstawianie użytkowników
INSERT INTO ecommerce.users (login, email, name, phone, address, password)
VALUES
('admin', 'admin@admin.com', 'Admin Admin', '123456789', 'Warszawa ul. Warszawska 1 00-001', crypt('admin', gen_salt('bf'))),
('user1', 'user@user.com', 'Użytkownik Standardowy', '987654321', 'Kraków ul. Krakowska 1 00-001', crypt('user', gen_salt('bf')));