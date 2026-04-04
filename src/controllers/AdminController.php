<?php

class AdminController extends Controller {
    public function index() {
        require_once __DIR__ . '/../models/User.php';

        $users_data = User::getAllUsersInfo();

        $this->render('admin', ['users_data' => $users_data]);
    }
}
