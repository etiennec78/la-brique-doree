<?php

class AdminController extends Controller {
    public function index() {
        require_once __DIR__ . '/../models/User.php';
        include_once __DIR__ . '/../format_data.php';

        $users_data = User::getAllUsersInfo();

        $this->render('admin', ['users_data' => $users_data, 'get_name' => 'getName']);
    }
}
