#!/bin/bash

DB_NAME="brique_doree.db"
SQL_FILE="schema.sql"

if [ -f "$DB_NAME" ]; then
    echo "A database already exists. Are you sure you want to replace it by an empty one ?"
    echo "Type 'DELETE' to continue"
    read confirmation
    if [[ "$confirmation" == "DELETE" ]]; then
        echo "Deleting the previous database..."
        rm "$DB_NAME"
    else
        echo "The database was not deleted. Exiting..."
        exit 1
    fi
fi

echo "Making the database from $SQL_FILE..."
sqlite3 "$DB_NAME" < "$SQL_FILE"

if [ $? -ne 0 ]; then
    echo "Something went wrong ! Do you have sqlite3 installed on your system ?"
    exit 2
fi

if [ -f "$DB_NAME" ]; then
    echo "Success ! The database '$DB_NAME' was created."
else
    echo "The database could not be created !"
    exit 3
fi
