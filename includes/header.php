<!DOCTYPE html>
<html>
<head>
    <title>Student Record Management System</title>
    <link rel="stylesheet" href="/Student_Record_Management_System/assets/css/style.css">

</head>

<body class="<?php echo isset($_SESSION['admin_id']) ? 'app-page' : 'login-page'; ?>">

<header>
    <h1>Student Record Management System</h1>

    <?php if (isset($_SESSION['admin_id'])): ?>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="students.php">Students</a>
            <a href="courses.php">Courses</a>
            <a href="modules.php">Modules</a>
            <a href="grades.php">Grades</a>
            <a href="attendance.php">Attendance</a>
            <a href="logout.php">Logout</a>
        </nav>
    <?php endif; ?>
</header>

<main>
