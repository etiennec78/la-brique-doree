<?php

class ReviewsController extends Controller {
    public function index() {
        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/Order.php';
        require_once __DIR__ . '/../models/Review.php';
        require_once __DIR__ . '/../models/User.php';
        include_once __DIR__ . '/../format_data.php';

        $cart_count = Cart::getCartCount();
        $reviews = Review::getReviews();
        $logged_in = isset($_SESSION['user']['id']);
        $user_can_review = false;
        $user_has_valid_info = false;
        $is_admin = false;
        $order_was_takeaway = false; 

        if ($logged_in) {
            $user_id = $_SESSION['user']['id'];
            $is_admin = User::isAdmin($user_id);

            $lastOrder = Order::getLastOrder($user_id);
            $user_can_review = $lastOrder != null && $lastOrder['review_id'] == null;
            $user_has_valid_info = User::hasValidInfo($user_id);
        }

        $this->render(
            'reviews',
            [
                'cart_count' => $cart_count,
                'reviews' => $reviews,
                'logged_in' => $logged_in,
                'user_can_review' => $user_can_review,
                'user_has_valid_info' => $user_has_valid_info,
                'is_admin' => $is_admin,
                'order_was_takeaway' => $order_was_takeaway,
                'getName' => 'getName'
            ]
        );
    }

    public function addReview() {
        require_once __DIR__ . '/../models/Order.php';
        require_once __DIR__ . '/../models/Review.php';
        require_once __DIR__ . '/../models/User.php';

        if (isset($_SESSION['user'])) {
            $user_id = $_SESSION['user']['id'];
            $comment = $_POST['comment'];
            $product = $_POST['product'];
            $delivery = $_POST['delivery'] ? isset($_POST['delivery']) : null;

            $new_post = empty($_POST['review_id']);
            $is_admin = User::isAdmin($user_id);

            // Get order data
            if ($new_post) {
                $review_id = $_POST['review_id'];
                $order = Order::getOrderById($review_id);
            } else {
                $order = Order::getLastOrder($user_id);
            }

            // Check that the received data always contains a product rating, and contains a delivery rating only if the food was delivered
            if ($product != null && $order['is_takeaway'] == ($delivery == null)) {
                if ($new_post) {
                    // Only authorize if the user is admin or his last order does not have a review attached yet
                    if ($is_admin || $lastOrder != null && $lastOrder['review_id'] == null) {
                        $order_id = $lastOrder['order_id'];
                        Review::addReview($order_id, $product, $delivery, $comment);
                    }
                } else {
                    $reviewer = Review::getReviewer($review_id);
                    if ($is_admin || $reviewer == $user_id) {
                        Review::updateReview($review_id, $product, $delivery, $comment);
                    }
                }
            }
        }
        header('Location: /reviews');
        exit();
    }
}
