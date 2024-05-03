<?php 
    session_start();
    include('settings.php');
    include('database.php');

    // logout
    if(isset($_GET['action']) && ($_GET['action'] === 'logout')) {
        unset($_SESSION['id']);
        unset($_SESSION['is_loggedin']);
        unset($_SESSION['fullname']);
        unset($_SESSION['email']);
        header('Location: index.php');
    }

    if(isset($_SESSION['id'])) {
        // user
        $stmu = $pdo->query("SELECT * FROM `users` WHERE `id` = ".$_SESSION['id']);
        $user = $stmu->fetch(PDO::FETCH_ASSOC);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $settings['title'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="./assets/css/style.css" type="text/css" rel="stylesheet" />
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container">
            <a class="navbar-brand" href="index.php"><?= $settings['title'] ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto me-4 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="posts.php">Posts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= isset($_SESSION['is_loggedin']) ? $_SESSION['fullname'] : 'Guest' ?>
                        </a>
                        <ul class="dropdown-menu">
                        <?php if(isset($_SESSION['is_loggedin'])): ?>
                            <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                            <?php if($user['role'] == 'admin'): ?>
                            <li><a class="dropdown-item" href="profile.php">Update profile</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="?action=logout">Logout</a></li>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="login.php">Login</a></li>
                            <li><a class="dropdown-item" href="register.php">Register</a></li>
                        <?php endif; ?>
                        </ul>
                    </li>
                </ul>
                <input class="form-control w-25" name="search" type="search" placeholder="Search" aria-label="Search" />
            </div>
        </div>
    </nav>