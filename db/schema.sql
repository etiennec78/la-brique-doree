----- ALLERGENES -----

CREATE TABLE allergen (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT UNIQUE NOT NULL
);

INSERT INTO allergen (name) VALUES
('crustacean'), ('fish'), ('gluten'), ('milk'), ('sesame'), ('egg'), ('soy'), ('nut'), ('sulfite');


----- NUTRISCORE et TIME_SLOT -----

CREATE TABLE nutriscore (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name char(1) UNIQUE NOT NULL
);

INSERT INTO nutriscore (name) VALUES
('A'), ('B'), ('C'), ('D'), ('E');

CREATE TABLE time_slot (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT UNIQUE NOT NULL
);

INSERT INTO time_slot (name) VALUES
('lunch'), ('dinner');


----- FOOD -----

CREATE TABLE food (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name varchar(30) NOT NULL,
    price float NOT NULL,
    description varchar(255) NOT NULL,
    image_path varchar(50) NOT NULL,
    nutriscore_id INTEGER,
    FOREIGN KEY(nutriscore_id) REFERENCES nutriscore(id)
);

-- Table de liaison pour les allergènes
CREATE TABLE food_allergen (
    food_id INTEGER,
    allergen_id INTEGER,
    PRIMARY KEY (food_id, allergen_id),
    FOREIGN KEY (food_id) REFERENCES food(id) ON DELETE CASCADE,
    FOREIGN KEY (allergen_id) REFERENCES allergen(id) ON DELETE CASCADE
);


----- MENU -----

CREATE TABLE menu (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name varchar(30) NOT NULL,
    price float NOT NULL,
    min_people INTEGER,
    time_slot_id INTEGER,
    FOREIGN KEY(time_slot_id) REFERENCES time_slot(id)
);

-- Table de liaison entre Menu et Food
CREATE TABLE menu_food (
    menu_id INTEGER,
    food_id INTEGER,
    PRIMARY KEY (menu_id, food_id),
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES food(id) ON DELETE CASCADE
);


----- ROLES et USERS -----

CREATE TABLE role (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT UNIQUE NOT NULL
);

INSERT INTO role (name) VALUES
('client'), ('restaurateur'), ('administrator'), ('delivery_person');

CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email varchar(255) NOT NULL,
    password_hash varchar(255) NOT NULL,
    name varchar(255),
    role_id INTEGER NOT NULL,
    phone varchar(20),
    birth_date DATE,
    inscription_date DATETIME,
    last_connection DATETIME,
    street_nb INTEGER,
    street_nb_ext varchar(3),
    street TEXT,
    town TEXT,
    zip_code INTEGER,
    FOREIGN KEY(role_id) REFERENCES role(id)
);


----- COUPONS -----

CREATE TABLE coupon (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code varchar(20) UNIQUE NOT NULL,
    discount_percent float,
    is_active BOOLEAN DEFAULT 1,
    expiration_date DATETIME
);


----- PANIER / COMMANDE -----


CREATE TABLE payment_status (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name varchar(20) UNIQUE NOT NULL
);

INSERT INTO payment_status (name) VALUES
('pending'), ('paid'), ('failed');


-- Le panier
CREATE TABLE cart (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    delivery_address TEXT,
    payment_status_id INTEGER,
    created_at DATETIME NOT NULL,
    coupon_id INTEGER,
    FOREIGN KEY(payment_status_id) REFERENCES payment_status(id),
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(coupon_id) REFERENCES coupon(id)
);

-- Table de liaison entre les menus et le panier
CREATE TABLE cart_menu (
    cart_id INTEGER,
    menu_id INTEGER,
    quantity INTEGER DEFAULT 1,
    PRIMARY KEY (cart_id, menu_id),
    FOREIGN KEY (cart_id) REFERENCES cart(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE
);


----- PAIEMENT -----

CREATE TABLE payment (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    cart_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    bank_details_token varchar(255) NOT NULL,
    transaction_date DATETIME NOT NULL,
    amount float NOT NULL,
    FOREIGN KEY(cart_id) REFERENCES cart(id),
    FOREIGN KEY(user_id) REFERENCES users(id)
);
