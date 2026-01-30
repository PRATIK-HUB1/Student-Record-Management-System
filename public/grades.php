<?php
require_once "../includes/auth.php";     // protect page
require_once "../config/db.php";         // database
require_once "../includes/header.php";   // layout + navigation

/* -----------------------
   HANDLE CREATE
----------------------- */
if (isset($_POST['add_grade'])) {
    $student_id = $_POST['student_id'];
    $module_id  = $_POST['module_id'];
    $grade      = $_POST['grade'];

    if ($student_id && $module_id && $grade) {
        $stmt = $pdo->prepare(
            "INSERT INTO grades (student_id, module_id, grade)
             VALUES (:student_id, :module_id, :grade)"
        );
        $stmt->execute([
            "student_id" => $student_id,
            "module_id"  => $module_id,
            "grade"      => $grade
        ]);
    }
    header("Location: grades.php");
    exit();
}

/* -----------------------
   HANDLE DELETE
----------------------- */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM grades WHERE grade_id = :id");
    $stmt->execute(["id" => $id]);

    header("Location: grades.php");
    exit();
}

/* -----------------------
   HANDLE UPDATE
----------------------- */
if (isset($_POST['update_grade'])) {
    $id         = $_POST['grade_id'];
    $student_id = $_POST['student_id'];
    $module_id  = $_POST['module_id'];
    $grade      = $_POST['grade'];

    $stmt = $pdo->prepare(
        "UPDATE grades
         SET student_id = :student_id,
             module_id  = :module_id,
             grade      = :grade
         WHERE grade_id = :id"
    );
    $stmt->execute([
        "student_id" => $student_id,
        "module_id"  => $module_id,
        "grade"      => $grade,
        "id"         => $id
    ]);

    header("Location: grades.php");
    exit();
}

/* -----------------------
   FETCH DATA
----------------------- */
$grades = $pdo->query(
    "SELECT g.grade_id, g.grade,
            s.name AS student_name,
            m.module_name
     FROM grades g
     JOIN students s ON g.student_id = s.student_id
     JOIN modules  m ON g.module_id  = m.module_id"
)->fetchAll(PDO::FETCH_ASSOC);

$students = $pdo->query(
    "SELECT student_id, name FROM students"
)->fetchAll(PDO::FETCH_ASSOC);

$modules = $pdo->query(
    "SELECT module_id, module_name FROM modules"
)->fetchAll(PDO::FETCH_ASSOC);

/* -----------------------
   EDIT MODE
----------------------- */
$editGrade = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM grades WHERE grade_id = :id");
    $stmt->execute(["id" => $_GET['edit']]);
    $editGrade = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<h2>Manage Grades</h2>
<a href="dashboard.php">← Back to Dashboard</a>

<hr>

<h3><?php echo $editGrade ? "Edit Grade" : "Add Grade"; ?></h3>

<form method="post">
    <?php if ($editGrade): ?>
        <input type="hidden" name="grade_id"
               value="<?php echo $editGrade['grade_id']; ?>">
    <?php endif; ?>

    <label>Student</label><br>
    <select name="student_id" required>
        <option value="">-- Select Student --</option>
        <?php foreach ($students as $student): ?>
            <option value="<?php echo $student['student_id']; ?>"
                <?php
                if ($editGrade &&
                    $editGrade['student_id'] == $student['student_id']) {
                    echo "selected";
                }
                ?>>
                <?php echo htmlspecialchars($student['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Module</label><br>
    <select name="module_id" required>
        <option value="">-- Select Module --</option>
        <?php foreach ($modules as $module): ?>
            <option value="<?php echo $module['module_id']; ?>"
                <?php
                if ($editGrade &&
                    $editGrade['module_id'] == $module['module_id']) {
                    echo "selected";
                }
                ?>>
                <?php echo htmlspecialchars($module['module_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Grade</label><br>
    <select name="grade" required>
        <?php foreach (['A','B','C','D','E','F'] as $g): ?>
            <option value="<?php echo $g; ?>"
                <?php
                if ($editGrade && $editGrade['grade'] === $g) {
                    echo "selected";
                }
                ?>>
                <?php echo $g; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <button type="submit"
        name="<?php echo $editGrade ? 'update_grade' : 'add_grade'; ?>">
        <?php echo $editGrade ? 'Update Grade' : 'Add Grade'; ?>
    </button>
</form>

<hr>

<h3>Grade List</h3>

<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Student</th>
        <th>Module</th>
        <th>Grade</th>
        <th>Actions</th>
    </tr>

    <?php if (count($grades) === 0): ?>
        <tr>
            <td colspan="5">No grades found</td>
        </tr>
    <?php endif; ?>

    <?php foreach ($grades as $row): ?>
        <tr>
            <td><?php echo $row['grade_id']; ?></td>
            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
            <td><?php echo htmlspecialchars($row['module_name']); ?></td>
            <td><?php echo $row['grade']; ?></td>
            <td>
                <a href="grades.php?edit=<?php echo $row['grade_id']; ?>">Edit</a> |
                <a href="grades.php?delete=<?php echo $row['grade_id']; ?>"
                   onclick="return confirm('Delete this grade?');">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
require_once "../includes/footer.php";   // layout footer
?>