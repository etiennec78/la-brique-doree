#!/bin/bash

DB_NAME="brique_doree"
SQL_FILE="schema.sql"

sudo systemctl start mariadb

mariadb --version

if [ $? -ne 0 ]; then
    echo "Something went wrong ! Do you have mariadb installed on your system ?"
    exit 2
fi

DB_EXISTS=$(sudo mariadb -u root -e "SHOW DATABASES LIKE '$DB_NAME';" | grep "$DB_NAME")

if [ ! -z "$DB_EXISTS" ]; then
    echo "A database already exists. Are you sure you want to replace it by an empty one ?"
    echo "Type 'DELETE' to continue"
    read confirmation
    if [[ "$confirmation" == "DELETE" ]]; then
        echo "Deleting the previous database..."
        sudo mariadb -e "DROP DATABASE $DB_NAME;"
    else
        echo "The database was not deleted. Exiting..."
        exit 1
    fi
fi

echo "Making the database from $SQL_FILE..."
sudo mariadb -e "CREATE DATABASE $DB_NAME;"

if [ $? -ne 0 ]; then
    echo "Error: Could not create database $DB_NAME !"
    exit 4
fi

sudo mariadb "$DB_NAME" < "$SQL_FILE"

if [ $? -ne 0 ]; then
    echo "Error: Could not import $SQL_FILE into the $DB_NAME database !"
    exit 5
fi

if [ $? -eq 0 ]; then
    echo "----------------------------------------------"
    echo "Success ! The database '$DB_NAME' was created."
    echo "The MariaDB service is running."
else
    echo "The database could not be created !"
    exit 3
fi
