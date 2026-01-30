<?php
require_once "../includes/auth.php";     // protect page
require_once "../config/db.php";         // database
require_once "../includes/header.php";   // layout + navigation

/* -----------------------
   HANDLE CREATE
----------------------- */
if (isset($_POST['add_course'])) {
    $course_name = trim($_POST['course_name']);

    if ($course_name) {
        $stmt = $pdo->prepare(
            "INSERT INTO courses (course_name)
             VALUES (:course_name)"
        );
        $stmt->execute([
            "course_name" => $course_name
        ]);
    }
    header("Location: courses.php");
    exit();
}

/* -----------------------
   HANDLE DELETE
----------------------- */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $stmt = $pdo->prepare(
        "DELETE FROM courses WHERE course_id = :id"
    );
    $stmt->execute(["id" => $id]);

    header("Location: courses.php");
    exit();
}

/* -----------------------
   HANDLE UPDATE
----------------------- */
if (isset($_POST['update_course'])) {
    $id = $_POST['course_id'];
    $course_name = trim($_POST['course_name']);

    $stmt = $pdo->prepare(
        "UPDATE courses
         SET course_name = :course_name
         WHERE course_id = :id"
    );
    $stmt->execute([
        "course_name" => $course_name,
        "id" => $id
    ]);

    header("Location: courses.php");
    exit();
}

/* -----------------------
   FETCH DATA
----------------------- */
$courses = $pdo->query(
    "SELECT * FROM courses"
)->fetchAll(PDO::FETCH_ASSOC);

/* -----------------------
   EDIT MODE
----------------------- */
$editCourse = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare(
        "SELECT * FROM courses WHERE course_id = :id"
    );
    $stmt->execute(["id" => $_GET['edit']]);
    $editCourse = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<h2>Manage Courses</h2>
<a href="dashboard.php">← Back to Dashboard</a>

<hr>

<h3><?php echo $editCourse ? "Edit Course" : "Add Course"; ?></h3>

<form method="post">
    <?php if ($editCourse): ?>
        <input type="hidden" name="course_id"
               value="<?php echo $editCourse['course_id']; ?>">
    <?php endif; ?>

    <label>Course Name</label><br>
    <input type="text" name="course_name" required
           value="<?php echo htmlspecialchars($editCourse['course_name'] ?? ''); ?>">
    <br><br>

    <button type="submit"
        name="<?php echo $editCourse ? 'update_course' : 'add_course'; ?>">
        <?php echo $editCourse ? 'Update Course' : 'Add Course'; ?>
    </button>
</form>

<hr>

<h3>Course List</h3>

<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Course Name</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($courses as $course): ?>
        <tr>
            <td><?php echo $course['course_id']; ?></td>
            <td><?php echo htmlspecialchars($course['course_name']); ?></td>
            <td>
                <a href="courses.php?edit=<?php echo $course['course_id']; ?>">
                    Edit
                </a> |
                <a href="courses.php?delete=<?php echo $course['course_id']; ?>"
                   onclick="return confirm('Delete this course?');">
                    Delete
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
require_once "../includes/footer.php";   // layout footer
?>