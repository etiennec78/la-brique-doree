// User.php
<?php
require_once __DIR__ . '/../db_connect.php';

class User {
    public static function findByEmail($email) {
	/*
	 	
	  INPUT :
	         
   	 	 (str) $email : variable representing the user's email address
	  
	  OUTPUT :

 		 (array|bool) $user : variable representing the user record and role name, or false if not found

	  
	  SUMMARY :
	 	
		This function searches for a user by email address and retrieves their details including their role name.

	*/
        global $pdo;
        $stmt = $pdo->prepare('SELECT u.*, r.name as role_name FROM users u JOIN role r ON u.role_id = r.id WHERE u.email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($email, $password) {
	/*
	 	
	  INPUT :
	         
   	 	 (str) $email : variable representing the user's email address
		 (str) $password : variable representing the plain text password
	  
	  OUTPUT :

 		 (string) $user_id : variable representing the newly created user ID

	  
	  SUMMARY :
	 	
		This function registers a new user in the database with a securely hashed password and the default role.

	*/
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role_id, inscription_date) VALUES (?, ?, 1, NOW())");
        $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);
        return $pdo->lastInsertId();
    }

    public static function getAllUsersInfo() {
	/*
	 	
	  INPUT :
	         
   	 	 None
	  
	  OUTPUT :

 		 (array) $users : variable representing an array of user information records, including order counts

	  
	  SUMMARY :
	 	
		This function retrieves summary details for all registered users, including their role and the total number of orders placed.

	*/
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT u.id, u.email, u.first_name, u.last_name, u.global_reduction, u.banned, r.name AS role, COUNT(o.id) as orders
            FROM users u
            LEFT JOIN orders o on u.id = o.customer_id
            LEFT JOIN role r ON u.role_id = r.id
            GROUP BY u.id, u.email, u.first_name, u.last_name, r.name
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function setAllUserData($first_name, $last_name, $street_nb, $street_nb_suf, $street, $zip_code, $phone, $email, $intercom, $birth_date, $uid) {
	/*
	 	
	  INPUT :
	         
   	 	 (str) $first_name : variable representing the first name
		 (str) $last_name : variable representing the last name
		 (int|str) $street_nb : variable representing the street number
		 (str|null) $street_nb_suf : variable representing the street number suffix
		 (str) $street : variable representing the street name
		 (str) $zip_code : variable representing the ZIP/postal code
		 (str) $phone : variable representing the phone number
		 (str) $email : variable representing the email address
		 (str|null) $intercom : variable representing the intercom code
		 (str) $birth_date : variable representing the birth date (YYYY-MM-DD)
		 (int) $uid : variable representing the user ID
	  
	  OUTPUT :

 		 None

	  
	  SUMMARY :
	 	
		This function updates the entire personal profile and address details of a specific user.

	*/
        global $pdo;
        $stmt = $pdo->prepare("
            UPDATE users
            SET first_name=?, last_name=?, street_nb=?, street_nb_suf=?, street=?, zip_code=?, phone=?, email=?, intercom_code=?, birth_date=?
            WHERE id = ?
        ");
        $stmt->execute([$first_name, $last_name, $street_nb, $street_nb_suf, $street, $zip_code, $phone, $email, $intercom, $birth_date, $uid]);
    }

    public static function getUserInfo($uid) {
	/*
	 	
	  INPUT :
	         
   	 	 (int) $uid : variable representing the user ID
	  
	  OUTPUT :

 		 (array|bool) $user : variable representing the user profile data array, or false if not found

	  
	  SUMMARY :
	 	
		This function retrieves a detailed set of profile and address information for a given user ID.

	*/
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT email, first_name, last_name, phone, birth_date, street_nb, street_nb_suf, street, town, zip_code, intercom_code, banned, id
            FROM users u
            WHERE u.id = ?
        ");
        $stmt->execute([$uid]);
        return $stmt->fetch();
    }

    public static function hasValidInfo($uid) {
	/*
	 	
	  INPUT :
	         
   	 	 (int) $uid : variable representing the user ID
	  
	  OUTPUT :

 		 (bool) $is_valid : variable representing whether all required contact info fields are filled

	  
	  SUMMARY :
	 	
		This function verifies that a user has provided non-empty values for essential contact fields (email, first name, last name, phone).

	*/
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT email, first_name, last_name, phone
            FROM users
            WHERE id = ?
        ");
        $stmt->execute([$uid]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return false;
        }
        foreach ($user as $value) {
            if ($value === null || $value === '') {
                return false;
            }
        }
        return true;
    }

    public static function hasValidAddress($uid) {
	/*
	 	
	  INPUT :
	         
   	 	 (int) $uid : variable representing the user ID
	  
	  OUTPUT :

 		 (bool) $has_address : variable representing whether all required address fields are filled

	  
	  SUMMARY :
	 	
		This function verifies that a user has filled out the mandatory address fields (street number, street name, zip code).

	*/
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT street_nb, street, zip_code
            FROM users
            WHERE id = ?
        ");
        $stmt->execute([$uid]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return false;
        }
        foreach ($user as $value) {
            if ($value === null || $value === '') {
                return false;
            }
        }
        return true;
    }

    public static function getUsersFromRole($role) {
	/*
	 	
	  INPUT :
	         
   	 	 (str) $role : variable representing the role name to filter by
	  
	  OUTPUT :

 		 (array) $users : variable representing an array of users who match the role

	  
	  SUMMARY :
	 	
		This function retrieves a list of user IDs and names belonging to a specified role.

	*/
        global $pdo;
        $stmt_users = $pdo->prepare("
            SELECT u.id, u.first_name, u.last_name
            FROM users u
            JOIN role r on u.role_id = r.id
            WHERE r.name = ?
        ");
        $stmt_users->execute([$role]);
        return $stmt_users->fetchAll();
    }

    public static function getGlobalReduction($uid) {
	/*
	 	
	  INPUT :
	         
   	 	 (int) $uid : variable representing the user ID
	  
	  OUTPUT :

 		 (float|int|bool) $reduction : variable representing the global reduction value, or false if not found

	  
	  SUMMARY :
	 	
		This function retrieves the global reduction rate assigned to a specific user.

	*/
        global $pdo;
        $stmt = $pdo->prepare("SELECT global_reduction FROM users u WHERE u.id = ?");
        $stmt->execute([$uid]);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public static function checkKey($key) {
	/*
	 	
	  INPUT :
	         
   	 	 (str) $key : variable representing the column/key name to check
	  
	  OUTPUT :

 		 None

	  
	  SUMMARY :
	 	
		This function acts as a whitelist safeguard to ensure only allowed column names are used in dynamic queries, throwing an exception otherwise.

	*/
        $allowedKeys = ['last_api_call', 'r.name', 'global_reduction', 'latitude', 'longitude', 'banned'];

        if (!in_array($key, $allowedKeys)) {
            throw new InvalidArgumentException("Nom de colonne non autorisé.");
        }
    }

    public static function setUserData($uid, $key, $value) {
	/*
	 	
	  INPUT :
	         
   	 	 (int) $uid : variable representing the user ID
		 (str) $key : variable representing the column name to update
		 (mixed) $value : variable representing the value to store
	  
	  OUTPUT :

 		 None

	  
	  SUMMARY :
	 	
		This function dynamically updates a single allowed column value for a specified user after verifying the key against a whitelist.

	*/
        global $pdo;
        self::checkKey($key);

        $stmt = $pdo->prepare("
            UPDATE users
            SET $key = ?
            WHERE id = ?
        ");
        $stmt->execute([$value, $uid]);
    }

    public static function getUserData($uid, $key, $default_value = NULL) {
	/*
	 	
	  INPUT :
	         
   	 	 (int) $uid : variable representing the user ID
		 (str) $key : variable representing the column name to retrieve
		 (mixed) $default_value : variable representing the fallback value if no result is found
	  
	  OUTPUT :

 		 (mixed) $result : variable representing the fetched column value or the default fallback value

	  
	  SUMMARY :
	 	
		This function retrieves a specific column value for a user from a whitelisted set of columns, falling back to a default value if unavailable.

	*/
        global $pdo;
        self::checkKey($key);

        $stmt = $pdo->prepare("
            SELECT $key
            FROM users u
            JOIN role r ON r.id = u.role_id
            WHERE u.id = ?
        ");
        $stmt->execute([$uid]);
        $result = $stmt->fetch(PDO::FETCH_COLUMN);
        return $result ? $result : $default_value;
    }

    public static function isAdmin($uid) {
	/*
	 	
	  INPUT :
	         
   	 	 (int) $uid : variable representing the user ID
	  
	  OUTPUT :

 		 (bool) $is_admin : variable representing whether the user has administrator privileges

	  
	  SUMMARY :
	 	
		This function checks if a given user ID belongs to an administrator role.

	*/
        $role = self::getUserData($uid, 'r.name');
        return $role == 'administrator';
    }

    public static function incrementSuccessiveAPICalls($uid) {
	/*
	 	
	  INPUT :
	         
   	 	 (int) $uid : variable representing the user ID
	  
	  OUTPUT :

 		 None

	  
	  SUMMARY :
	 	
		This function increments the count of successive API calls for a user and automatically bans them if they exceed the threshold of 10 calls.

	*/
        $successive = User::getUserData($uid, 'successive_api_calls', 0);
        $successive += 1;

        if ($successive >= 10)
            User::setUserData($uid, 'banned', 1);

        User::setUserData($uid, 'successive_api_calls', $successive);
    }

    public static function passwordVerification($password) {
	/*
	 	
	  INPUT :
	         
   	 	 (str) $password : variable representing the plain text password to check
	  
	  OUTPUT :

 		 (array) $verifications : variable representing an associative array of boolean password strength criteria checks

	  
	  SUMMARY :
	 	
		This function checks a plain text password against various security requirements like length, uppercase, lowercase, numbers, and special characters.

	*/
        $verifications = [
            'length' => 0,
            'uppercase' => 0,
            'lowercase' => 0,
            'number' => 0,
            'special' => 0,

        ];

        $password = trim($password);

        if (strlen($password) > 8) {
            $verifications['length'] = 1;
        }

        $verifications['uppercase'] = preg_match('/[A-Z]/', $password);
        $verifications['lowercase'] = preg_match('/[a-z]/', $password) ;
        $verifications['number'] = preg_match('/\d/', $password) ;
        $verifications['special'] = preg_match('/\W/', $password) ;

        return $verifications;
    }
}
