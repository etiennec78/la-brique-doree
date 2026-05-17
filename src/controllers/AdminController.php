<?php

class AdminController extends Controller {
    public function index() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
            session_destroy();
            unset($_SESSION);
            header('Location: /login');
            exit();
        }

        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Order.php';
        include_once __DIR__ . '/../format_data.php';

        $users_data = User::getAllUsersInfo();
        $running_deliveries = Order::getOrdersFromState(['preparing', 'shipping']);

        $this->render('admin', [
            'users_data' => $users_data, 
            'running_deliveries' => $running_deliveries, 
            'get_name' => 'getName'
        ]);
    }

    public function applyGlobalReduction() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
            header('Location: /login');
            exit();
        }

        require_once __DIR__ . '/../models/User.php';

        if (isset($_POST['user_id']) and isset($_POST['reduction'])) {
            User::setUserData($_POST['user_id'], 'global_reduction', $_POST['reduction']/100);
        }

        header('Location: /admin');
        exit();
    }

    public function apiBanUser() {
        if (($_SESSION['user']['role_id'] ?? 0) != 3) {
            exit(); 
        }

        require_once __DIR__ . '/../models/User.php';

        $id = $_POST['user_id'];
        $etat = $_POST['banned'];

        if ($id != $_SESSION['user']['id']) {
            User::setUserData($id, 'banned', $etat);
        }

        echo json_encode(['success' => true, 'banned' => $etat]);
        exit();
    }
}
