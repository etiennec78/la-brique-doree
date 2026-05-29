<?php

class AuthController extends Controller
{
  public function showLogin()
  {
    if (isset($_SESSION['user'])) {
      header("Location: /");
      exit();
    }
    $this->render('login');
  }

  public function processLogin()
  {
    require_once __DIR__ . '/../models/User.php';

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
      $_SESSION['error'] = 'Veuillez remplir tous les champs.';
      $this->render('login');
      return;
    }

    $user_found = User::findByEmail($email);

    if ($user_found && password_verify($password, $user_found['password_hash'])) {
      if (!empty($user_found['banned'])) {
        $_SESSION['error'] = 'Votre compte a été banni.';
        $this->render('login');
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
      exit();
    } else {
      $_SESSION['failed_email'] = $_POST['email'] ?? '';
      $_SESSION['error'] = 'Email ou mot de passe incorrect.';
      $this->render('login');
    }
  }

  public function showRegister()
  {
    if (isset($_SESSION['user'])) {
      header("Location: /");
      exit();
    }
    $this->render('register');
  }

  public function processRegister()
  {
    require_once __DIR__ . '/../models/User.php';

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $existing_user = User::findByEmail($email);

    if (empty($email) || empty($password)) {
      $_SESSION['error'] = 'Veuillez remplir tous les champs.';
      $this->render('register');
      return;
    }

    if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/', $password)) {
      $_SESSION['error'] = 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.';
      $this->render('register');
      return;
    }

    elseif (!empty($existing_user)) {
      $_SESSION['error'] = 'L\'email est déjà associé à un compte.';
      $this->render('register');
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
      exit();
    } catch (\PDOException $error) {
      $pdo->rollBack();
      error_log("Registering error: " . $error->getMessage());
      $_SESSION['error'] = 'Erreur lors de l\'inscription : ' . $error->getMessage();
      $this->render('register');
    }
  }

  public function logout()
  {
    session_destroy();
    unset($_SESSION);

    header('Location: /login');
    exit();
  }
}
