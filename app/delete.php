<?php
require 'db.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM salle WHERE NumSalle = ?");
$stmt->execute([$id]);

header('Location: index.php');
exit;
?>
