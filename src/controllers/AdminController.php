<?php

class AdminController extends Controller {
    public function index() {
        /*
            
         INPUT :
                 
            None
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Requires role level 3, fetches all user detailed information alongside active delivery statuses, and renders the administration dashboard view.

        */
        $this->requireRole(3);

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
        /*
            
         INPUT :
                 
            None
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Requires role level 3, captures specified global reduction and user ID flags via global POST data, performs the modification, and forwards the browser to the administration route.

        */
        $this->requireRole(3);

        require_once __DIR__ . '/../models/User.php';

        if (isset($_POST['user_id']) and isset($_POST['reduction'])) {
            $reduction = $_POST['reduction'];
            
            if ($reduction > 100) {
                $reduction = 100;
            }

            elseif ($reduction < 0) {
                $reduction = 0;
            }

            User::setUserData($_POST['user_id'], 'global_reduction', $reduction/100);
        }

        header('Location: /admin');
        exit();
    }

    public function apiBanUser() {
        /*
            
         INPUT :
                 
            None
          
         OUTPUT :

            None

          
         SUMMARY :
            
            Requires role level 3 in API mode, enforces an account ban state targeting a specific user from POST parameters if they do not match the current user ID, and outputs a JSON confirmation payload.

        */
        $this->requireRole(3, true);

        require_once __DIR__ . '/../models/User.php';

        $id = $_POST['user_id'];
        $state = $_POST['banned'];

        if ($id != $_SESSION['user']['id']) {
            User::setUserData($id, 'banned', $state);
        }

        echo json_encode(['success' => true, 'banned' => $state]);
        exit();
    }
}
