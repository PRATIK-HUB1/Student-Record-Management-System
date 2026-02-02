<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
require_once "../includes/header.php";
?>

<div class="dashboard-container">

    <div class="dashboard-header">
        <h2>Welcome to Student Record Management System</h2>

        <p class="dashboard-subtitle">
            Use the sections below to manage student records and academic data.
        </p>
    </div>

    <div class="dashboard-section">
        <h3 class="section-title">Quick Access</h3>

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
    </div>

</div>

<?php
require_once "../includes/footer.php";
?>
