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
    $password_verification = User::passwordVerification($password);

    if (empty($email) || empty($password)) {
      $_SESSION['failed_email'] = $email;
      $_SESSION['error'] = 'Veuillez remplir tous les champs.';
      $this->render('register');
      return;
    }

    elseif (!empty($existing_user)) {
      $_SESSION['failed_email'] = $email;
      $_SESSION['error'] = 'L\'email est déjà associé à un compte.';
      $this->render('register');
      return;
    }

    else {
      $error_password = "Votre mot de passe à besoin";
      $error_length = ", de 8 caractères";
      $error_upper = ", d'un caractère majuscule";
      $error_lower = ", d'un caractère minuscule";
      $error_number = ", d'un chiffre";
      $error_special = ", d'un caractère spécial";
      $error_string_length = strlen(trim($error_password));

      foreach ($password_verification as $key => $value) {
        if (($value != 0) && ($value != 1)) {
          $_SESSION['failed_email'] = $email;
          $_SESSION['error'] = 'Erreur inconnue de mot de passe.';
          $this->render('register');
          return;
        }

        if ($value == 0) {

          switch ($key) {

            case 'length':
              $error_password .= $error_length;
              break;

            case 'uppercase':
              $error_password .= $error_upper;
              break;

            case 'lowercase':
              $error_password .= $error_lower;
              break;

            case 'number':
              $error_password .= $error_number;
              break;

            case 'special':
              $error_password .= $error_special;
              break;
            
            default:
              $_SESSION['failed_email'] = $email;
              $_SESSION['error'] = 'Erreur inconnue de mot de passe.';
              $this->render('register');
              return;
          }
        }
      }

      if (strlen(trim($error_password)) > $error_string_length) {
        $_SESSION['failed_email'] = $email;
        $_SESSION['error'] = $error_password . '.';
        $this->render('register');
        return;
      }
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
