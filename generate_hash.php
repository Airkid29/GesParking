<?php
$password = 'admin123'; // Le mot de passe que vous voulez hacher
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "Le hash pour '$password' est : " . $hashed_password;
// Exemple de sortie : $2y$10$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789./
?>