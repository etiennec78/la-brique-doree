-- ------- ALLERGENES -------

CREATE TABLE allergen (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(25) UNIQUE NOT NULL
);

INSERT INTO allergen (name) VALUES
('crustacean'), ('fish'), ('gluten'), ('milk'), ('sesame'), ('egg'), ('soy'), ('nut'), ('sulfite');


-- ------- TIME_SLOT and FOOD_TYPE -------

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


-- ------- FOOD -------

CREATE TABLE food (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(30) NOT NULL,
    food_type INT,
    price FLOAT NOT NULL,
    description VARCHAR(255) NOT NULL,
    image_path VARCHAR(50) NOT NULL,
    FOREIGN KEY(food_type) REFERENCES food_type(id)
);

INSERT INTO food (name, food_type, price, description, image_path) VALUES
('Crabe', 1, 19.9, 'Chair de crabe fraîche et délicate, un délice à savourer brique par brique !', 'images/food/dish/crab.jpg'),
('Bento', 1, 11.9, 'Assortiment coloré de riz, légumes et protéines, un repas complet et joyeux.', 'images/food/dish/bento.jpg'),
('Sushis', 1, 8.9, 'Riz vinaigré et poisson frais, préparés avec soin pour une bouchée pleine de finesse.', 'images/food/dish/sushi.jpg'),
('Sashimis', 1, 14.9, 'Tranches de poisson ultra-frais, fondantes et prêtes à être dégustées en une bouchée.', 'images/food/dish/sashimi.webp'),
('Steak', 1, 13.9, 'Pièce de bœuf juteuse et grillée à la perfection, une vraie brique de gourmandise.', 'images/food/dish/steak.jpg'),
('Burger enfant', 1, 4.9, 'Mini-burger tendre et fromage fondant, parfait pour les petits appétits.', 'images/food/dish/kid_burger.jpg'),
('Burger végé', 1, 9.9, 'Galette de légumes savoureuse et pain moelleux, un burger coloré et fun à manger.', 'images/food/dish/veggy_burger.jpg'),
('Burger frites', 1, 14.9, 'Burger gourmand avec frites dorées et croustillantes, un classique qui se déguste sans effort.', 'images/food/dish/burger.png'),
('Pâtes', 1, 3.9, 'Pâtes al dente nappées d’une sauce riche et parfumée, simples mais toujours délicieuses.', 'images/food/dish/noodles.jpg'),
('Œuf & Steak', 1, 9.9, 'Œuf coulant sur steak haché tendre, un duo réconfortant et délicieux.', 'images/food/dish/egg.jpg'),
('Crevettes', 1, 9.9, 'Crevettes sautées, légèrement relevées, pleines de goût et de fraîcheur.', 'images/food/dish/shrimp.jpg'),
('Bacon & Œufs', 1, 5.9, 'Bacon croustillant et œufs dorés, un petit déjeuner ou dîner plein de gourmandise.', 'images/food/dish/bacon.jpg'),
('Coca-Cola', 2, 2.9, 'Boisson fraîche et pétillante, parfaite pour accompagner chaque bouchée.', 'images/food/drink/coke.jpg'),
('Jus d''orange', 2, 1.9, 'Jus maison fruité et vitaminé, idéal pour une pause rafraîchissante.', 'images/food/drink/orange_juice.jpg'),
('Champagne', 2, 24.9, 'Champagne de la maison de la brique. Bulles fines et élégantes, pour un moment festif et léger.', 'images/food/drink/champaign.png'),
('Fromages', 3, 19.9, 'Sélection de fromages affinés, à savourer brique par brique… ou tout d’un coup !', 'images/food/dessert/cheese.jpg'),
('Tarte', 3, 4.9, 'Tarte à la framboise. Pâte croustillante et framboises acidulées, douces et fruitées.', 'images/food/dessert/raspberry_pie.jpg'),
('Glace', 3, 2.9, 'Boules glacées onctueuses, parfaites pour une pause fraîcheur.', 'images/food/dessert/ice_cream.jpg'),
('Gâteau', 3, 4.9, 'Gâteau moelleux et gourmand, un vrai plaisir à chaque bouchée.', 'images/food/dessert/cake.jpg'),
('Cupcake', 3, 2.9, 'Petit gâteau fondant, recouvert d’un glaçage généreux et coloré.', 'images/food/dessert/cupcake.jpg'),
('Dessert glacé', 3, 4.9, 'Alliance sucrée et rafraîchissante, pour les plus gourmands.', 'images/food/dessert/cupcakes_ice_cream.jpg'),
('Banane', 3, 0.9, 'Simple et naturellement sucrée, parfaite en dessert ou en encas.', 'images/food/dessert/banana.jpg');

-- Linking table for allergenes
CREATE TABLE food_allergen (
    food_id INT,
    allergen_id INT,
    PRIMARY KEY (food_id, allergen_id),
    FOREIGN KEY (food_id) REFERENCES food(id) ON DELETE CASCADE,
    FOREIGN KEY (allergen_id) REFERENCES allergen(id) ON DELETE CASCADE
);

INSERT INTO food_allergen (food_id, allergen_id) VALUES
(1, 1), -- Crab: crustacean
(2, 1), -- Bento: crustacean
(3, 2), -- Sushis: fish
(4, 2), -- Sashimi: fish
 -- Kid burger: gluten, milk, sesame
(6, 3),
(6, 4),
(6, 5),
-- Veggy burger: gluten, sesame, soy
(7, 3),
(7, 5),
(7, 7),
-- Burger fries: gluten, milk, sesame
(8, 3),
(8, 4),
(8, 5),
(10, 6), -- Egg & Steak: egg
(11, 1), -- Shrimp: crustacean
 -- Bacon & Eggs: gluten, egg
