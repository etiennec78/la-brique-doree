----- ALLERGENES -----

CREATE TABLE allergen (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(25) UNIQUE NOT NULL
);

INSERT INTO allergen (name) VALUES
('crustacean'), ('fish'), ('gluten'), ('milk'), ('sesame'), ('egg'), ('soy'), ('nut'), ('sulfite');


----- NUTRISCORE, TIME_SLOT et FOOD_TYPE -----

CREATE TABLE nutriscore (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name CHAR(1) UNIQUE NOT NULL
);

INSERT INTO nutriscore (name) VALUES
('A'), ('B'), ('C'), ('D'), ('E');

CREATE TABLE time_slot (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(7) UNIQUE NOT NULL
);

INSERT INTO time_slot (name) VALUES
('lunch'), ('dinner');

CREATE TABLE food_type (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(8) UNIQUE NOT NULL
);

INSERT INTO food_type (name) VALUES
('Plats'), ('Boissons'), ('Desserts');


----- FOOD -----

CREATE TABLE food (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(30) NOT NULL,
    food_type INT,
    price FLOAT NOT NULL,
    description VARCHAR(255) NOT NULL,
    image_path VARCHAR(50) NOT NULL,
    nutriscore_id INT,
    FOREIGN KEY(food_type) REFERENCES food_type(id),
    FOREIGN KEY(nutriscore_id) REFERENCES nutriscore(id)
);

INSERT INTO food (name, food_type, price, description, image_path) VALUES
('Crabe', 1, 21.7, 'Chair de crabe fraîche et délicate, un délice à savourer brique par brique !', 'images/food/dish/crab.jpg'),
('Bento', 1, 11.5, 'Assortiment coloré de riz, légumes et protéines, un repas complet et joyeux.', 'images/food/dish/bento.jpg'),
('Sushis', 1, 9.3, 'Riz vinaigré et poisson frais, préparés avec soin pour une bouchée pleine de finesse.', 'images/food/dish/sushi.jpg'),
('Sashimis', 1, 15.9, 'Tranches de poisson ultra-frais, fondantes et prêtes à être dégustées en une bouchée.', 'images/food/dish/sashimi.webp'),
('Steak', 1, 13.5, 'Pièce de bœuf juteuse et grillée à la perfection, une vraie brique de gourmandise.', 'images/food/dish/steak.jpg'),
('Burger enfant', 1, 5, 'Mini-burger tendre et fromage fondant, parfait pour les petits appétits.', 'images/food/dish/kid_burger.jpg'),
('Burger végé', 1, 10.2, 'Galette de légumes savoureuse et pain moelleux, un burger coloré et fun à manger.', 'images/food/dish/veggy_burger.jpg'),
('Burger frites', 1, 14.6, 'Burger gourmand avec frites dorées et croustillantes, un classique qui se déguste sans effort.', 'images/food/dish/burger.png'),
('Pâtes', 1, 4, 'Pâtes al dente nappées d’une sauce riche et parfumée, simples mais toujours délicieuses.', 'images/food/dish/noodles.jpg'),
('Œuf & Steak', 1, 9.5, 'Œuf coulant sur steak haché tendre, un duo réconfortant et délicieux.', 'images/food/dish/egg.jpg'),
('Crevettes', 1, 10.8, 'Crevettes sautées, légèrement relevées, pleines de goût et de fraîcheur.', 'images/food/dish/shrimp.jpg'),
('Bacon & Œufs', 1, 6.5, 'Bacon croustillant et œufs dorés, un petit déjeuner ou dîner plein de gourmandise.', 'images/food/dish/bacon.jpg'),
('Coca-Cola', 2, 2.3, 'Boisson fraîche et pétillante, parfaite pour accompagner chaque bouchée.', 'images/food/drink/coke.jpg'),
('Jus d''orange', 2, 1.5, 'Jus maison fruité et vitaminé, idéal pour une pause rafraîchissante.', 'images/food/drink/orange_juice.jpg'),
('Champagne', 2, 25.9, 'Champagne de la maison de la brique. Bulles fines et élégantes, pour un moment festif et léger.', 'images/food/drink/champaign.png'),
('Fromages', 3, 8.5, 'Sélection de fromages affinés, à savourer brique par brique… ou tout d’un coup !', 'images/food/dessert/cheese.jpg'),
('Tarte', 3, 4.2, 'Tarte à la framboise. Pâte croustillante et framboises acidulées, douces et fruitées.', 'images/food/dessert/raspberry_pie.jpg'),
('Glace', 3, 3.3, 'Boules glacées onctueuses, parfaites pour une pause fraîcheur.', 'images/food/dessert/ice_cream.jpg'),
('Gâteau', 3, 4.7, 'Gâteau moelleux et gourmand, un vrai plaisir à chaque bouchée.', 'images/food/dessert/cake.jpg'),
('Cupcake', 3, 2.3, 'Petit gâteau fondant, recouvert d’un glaçage généreux et coloré.', 'images/food/dessert/cupcake.jpg'),
('Dessert glacé', 3, 4.7, 'Alliance sucrée et rafraîchissante, pour les plus gourmands.', 'images/food/dessert/cupcakes_ice_cream.jpg'),
('Banane', 3, 0.9, 'Simple et naturellement sucrée, parfaite en dessert ou en encas.', 'images/food/dessert/banana.jpg');

