<?php 
    include('includes/header.php'); 


    $register_form_errors = [];

    if(isset($_POST['register_btn'])) {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $repeat_password = $_POST['repeat_password'];

        // validation
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $register_form_errors[] = "Email is not valid!";
        }

        if($password !== $repeat_password) {
            $register_form_errors[] = "Password's does not match!";
        }

        if(count($register_form_errors) === 0) {
            $sql = "INSERT INTO `users` (`fullname`, `email`, `password`) VALUES (?, ?, ?)";
            $stm = $pdo->prepare($sql);

            if($stm->execute([$fullname, $email, password_hash($password, PASSWORD_BCRYPT)])) {
                header('Location: login.php?action=register&status=1');
            } else {
                $register_form_errors[] = 'Invalid credentials!';
            }
        }
    }
?>

<section class="register">
    <div class="container">
        <div class="row">
            <div class="col-6 mx-auto">
                <h2 class="mb-4">Register</h2>
                <?php if(count($register_form_errors) > 0): ?>
                    <ul>
                    <?php foreach($register_form_errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                    <div class="form-group mb-3">
                        <label for="fullname">Fullname</label>
                        <input type="text" name="fullname" id="fullname" class="form-control" />
                    </div>
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" />
                    </div>
                    <div class="form-group mb-3">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" />
                    </div>
                    <div class="form-group mb-3">
                        <label for="repeat-password">Repeat password</label>
                        <input type="password" name="repeat_password" id="repeat-password" class="form-control" />
                    </div>
                    <button name="register_btn" type="submit" class="btn btn-sm btn-outline-primary">Register</button>
                    <a href="login.php" class="btn btn-sm btn-outline-link ms-2">Login</a>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>