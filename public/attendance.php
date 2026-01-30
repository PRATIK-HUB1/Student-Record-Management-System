<?php
require_once "../includes/auth.php";     // protect page
require_once "../config/db.php";         // database
require_once "../includes/header.php";   // layout + navigation

/* -----------------------
   HANDLE CREATE
----------------------- */
if (isset($_POST['add_attendance'])) {
    $student_id = $_POST['student_id'];
    $module_id  = $_POST['module_id'];
    $status     = $_POST['status'];
    $date       = $_POST['attendance_date'];

    if ($student_id && $module_id && $status && $date) {
        $stmt = $pdo->prepare(
            "INSERT INTO attendance (student_id, module_id, status, attendance_date)
             VALUES (:student_id, :module_id, :status, :attendance_date)"
        );
        $stmt->execute([
            "student_id"      => $student_id,
            "module_id"       => $module_id,
            "status"          => $status,
            "attendance_date" => $date
        ]);
    }
    header("Location: attendance.php");
    exit();
}

/* -----------------------
   HANDLE DELETE
----------------------- */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $stmt = $pdo->prepare(
        "DELETE FROM attendance WHERE attendance_id = :id"
    );
    $stmt->execute(["id" => $id]);

    header("Location: attendance.php");
    exit();
}

/* -----------------------
   HANDLE UPDATE
----------------------- */
if (isset($_POST['update_attendance'])) {
    $id         = $_POST['attendance_id'];
    $student_id = $_POST['student_id'];
    $module_id  = $_POST['module_id'];
    $status     = $_POST['status'];
    $date       = $_POST['attendance_date'];

    $stmt = $pdo->prepare(
        "UPDATE attendance
         SET student_id = :student_id,
             module_id  = :module_id,
             status     = :status,
             attendance_date = :attendance_date
         WHERE attendance_id = :id"
    );
    $stmt->execute([
        "student_id"      => $student_id,
        "module_id"       => $module_id,
        "status"          => $status,
        "attendance_date" => $date,
        "id"              => $id
    ]);

    header("Location: attendance.php");
    exit();
}

/* -----------------------
   FETCH DATA
----------------------- */
$attendance = $pdo->query(
    "SELECT a.attendance_id, a.status, a.attendance_date,
            s.name AS student_name,
            m.module_name
     FROM attendance a
     JOIN students s ON a.student_id = s.student_id
     JOIN modules  m ON a.module_id  = m.module_id
     ORDER BY a.attendance_date DESC"
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
$editAttendance = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare(
        "SELECT * FROM attendance WHERE attendance_id = :id"
    );
    $stmt->execute(["id" => $_GET['edit']]);
    $editAttendance = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<h2>Manage Attendance</h2>
<a href="dashboard.php">← Back to Dashboard</a>

<hr>

<h3><?php echo $editAttendance ? "Edit Attendance" : "Add Attendance"; ?></h3>

<form method="post">
    <?php if ($editAttendance): ?>
        <input type="hidden" name="attendance_id"
               value="<?php echo $editAttendance['attendance_id']; ?>">
    <?php endif; ?>

    <label>Student</label><br>
    <select name="student_id" required>
        <option value="">-- Select Student --</option>
        <?php foreach ($students as $student): ?>
            <option value="<?php echo $student['student_id']; ?>"
                <?php
                if ($editAttendance &&
                    $editAttendance['student_id'] == $student['student_id']) {
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
                if ($editAttendance &&
                    $editAttendance['module_id'] == $module['module_id']) {
                    echo "selected";
                }
                ?>>
                <?php echo htmlspecialchars($module['module_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Status</label><br>
    <select name="status" required>
        <?php foreach (['Present', 'Absent'] as $st): ?>
            <option value="<?php echo $st; ?>"
                <?php
                if ($editAttendance &&
                    $editAttendance['status'] === $st) {
                    echo "selected";
                }
                ?>>
                <?php echo $st; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Date</label><br>
    <input type="date" name="attendance_date" required
        value="<?php echo htmlspecialchars($editAttendance['attendance_date'] ?? ''); ?>">
    <br><br>

    <button type="submit"
        name="<?php echo $editAttendance ? 'update_attendance' : 'add_attendance'; ?>">
        <?php echo $editAttendance ? 'Update Attendance' : 'Add Attendance'; ?>
    </button>
</form>

<hr>

<h3>Attendance Records</h3>

<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Student</th>
        <th>Module</th>
        <th>Status</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>

    <?php if (count($attendance) === 0): ?>
        <tr>
            <td colspan="6">No attendance records found</td>
        </tr>
    <?php endif; ?>

    <?php foreach ($attendance as $row): ?>
        <tr>
            <td><?php echo $row['attendance_id']; ?></td>
            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
            <td><?php echo htmlspecialchars($row['module_name']); ?></td>
            <td><?php echo $row['status']; ?></td>
            <td><?php echo $row['attendance_date']; ?></td>
            <td>
                <a href="attendance.php?edit=<?php echo $row['attendance_id']; ?>">
                    Edit
                </a> |
                <a href="attendance.php?delete=<?php echo $row['attendance_id']; ?>"
                   onclick="return confirm('Delete this record?');">
                    Delete
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
require_once "../includes/footer.php";   // layout footer
?>