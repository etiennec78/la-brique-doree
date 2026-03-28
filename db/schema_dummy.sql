----- USERS -----

INSERT INTO users (email, password_hash, first_name, last_name, role_id, phone, birth_date, inscription_date, last_connection, street_nb, street_nb_ext, street, town, zip_code) VALUES
-- Passwords: root
('client@gmail.com', '$2y$12$0FEsdSyYSTrC0713vB2KzuoXFnZldB0v1Wr8JxUmMTmoHow07bDwe', 'Etienne', 'Coriou', 1, '+33712345678', '2006-03-27', '2026-03-28 22:00:00.000000', '2026-03-28 23:00:00.000000', 2, NULL, 'Rue de la Brique', 'Paris', 75000),
('restaurateur@gmail.com', '$2y$12$0FEsdSyYSTrC0713vB2KzuoXFnZldB0v1Wr8JxUmMTmoHow07bDwe', 'Alice', 'La Restauratrice', 2, '+33712345678', '2006-03-27', '2026-03-28 22:00:00.000000', '2026-03-28 23:00:00.000000', 2, 'bis', 'Rue de la Brique', 'Paris', 75000),
('administrator@gmail.com', '$2y$12$0FEsdSyYSTrC0713vB2KzuoXFnZldB0v1Wr8JxUmMTmoHow07bDwe', 'Patrice', 'L Administratrice', 3, '+33712345678', '2006-03-27', '2026-03-28 22:00:00.000000', '2026-03-28 23:00:00.000000', 2, 'ter', 'Rue de la Brique', 'Paris', 75000),
('delivery_person@gmail.com', '$2y$12$0FEsdSyYSTrC0713vB2KzuoXFnZldB0v1Wr8JxUmMTmoHow07bDwe', 'Peter', 'Le Livreur', 4, '+33712345678', '2006-03-27', '2026-03-28 22:00:00.000000', '2026-03-28 23:00:00.000000', 2, NULL, 'Rue de la Brique', 'Paris', 75000);

INSERT INTO coupon (code, discount_percent, expiration_date) VALUES
('GRIGNON75', 0.75, '2027-01-01 00:00:00.000000'),
('LEBRETON75', 0.75, '2027-01-01 00:00:00.000000');

INSERT INTO cart (user_id, payment_status_id, created_at, coupon_id) VALUES
(1, 1, '2026-03-28 22:30:00.000000', 1);

INSERT INTO reviews (user_id, product_stars, delivery_stars, comment) VALUES
(1, 5, 5, 'Excellent restaurant !'),
(2, 1, 5, 'Livraison parfaite, mais la nourriture laisse à désirer...'),
(3, 3, 1, 'Il y avait un cheveu dans ma soupe.'),
(4, 5, 2, 'Très bon, mais la livraison était assez moyenne...');
