<?php

class ProfileController extends Controller {
    public function index($target_id = NULL) {
        /*
            
         INPUT :
                 
            (mixed) $target_id : variable representing the unique identifier of the profile to display
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Authenticates current session, determines the profile view scope based on administrative context or defaults to the self identity, queries user dataset arrays, and renders the profile management interface.

        */
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }

        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/User.php';

        $uid = $_SESSION['user']['id'];

        if (isset($_GET['user_id']) && User::isAdmin($uid)) {
            $target_id = (int)$_GET['user_id'];
        }

        if ($target_id == NULL)
            $target_id = $uid;

        $cart_count = Cart::getCartCount();
        $user_data = User::getUserInfo($target_id);
        $is_admin = User::isAdmin($uid);

        $this->render(
            'profile',
            [
                'cart_count' => $cart_count,
                'user_data' => $user_data,
                'target' => $target_id,
                'is_admin' => $is_admin,
                'uid' => $uid
            ]
        );
    }

    public function updateProfile() {
        /*
            
         INPUT :
                 
            None
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Manages updates to user record values, cross-checks email uniqueness thresholds, determines geographical coordinate modifications using location API components when address elements transition, and responds using structured JSON streams.

        */
        if (!isset($_SESSION['user'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false]);
            exit();
        }

        global $pdo;
        require_once __DIR__ . '/../db_connect.php';
        require_once __DIR__ . '/../models/Location.php';
        require_once __DIR__ . '/../models/User.php';

        $uid = $_SESSION['user']['id'];

        $target = $uid;
        if (isset($_POST['user_id'])) {
            $target = $_POST['user_id'];
        }

        try {
            $old_user_data = User::getUserInfo($target);

            $street_nb = !empty($_POST['street_nb']) ? $_POST['street_nb'] : null;
            $street_nb_suf = !empty($_POST['street_nb_suf']) ? $_POST['street_nb_suf'] : null;
            $street = !empty($_POST['street']) ? $_POST['street'] : null;
            $zip_code = !empty($_POST['zip_code']) ? $_POST['zip_code'] : null;
            $intercom_code = !empty($_POST['intercom_code']) ? $_POST['intercom_code'] : null;
            $birth_date = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;
            $phone = !empty($_POST['phone']) ? $_POST['phone'] : null;

            $address_has_changed = (
                $old_user_data['street_nb'] != $street_nb or
                $old_user_data['street_nb_suf'] != $street_nb_suf or
                $old_user_data['street'] != $street or
                $old_user_data['zip_code'] != $zip_code
            );

            $email_has_changed = ($old_user_data['email'] != $_POST['email']);

            if ($email_has_changed && User::findByEmail($_POST['email'])) {
                $_SESSION['error'] = 'L\'adresse email est déjà utilisée.';
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $_SESSION['error']]);
                exit();
            } else {
                if ($address_has_changed && !empty($street) && !empty($zip_code)) {
                    $coordinates = Location::getLocationCoord($_POST, $uid);
                    if (isset($coordinates['error'])) {
                        $_SESSION['error'] = $coordinates['error'];
                    } else {
                        User::setUserData($target, 'latitude', $coordinates['lat']);
                        User::setUserData($target, 'longitude', $coordinates['lng']);
                    }
                } elseif ($address_has_changed && (empty($street) || empty($zip_code))) {
                    User::setUserData($target, 'latitude', null);
                    User::setUserData($target, 'longitude', null);
                }
                
                $birth_date = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;

                User::setAllUserData(
                    $_POST['first_name'], 
                    $_POST['last_name'], 
                    $street_nb, 
                    $street_nb_suf, 
                    $street, 
                    $zip_code, 
                    $_POST['phone'], 
                    $_POST['email'], 
                    $intercom_code, 
                    $birth_date, 
                    $target
                );

                if ($target == $uid) {
                    $_SESSION['user'] = array_merge($_SESSION['user'], $_POST);
                }

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'error' => $_SESSION['error'] ?? null
                ]);
                exit();
            }
        } catch (\Throwable $error) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Profile update error: " . $error->getMessage());
            $_SESSION['error'] = 'Erreur lors de la mise à jour des données du profil : ' . $error->getMessage();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $_SESSION['error']]);
            exit();
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => false]);
        exit();
    }
}
