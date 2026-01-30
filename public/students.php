<?php
require_once "../includes/auth.php";     // protect page
require_once "../config/db.php";         // database
require_once "../includes/header.php";   // layout + navigation

/* -----------------------
   HANDLE CREATE
----------------------- */
if (isset($_POST['add_student'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course_id = $_POST['course_id'];

    if ($name && $email) {
        $stmt = $pdo->prepare(
    "INSERT INTO students (name, email, course_id)
     VALUES (:name, :email, :course_id)"
);
$stmt->execute([
    "name" => $name,
    "email" => $email,
    "course_id" => $course_id ?: null
]);

$lastId = $pdo->lastInsertId();

$rollNumber = "ROLL-" . str_pad($lastId, 3, "0", STR_PAD_LEFT);

$update = $pdo->prepare(
    "UPDATE students SET roll_number = :roll WHERE student_id = :id"
);
$update->execute([
    "roll" => $rollNumber,
    "id"   => $lastId
]);
    }
    header("Location: students.php");
    exit();
}

/* -----------------------
   HANDLE DELETE
----------------------- */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM students WHERE student_id = :id");
    $stmt->execute(["id" => $id]);

    header("Location: students.php");
    exit();
}

/* -----------------------
   HANDLE UPDATE
----------------------- */
if (isset($_POST['update_student'])) {
    $id = $_POST['student_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course_id = $_POST['course_id'];

    $stmt = $pdo->prepare(
        "UPDATE students
         SET name = :name, email = :email, course_id = :course_id
         WHERE student_id = :id"
    );
    $stmt->execute([
        "name" => $name,
        "email" => $email,
        "course_id" => $course_id ?: null,
        "id" => $id
    ]);

    header("Location: students.php");
    exit();
}

/* -----------------------
   FETCH DATA
----------------------- */
$students = $pdo->query(
    "SELECT students.student_id,
            students.roll_number,
            students.name,
            students.email,
            students.course_id,
            courses.course_name
     FROM students
     LEFT JOIN courses ON students.course_id = courses.course_id"
)->fetchAll(PDO::FETCH_ASSOC);

$courses = $pdo->query(
    "SELECT * FROM courses"
)->fetchAll(PDO::FETCH_ASSOC);

/* -----------------------
   EDIT MODE
----------------------- */
$editStudent = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = :id");
    $stmt->execute(["id" => $_GET['edit']]);
    $editStudent = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<h2>Manage Students</h2>
<a href="dashboard.php">← Back to Dashboard</a>

<hr>

<h3><?php echo $editStudent ? "Edit Student" : "Add Student"; ?></h3>

<form method="post">
    <?php if ($editStudent): ?>
        <input type="hidden" name="student_id"
               value="<?php echo $editStudent['student_id']; ?>">
    <?php endif; ?>

    <label>Name</label><br>
    <input type="text" name="name" required
        value="<?php echo htmlspecialchars($editStudent['name'] ?? ''); ?>">
    <br><br>

    <label>Email</label><br>
    <input type="email" name="email" required
        value="<?php echo htmlspecialchars($editStudent['email'] ?? ''); ?>">
    <br><br>

    <label>Course</label><br>
    <select name="course_id">
        <option value="">-- Select Course --</option>
        <?php foreach ($courses as $course): ?>
            <option value="<?php echo $course['course_id']; ?>"
                <?php
                if ($editStudent &&
                    $editStudent['course_id'] == $course['course_id']) {
                    echo "selected";
                }
                ?>>
                <?php echo htmlspecialchars($course['course_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <button type="submit"
        name="<?php echo $editStudent ? 'update_student' : 'add_student'; ?>">
        <?php echo $editStudent ? 'Update Student' : 'Add Student'; ?>
    </button>
</form>

<hr>

<h3>Search Students (Ajax)</h3>

<input type="text" id="studentSearch"
       placeholder="Type student name..."
       autocomplete="off">

<ul id="searchResults"></ul>

<hr>

<h3>Student List</h3>

<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Roll Number</th>
        <th>Name</th>
        <th>Email</th>
        <th>Course</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($students as $student): ?>
        <tr>
            <td><?php echo $student['student_id']; ?></td>
<td><?php echo htmlspecialchars($student['roll_number']); ?></td>
<td><?php echo htmlspecialchars($student['name']); ?></td>
<td><?php echo htmlspecialchars($student['email']); ?></td>
<td><?php echo htmlspecialchars($student['course_name'] ?? ''); ?></td>
<td>
    <a href="students.php?edit=<?php echo $student['student_id']; ?>">Edit</a> |
    <a href="students.php?delete=<?php echo $student['student_id']; ?>"
       onclick="return confirm('Delete this student?');">Delete</a>
</td>
        </tr>
    <?php endforeach; ?>
</table>

<script src="../assets/js/student_search.js"></script>

<?php
require_once "../includes/footer.php";   // layout footer
?>