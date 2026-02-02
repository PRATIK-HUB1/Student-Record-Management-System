<?php

$host = "localhost";

if ($_SERVER['HTTP_HOST'] === 'localhost') {
    // LOCALHOST (XAMPP)
    $dbname = "student_record_system";
    $username = "root";
    $password = "";
} else {
    // STUDENT SERVER
    $dbname = "NP03CS4S250037";
    $username = "NP03CS4S250037";
    $password = "xooQo5BEnN";
}

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed");
}
