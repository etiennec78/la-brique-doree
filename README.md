# La brique dorée 🧱
This website is a school project for a fictional restaurant that specializes in serving "trompe l'oeil" dishes that look like LEGO bricks.

The site handles all aspects of the restaurant's operations, from customers and cooks to delivery drivers and administrators.

This project is made with PHP+JS+CSS, and runs with a MariaDB SQL database and some bash scripts.

## List of default main accounts
| Email | Password | Role |
| :---: | :---: | :---: |
| client@gmail.com | root | Client |
| cook@gmail.com | root | Cook |
| administrator@gmail.com | root | Administrator |
| delivery_person@gmail.com | root | Delivery person |

## List of default coupons
| Coupon | Discount | Expiration |
| :---: | :---: | :---: |
| GRIGNON75 | 75% | active |
| LEBRETON75 | 75% | active |
| INVALID | 75% | expired |

## How to setup the server (debian)

1. Execute `sudo apt update && sudo apt install php-mysql mariadb`
2. Execute `sudo systemctl start mariadb.service`
3. Execute `sudo mariadb-secure-installation`
4. Execute `sudo nano /etc/php/php.ini`
5. Press CTRL+W, type "extension=pdo_mysql", remove the leading ";", press CTRL+W, CTRL+Q
6. Open the project's directory, then execute `cd db && chmod +x make_db.sh && ./make_db.sh`
7. Optional: Copy the file "./config/settings.example.json" to "./config/settings.json" and edit this new file to suit your needs

## How to start the server

1. Open the project's directory
2. Execute `cd public`
3. Execute `sudo systemctl start mariadb.service`
4. Execute `sudo php -S localhost:80`
