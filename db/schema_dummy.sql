----- USERS -----

INSERT INTO users (email, password_hash, first_name, last_name, role_id, phone, birth_date, inscription_date, last_connection, street_nb, street_nb_suf, street, town, zip_code, intercom_code) VALUES
-- Passwords: root
-- 5 clients, 1 restaurateur, 3 administrateurs, 1 livreur
('client@gmail.com', '$2y$12$0FEsdSyYSTrC0713vB2KzuoXFnZldB0v1Wr8JxUmMTmoHow07bDwe', 'Etienne', 'Coriou', 1, '+33712345678', '2006-03-27', '2026-03-28 22:00:00.000000', '2026-03-28 23:00:00.000000', 2, NULL, 'Rue de la Brique', 'Paris', 75000, NULL),
('cook@gmail.com', '$2y$12$0FEsdSyYSTrC0713vB2KzuoXFnZldB0v1Wr8JxUmMTmoHow07bDwe', 'Alice', 'La Restauratrice', 2, '+33712345678', '2006-03-27', '2026-03-28 22:00:00.000000', '2026-03-28 23:00:00.000000', 2, 'bis', 'Rue de la Brique', 'Paris', 75000, '012345'),
('administrator@gmail.com', '$2y$12$0FEsdSyYSTrC0713vB2KzuoXFnZldB0v1Wr8JxUmMTmoHow07bDwe', 'Patrice', 'L''Administratrice', 3, '+33712345678', '2006-03-27', '2026-03-28 22:00:00.000000', '2026-03-28 23:00:00.000000', 2, 'ter', 'Rue de la Brique', 'Paris', 75000, '012345'),
('delivery_person@gmail.com', '$2y$12$0FEsdSyYSTrC0713vB2KzuoXFnZldB0v1Wr8JxUmMTmoHow07bDwe', 'Peter', 'Le Livreur', 4, '+33712345678', '2006-03-27', '2026-03-28 22:00:00.000000', '2026-03-28 23:00:00.000000', 2, NULL, 'Rue de la Brique', 'Paris', 75000, '012345'),
('john@gmail.com', '$2y$12$0FEsdSyYSTrC0713vB2KzuoXFnZldB0v1Wr8JxUmMTmoHow07bDwe', 'John', 'Doe', 1, '+33712345678', '2006-03-27', '2026-03-28 22:00:00.000000', '2026-03-28 23:00:00.000000', 2, NULL, 'Rue de la Brique', 'Paris', 75000, '012345'),
('dupont@gmail.com', '$2y$12$0FEsdSyYSTrC0713vB2KzuoXFnZldB0v1Wr8JxUmMTmoHow07bDwe', 'Dupont', 'Dupont', 1, '+33712345678', '2006-03-27', '2026-03-28 22:00:00.000000', '2026-03-28 23:00:00.000000', 2, NULL, 'Rue de la Brique', 'Paris', 75000, '012345'),
('martin@gmail.com', '$2y$12$0FEsdSyYSTrC0713vB2KzuoXFnZldB0v1Wr8JxUmMTmoHow07bDwe', 'Martin', 'Jeudy', 1, '+33712345678', '2006-03-27', '2026-03-28 22:00:00.000000', '2026-03-28 23:00:00.000000', 2, NULL, 'Rue de la Brique', 'Paris', 75000, '012345'),
('axel@gmail.com', '$2y$12$0FEsdSyYSTrC0713vB2KzuoXFnZldB0v1Wr8JxUmMTmoHow07bDwe', 'Axel', 'Can', 3, '+33712345678', '2006-03-27', '2026-03-28 22:00:00.000000', '2026-03-28 23:00:00.000000', 2, NULL, 'Rue de la Brique', 'Paris', 75000, '012345'),
('grignon@gmail.com', '$2y$12$0FEsdSyYSTrC0713vB2KzuoXFnZldB0v1Wr8JxUmMTmoHow07bDwe', 'Romuald', 'Grignon', 3, '+33712345678', '2006-03-27', '2026-03-28 22:00:00.000000', '2026-03-28 23:00:00.000000', 2, NULL, 'Rue de la Brique', 'Paris', 75000, '012345'),
('lebreton@gmail.com', '$2y$12$0FEsdSyYSTrC0713vB2KzuoXFnZldB0v1Wr8JxUmMTmoHow07bDwe', 'Caryl', 'Le Breton', 1, '+33712345678', '2006-03-27', '2026-03-28 22:00:00.000000', '2026-03-28 23:00:00.000000', 2, NULL, 'Rue de la Brique', 'Paris', 75000, NULL);


----- COUPONS -----

INSERT INTO coupon (code, discount_percent, expiration_date) VALUES
('GRIGNON75', 0.75, '2027-01-01 00:00:00.000000'),
('LEBRETON75', 0.75, '2027-01-01 00:00:00.000000'),
('INVALID', 0.75, '2023-01-01 00:00:00.000000');


----- CART -----

INSERT INTO cart (user_id, payment_status_id, created_at, coupon_id) VALUES
(1, 1, '2026-03-28 22:30:00.000000', 1),
(2, 1, '2026-03-28 22:30:00.000000', NULL),
(3, 1, '2026-03-28 22:30:00.000000', NULL),
(4, 1, '2026-03-28 22:30:00.000000', NULL);

INSERT INTO cart_menu (cart_id, menu_id, quantity) VALUES
(1, 1, 9),
(2, 3, 1),
(3, 4, 2),
(4, 2, 1);

INSERT INTO cart_food (cart_id, food_id, quantity) VALUES
(1, 1, 1),
(1, 6, 1),
(1, 7, 1),
(1, 10, 1),
(1, 12, 1);


---- DELIVERIES ----

INSERT INTO orders (cart_id, customer_id, cook_id, delivery_person_id) VALUES
(1, 1, 2, 4),
(2, 2, 2, 4),
(3, 3, 2, 4),
(4, 4, 2, 4);


----- REVIEWS -----

INSERT INTO reviews (order_id, product_stars, delivery_stars, comment) VALUES
(1, 5, 5, 'Excellent restaurant !'),
(2, 1, 5, 'Livraison parfaite, mais la nourriture laisse à désirer...'),
(3, 3, 1, 'Il y avait un cheveu dans ma soupe.'),
(4, 5, 2, 'Très bon, mais la livraison était assez moyenne...');
