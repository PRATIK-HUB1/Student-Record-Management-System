<?php
require_once "../config/db.php";

$term = $_GET['term'] ?? '';

$stmt = $pdo->prepare(
    "SELECT student_id, name, email
     FROM students
     WHERE name LIKE :term
     ORDER BY name
     LIMIT 10"
);

try {
    $stmt->execute([
        "term" => "%" . $term . "%"
    ]);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

echo json_encode($results);