<?php

class ReviewsController extends Controller {
    public function index($error = NULL) {
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
                'error' => $error,
                'reviews' => $reviews,
                'logged_in' => $logged_in,
                'user_can_review' => $user_can_review,
                'user_has_valid_info' => $user_has_valid_info,
                'is_admin' => $is_admin,
                'getName' => 'getName'
            ]
        );
    }

    public function addReview() {
        require_once __DIR__ . '/../models/Order.php';
        require_once __DIR__ . '/../models/Review.php';
        require_once __DIR__ . '/../models/User.php';

        $error = NULL;
        if (isset($_SESSION['user'])) {
            $user_id = $_SESSION['user']['id'];
            $is_admin = User::isAdmin($user_id);

            $lastOrder = Order::getLastOrder($user_id);
            if ($is_admin || $lastOrder != null && $lastOrder['review_id'] == null) {
                $comment = $_POST['comment'];
                $product = $_POST['product'];
                $delivery = $_POST['delivery'];

                if (!empty($_POST['review_id'])) {
                    $review_id = $_POST['review_id'];
                    Review::updateReview($review_id, $user_id, $is_admin, $product, $delivery, $comment);
                } else {
                    $order_id = $lastOrder['order_id'];
                    Review::addReview($order_id, $product, $delivery, $comment);
                }
            }
        }
        header('Location: /reviews');
    }
}
