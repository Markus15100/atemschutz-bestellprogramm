<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header('Location: /atemschutz/login.php');
    exit;
}
