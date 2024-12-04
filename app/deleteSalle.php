<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$id = $_GET['id'];

// Suppression uniquement si l'utilisateur est propriétaire
$stmt = $pdo->prepare("DELETE FROM salle WHERE NumSalle = ? AND IdUser = ?");
$stmt->execute([$id, $user_id]);

header('Location: salle.php');
exit();
?>
