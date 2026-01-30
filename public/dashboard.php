<?php
require_once "../includes/auth.php";     // protect page
require_once "../config/db.php";         // database
require_once "../includes/header.php";   // page header + nav
?>
<h2>Welcome, <?php echo htmlspecialchars($_SESSION["admin_username"]); ?></h2>

<div class="dashboard-grid">

    <a href="students.php" class="dashboard-card">
        <h3>Students</h3>
        <p>Manage student records</p>
    </a>

    <a href="courses.php" class="dashboard-card">
        <h3>Courses</h3>
        <p>Manage courses</p>
    </a>

    <a href="modules.php" class="dashboard-card">
        <h3>Modules</h3>
        <p>Assign modules to courses</p>
    </a>

    <a href="grades.php" class="dashboard-card">
        <h3>Grades</h3>
        <p>Assign grades (A–F)</p>
    </a>

    <a href="attendance.php" class="dashboard-card">
        <h3>Attendance</h3>
        <p>Track student attendance</p>
    </a>

</div>

<?php
require_once "../includes/footer.php";   // page footer
?>