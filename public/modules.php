<?php
require_once "../includes/auth.php";     
require_once "../config/db.php";         
require_once "../includes/header.php";   

/* HANDLE CREATE */
if (isset($_POST['add_module'])) {
    $module_name = trim($_POST['module_name']);
    $course_id = $_POST['course_id'];

    if ($module_name && $course_id) {
        $stmt = $pdo->prepare(
            "INSERT INTO modules (module_name, course_id)
             VALUES (:module_name, :course_id)"
        );
        $stmt->execute([
            "module_name" => $module_name,
            "course_id" => $course_id
        ]);
    }
    header("Location: modules.php");
    exit();
}

/* HANDLE DELETE */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $stmt = $pdo->prepare(
        "DELETE FROM modules WHERE module_id = :id"
    );
    $stmt->execute(["id" => $id]);

    header("Location: modules.php");
    exit();
}

/* HANDLE UPDATE */
if (isset($_POST['update_module'])) {
    $id = $_POST['module_id'];
    $module_name = trim($_POST['module_name']);
    $course_id = $_POST['course_id'];

    $stmt = $pdo->prepare(
        "UPDATE modules
         SET module_name = :module_name, course_id = :course_id
         WHERE module_id = :id"
    );
    $stmt->execute([
        "module_name" => $module_name,
        "course_id" => $course_id,
        "id" => $id
    ]);

    header("Location: modules.php");
    exit();
}

/*  FETCH DATA */
$modules = $pdo->query(
    "SELECT modules.*, courses.course_name
     FROM modules
     JOIN courses ON modules.course_id = courses.course_id"
)->fetchAll(PDO::FETCH_ASSOC);

$courses = $pdo->query(
    "SELECT * FROM courses"
)->fetchAll(PDO::FETCH_ASSOC);

/* EDIT MODE */
$editModule = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare(
        "SELECT * FROM modules WHERE module_id = :id"
    );
    $stmt->execute(["id" => $_GET['edit']]);
    $editModule = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<h2>Manage Modules</h2>

<hr>

<h3><?php echo $editModule ? "Edit Module" : "Add Module"; ?></h3>

<form method="post">
    <?php if ($editModule): ?>
        <input type="hidden" name="module_id"
               value="<?php echo $editModule['module_id']; ?>">
    <?php endif; ?>

    <label>Module Name</label><br>
    <input type="text" name="module_name" required
        value="<?php echo htmlspecialchars($editModule['module_name'] ?? ''); ?>">
    <br><br>

    <label>Course</label><br>
    <select name="course_id" required>
        <option value="">-- Select Course --</option>
        <?php foreach ($courses as $course): ?>
            <option value="<?php echo $course['course_id']; ?>"
                <?php
                if ($editModule &&
                    $editModule['course_id'] == $course['course_id']) {
                    echo "selected";
                }
                ?>>
                <?php echo htmlspecialchars($course['course_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <button type="submit"
        name="<?php echo $editModule ? 'update_module' : 'add_module'; ?>">
        <?php echo $editModule ? 'Update Module' : 'Add Module'; ?>
    </button>
</form>

<hr>

<h3>Module List</h3>

<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Module Name</th>
        <th>Course</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($modules as $module): ?>
        <tr>
            <td><?php echo $module['module_id']; ?></td>
            <td><?php echo htmlspecialchars($module['module_name']); ?></td>
            <td><?php echo htmlspecialchars($module['course_name']); ?></td>
            <td>
                <a href="modules.php?edit=<?php echo $module['module_id']; ?>">
                    Edit
                </a> |
                <a href="modules.php?delete=<?php echo $module['module_id']; ?>"
                   onclick="return confirm('Delete this module?');">
                    Delete
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
require_once "../includes/footer.php";   // layout footer
?>