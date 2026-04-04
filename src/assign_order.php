<?php
session_start();
require_once './db_connect.php'; 

//Sécurité, on vérifie si la personne connectée est restaurateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../views/login.php');
    exit();
}


if (isset($_POST['order_id']) && isset($_POST['delivery_person_id'])) {
    
    $order_id = $_POST['order_id'];
    $delivery_id = $_POST['delivery_person_id'];

    try {
        
        $sql = "UPDATE orders 
                SET delivery_person_id = delivery_id, 
                    order_status_id = 3 
                WHERE id = order_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'delivery_id' => $delivery_id,
            'order_id'    => $order_id
        ]);

        header('Location: ../views/restaurateur.php?success=assigned');
        exit();

    } catch (PDOException $e) {
        $erreur = "Erreur de base de données : " . $e->getMessage();
    }
} else {
    header('Location: ../views/restaurateur.php');
    exit();
}