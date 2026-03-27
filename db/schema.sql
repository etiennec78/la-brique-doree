----- ALLERGENES -----

CREATE TABLE allergen (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(25) UNIQUE NOT NULL
);

INSERT INTO allergen (name) VALUES
('crustacean'), ('fish'), ('gluten'), ('milk'), ('sesame'), ('egg'), ('soy'), ('nut'), ('sulfite');


----- NUTRISCORE et TIME_SLOT -----

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


----- FOOD -----

CREATE TABLE food (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(30) NOT NULL,
    price FLOAT NOT NULL,
    description VARCHAR(255) NOT NULL,
    image_path VARCHAR(50) NOT NULL,
    nutriscore_id INT,
    FOREIGN KEY(nutriscore_id) REFERENCES nutriscore(id)
);

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

-- Table de liaison entre Menu et Food
CREATE TABLE menu_food (
    menu_id INT,
    food_id INT,
    PRIMARY KEY (menu_id, food_id),
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES food(id) ON DELETE CASCADE
);


----- ROLES et USERS -----

CREATE TABLE role (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(15) UNIQUE NOT NULL
);

INSERT INTO role (name) VALUES
('client'), ('restaurateur'), ('administrator'), ('delivery_person');

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    role_id INT NOT NULL,
    phone VARCHAR(20),
    birth_date DATE,
    inscription_date DATETIME,
    last_connection DATETIME,
    street_nb INT,
    street_nb_ext VARCHAR(3),
    street VARCHAR(50),
    town VARCHAR(50),
    zip_code INT,
    FOREIGN KEY(role_id) REFERENCES role(id)
);


----- COUPONS -----

CREATE TABLE coupon (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE NOT NULL,
    discount_percent FLOAT,
    is_active TINYINT(1) DEFAULT 1,
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
