<?php

class AuthController extends Controller
{
  public function showLogin()
  {
    $this->render('login');
  }

  public function processLogin()
  {
    require_once __DIR__ . '/../models/User.php';

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
      $this->render('login', ['error' => 'Veuillez remplir tous les champs.']);
      return;
    }

    $user_found = User::findByEmail($email);

    if ($user_found && password_verify($password, $user_found['password_hash'])) {
      if (!empty($user_found['banned'])) {
        $this->render('login', ['error' => 'Votre compte a été banni.']);
        return;
      }
      
      $user_found['role'] = $user_found['role_name'];
      
      $_SESSION['user'] = $user_found;

      if ($user_found['role'] === 'administrator') {
          header("Location: /admin");
      } elseif ($user_found['role'] === 'cook') {
          header("Location: /cook");
      } elseif ($user_found['role'] === 'delivery_person') {
          header("Location: /delivery");
      } else {
          header("Location: /");
      }
      exit;
    } else {
      $this->render('login', ['error' => 'Email ou mot de passe incorrect.']);
    }
  }

  public function showRegister()
  {
    $this->render('register');
  }

  public function processRegister()
  {
    require_once __DIR__ . '/../models/User.php';

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $existing_user = User::findByEmail($email);

    if (empty($email) || empty($password)) {
      $this->render('register', ['error' => 'Veuillez remplir tous les champs.']);
      return;
    }

    elseif (!empty($existing_user)) {
      $this->render('register', ['error' => 'L\'email est déjà associé à un compte.']);
      return;
    }

    try {
      $userId = User::create($email, $password);
      
      $newUser = [
          "id" => $userId,
          "email" => $email,
          "role" => "client"
      ];

      $_SESSION['user'] = $newUser;
      header("Location: /");
      exit;
    } catch (\PDOException $e) {
      $this->render('register', ['error' => 'Erreur lors de l\'inscription.']);
    }
  }

  public function logout()
  {
    session_destroy();
    unset($_SESSION);

    header('Location: /login');
    exit;
  }
}
