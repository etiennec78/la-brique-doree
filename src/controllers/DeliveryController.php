<?php

class DeliveryController extends Controller {
    public function index() {
        include_once __DIR__ . '/../format_data.php';
        require_once __DIR__ . '/../models/Delivery.php';

        $uid = $_SESSION['user']['id'];
        $deliveries = Delivery::getDeliveries($uid);

        $this->render(
            'delivery',
            [
                'deliveries' => $deliveries,
                'getName' => 'getName',
                'getAddress' => 'getAddress'
            ]
        );
    }
}
