<?php
$host = "localhost";
$dbname = "student_record_system";
$username = "root";      

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=UTF8",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