-- Table de liaison pour les allergènes
CREATE TABLE food_allergen (
    food_id INT,
    allergen_id INT,
    PRIMARY KEY (food_id, allergen_id),
    FOREIGN KEY (food_id) REFERENCES food(id) ON DELETE CASCADE,
    FOREIGN KEY (allergen_id) REFERENCES allergen(id) ON DELETE CASCADE
);


----- MENU -----

CREATE TABLE menu (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(30) NOT NULL,
    price FLOAT NOT NULL,
    min_people INT,
    time_slot_id INT,
    FOREIGN KEY(time_slot_id) REFERENCES time_slot(id)
);

INSERT INTO menu (name, price, min_people, time_slot_id) VALUES
('Menu enfant', 7.5, 1, NULL),
('Partage d''Asie', 32, 3, 1);

-- Table de liaison entre Menu et Food
CREATE TABLE menu_food (
    menu_id INT,
    food_id INT,
    quantity INT DEFAULT 1,
    PRIMARY KEY (menu_id, food_id),
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES food(id) ON DELETE CASCADE
);

INSERT INTO menu_food (menu_id, food_id, quantity) VALUES
-- Menu enfant
(1, 6, 1), -- Burger enfant
(1, 12, 1), -- Coca
(1, 22, 1), -- Cupcake
-- Partage d'Asie
(2, 11, 1), -- Crevettes
(2, 4, 1), -- Sashimi
(2, 3, 1), -- Sushi
(2, 17, 2), -- Ice cream (x2)
(2, 19, 1); -- Cake


----- ROLES et USERS -----

CREATE TABLE role (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(15) UNIQUE NOT NULL
);

INSERT INTO role (name) VALUES
('client'), ('restaurateur'), ('administrator'), ('delivery_person');

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    role_id INT NOT NULL,
    phone VARCHAR(20),
    birth_date DATE,
    inscription_date DATETIME,
    last_connection DATETIME,
    street_nb INT,
    street_nb_suf VARCHAR(3),
    street VARCHAR(50),
    town VARCHAR(50),
    zip_code INT,
    intercom_code VARCHAR(50),
    FOREIGN KEY(role_id) REFERENCES role(id)
);


----- COUPONS -----

CREATE TABLE coupon (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE NOT NULL,
    discount_percent FLOAT,
    expiration_date DATETIME
);


----- PANIER / COMMANDE -----


CREATE TABLE payment_status (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(20) UNIQUE NOT NULL
);

INSERT INTO payment_status (name) VALUES
('pending'), ('paid'), ('failed');


-- Le panier
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    payment_status_id INT,
    created_at DATETIME NOT NULL,
    coupon_id INT,
    FOREIGN KEY(payment_status_id) REFERENCES payment_status(id),
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(coupon_id) REFERENCES coupon(id)
);

-- Table de liaison entre les menus et le panier
CREATE TABLE cart_menu (
    cart_id INT,
    menu_id INT,
    quantity INT DEFAULT 1,
    PRIMARY KEY (cart_id, menu_id),
    FOREIGN KEY (cart_id) REFERENCES cart(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE
);


----- PAIEMENT -----

CREATE TABLE payment (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cart_id INT NOT NULL,
    user_id INT NOT NULL,
    bank_details_token VARCHAR(255) NOT NULL,
    transaction_date DATETIME NOT NULL,
    amount FLOAT NOT NULL,
    FOREIGN KEY(cart_id) REFERENCES cart(id),
    FOREIGN KEY(user_id) REFERENCES users(id)
);



----- AVIS -----

CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_stars TINYINT(3) NOT NULL,
    delivery_stars TINYINT(3) NOT NULL,
    comment VARCHAR(255) NOT NULL,
    FOREIGN KEY(user_id) REFERENCES users(id)
);