(12, 3),
(12, 6),
(15, 9), -- Champaign: sulfite
(16, 4), -- Chese: milk
-- Raspberry pie: gluten, milk, egg
(17, 3),
(17, 4),
(17, 6),
-- Ice Cream: milk, soy, nut
(18, 4),
(18, 7),
(18, 8),
-- Cake: gluten, egg, soy
(19, 3),
(19, 6),
(19, 7),
-- Cupcake: gluten, milk, egg, soy
(20, 3),
(20, 4),
(20, 6),
(20, 7),
-- Cupcake & Ice Cream: gluten, milk, egg, soy
(21, 3),
(21, 4),
(21, 6),
(21, 7);


-- ------- MENU -------

CREATE TABLE menu (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(30) NOT NULL,
    description VARCHAR(255) NOT NULL,
    price FLOAT NOT NULL,
    min_people INT,
    time_slot_id INT,
    FOREIGN KEY(time_slot_id) REFERENCES time_slot(id)
);

INSERT INTO menu (name, description, price, min_people, time_slot_id) VALUES
('Menu enfant', 'Un menu pour les futurs petits minis micro prochains constructeurs !' , 7.9, 1, NULL),
('Partage d''Asie', 'Un menu qui vous fera voyager dans un autre continent', 37.9, 3, 1),
('Menu gourmand', 'Un menu pour les gourmands, arriverez-vous à le terminer ?', 19.9, 1, NULL),
('Menu doré', 'Un menu pour les palais les plus fins', 54.9, 1, 2);

-- Linking table between Menu and Food
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
(1, 14, 1), -- Jus d'orange
(1, 20, 1), -- Cupcake
-- Partage d'Asie
(2, 11, 1), -- Crevettes
(2, 4, 1), -- Sashimi
(2, 3, 1), -- Sushi
(2, 18, 2), -- Ice cream (x2)
(2, 19, 1), -- Cake
-- Menu gourmand
(3, 12, 1), -- Bacon & Oeufs
(3, 8, 1), -- Burger frites
(3, 13, 1), -- Coca Cola
-- Menu doré
(4, 1, 1), -- Crabe
(4, 15, 1), -- Champagne
(4, 16, 1); -- Plateau de fromages

-- ------- ROLES and USERS -------

CREATE TABLE role (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(15) UNIQUE NOT NULL
);

INSERT INTO role (name) VALUES
('client'), ('cook'), ('administrator'), ('delivery_person');

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    banned BOOLEAN NOT NULL DEFAULT FALSE,
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
    latitude FLOAT DEFAULT NULL,
    longitude FLOAT DEFAULT NULL,
    intercom_code VARCHAR(50),
    global_reduction FLOAT NOT NULL DEFAULT 0,
    last_api_call DATETIME NOT NULL DEFAULT 0,
    successive_api_calls INT NOT NULL DEFAULT 0,
    FOREIGN KEY(role_id) REFERENCES role(id)
);


-- ------- COUPONS -------

CREATE TABLE coupon (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE NOT NULL,
    discount_percent FLOAT,
    expiration_date DATETIME
);


-- ------- STATUS (PAYMENTS AND DELIVERY) -------

CREATE TABLE payment_status (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(20) UNIQUE NOT NULL
);

INSERT INTO payment_status (name) VALUES
('pending'), ('paid');

CREATE TABLE order_status (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(20) UNIQUE NOT NULL
);

INSERT INTO order_status (name) VALUES
('paid'), ('preparing'), ('ready'), ('shipping'), ('delivered');


-- ------- THE CART -------

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


-- ------- THE ORDER (Link Cook / Delivery-Person) -------

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,               
    customer_id INT NOT NULL,           
    cook_id INT DEFAULT NULL,
    delivery_person_id INT DEFAULT NULL,
    order_status_id INT DEFAULT 1, -- Correspond à 'paid' par défaut
    is_takeaway BOOLEAN NOT NULL DEFAULT FALSE,
    takeaway_time DATETIME DEFAULT NULL,
    cook_assigned_at DATETIME DEFAULT NULL,
    delivery_person_assigned_at DATETIME DEFAULT NULL,
    
    FOREIGN KEY (cart_id) REFERENCES cart(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (cook_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (delivery_person_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (order_status_id) REFERENCES order_status(id)
);


-- ------- CART CONTENT (Links) -------

CREATE TABLE cart_menu (
    cart_id INT,
    menu_id INT,
    quantity INT DEFAULT 1,
    PRIMARY KEY (cart_id, menu_id),
    FOREIGN KEY (cart_id) REFERENCES cart(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE
);

CREATE TABLE cart_food (
    cart_id INT,
    food_id INT,
    quantity INT DEFAULT 1,
    PRIMARY KEY (cart_id, food_id),
    FOREIGN KEY (cart_id) REFERENCES cart(id) ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES food(id) ON DELETE CASCADE
);


-- ------- PAYMENT -------

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



-- ------- REVIEWS -------

CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_stars TINYINT(3) NOT NULL,
    delivery_stars TINYINT(3),
    comment VARCHAR(255) NOT NULL,
    FOREIGN KEY(order_id) REFERENCES orders(id)
);

-- ------- DELIVERY CANCELLATION -------

CREATE TABLE delivery_cancellation (
    order_id INT,
    delivery_person_id INT,
    PRIMARY KEY (order_id, delivery_person_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (delivery_person_id) REFERENCES users(id) ON DELETE CASCADE
);
