<?php
// Core Settings
date_default_timezone_set("Asia/Kolkata");

// Connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=infoqr", "root", "");
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}

// Values
$path_to_qr  = "public/QR/";
