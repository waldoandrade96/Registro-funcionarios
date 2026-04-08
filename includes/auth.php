<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}

function getCurrentUser() {
    return $_SESSION['usuario_nome'] ?? '';
}

function logout() {
    session_destroy();
    header('Location: index.php');
    exit;
}
