<?php
require_once "../../config/db.php";

$search = trim($_GET['q'] ?? '');

if ($search === '') {
    exit;
}

$stmt = $pdo->prepare(
    "SELECT students.student_id,
            students.roll_number,
            students.name,
            students.email,
            courses.course_name
     FROM students
     LEFT JOIN courses ON students.course_id = courses.course_id
     WHERE students.name LIKE :q
        OR students.roll_number LIKE :q
        OR students.email LIKE :q"
);

$stmt->execute([
    'q' => "%$search%"
]);

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$students) {
    echo '<tr><td colspan="6">No students found</td></tr>';
    exit;
}

foreach ($students as $student) {
    echo "<tr>";
    echo "<td>{$student['student_id']}</td>";
    echo "<td>" . htmlspecialchars($student['roll_number']) . "</td>";
    echo "<td>" . htmlspecialchars($student['name']) . "</td>";
    echo "<td>" . htmlspecialchars($student['email']) . "</td>";
    echo "<td>" . htmlspecialchars($student['course_name'] ?? '') . "</td>";
    echo "<td>
            <a href='students.php?edit={$student['student_id']}'>Edit</a> |
            <a href='students.php?delete={$student['student_id']}'
               onclick=\"return confirm('Delete this student?');\">Delete</a>
          </td>";
    echo "</tr>";
}
