<?php 
    include('includes/header.php'); 


    $login_form_errors = [];

    if(isset($_POST['login_btn'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // validation
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $register_form_errors[] = "Email is not valid!";
        }

        if(count($login_form_errors) === 0) {
            $sql = "SELECT * FROM `users` WHERE `email` = ?";
            $stm = $pdo->prepare($sql);
            
            if($stm->execute([$email])) {
                $user = $stm->fetch();

                if(password_verify($password, $user['password'])) {
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['is_loggedin'] = true;
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['email'] = $user['email'];
                    header('Location: dashboard.php');
                } else {
                    $login_form_errors[] = 'Password is incorrect!';
                }
            }
        }
    }
?>

<section class="login">
    <div class="container">
        <div class="row">
            <div class="col-6 mx-auto">
                <h2 class="mb-4">Login</h2>
                <?php if(count($login_form_errors) > 0): ?>
                    <ul>
                    <?php foreach($login_form_errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php if(isset($_GET['action']) && ($_GET['action'] === 'register')): ?>
                    <?php if(isset($_GET['status']) && ($_GET['status'] == 1)): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            User was registered successfully.
                            <br />
                            Please login.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" />
                    </div>
                    <div class="form-group mb-3">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" />
                    </div>
                    <button name="login_btn" type="submit" class="btn btn-sm btn-outline-primary">Login</button>
                    <a href="register.php" class="btn btn-sm btn-outline-link ms-2">Register</a>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>