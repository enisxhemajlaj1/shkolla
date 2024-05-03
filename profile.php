<?php 
    include('includes/header.php'); 

    if(!isset($_SESSION['is_loggedin'])) {
      header('Location: login.php');
    }


    if($user['role'] !== 'admin') {
        header('Location: dashboard.php');
    }
    

    $profile_form_errors = [];

    if(isset($_POST['update_btn'])) {
        $bio = $_POST['bio'];
        $photo = $_FILES['photo'];
        $allowed_filetypes = ['png', 'jpeg', 'jpg'];
        $filename = time().'_'.$photo['name'];

        // validation
        $fp = explode(".", $photo['name']);
        $ext = end($fp);
        if(!in_array($ext, $allowed_filetypes)) {
            $profile_form_errors[] = "Filetype is not supported!";
        }

        if(count($profile_form_errors) === 0) {
            $sql = "UPDATE `users` SET `bio` = ?, `profile_image` = ? WHERE `id` = ?";
            $stm = $pdo->prepare($sql);
            
            if($stm->execute([$bio, $filename, $_SESSION['id']])) {
                move_uploaded_file($photo['tmp_name'], 'avatars/'.$filename);
                header('Location: profile.php?status=1');
            } else {
                header('Location: profile.php?status=0');
            }
        }
    }
?>

<section class="login">
    <div class="container">
        <div class="row">
            <div class="col-6 mx-auto">
                <h2 class="mb-4">Profile</h2>
                <?php if(count($profile_form_errors) > 0): ?>
                    <ul>
                    <?php foreach($profile_form_errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php if(isset($_GET['status']) && ($_GET['status'] == 1)): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        User profile was updated successfully.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group mb-3">
                        <label for="bio">Bio</label>
                        <textarea name="bio" id="bio" class="form-control"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="photo">Photo</label>
                        <input type="file" name="photo" id="photo" class="form-control" />
                    </div>
                    <button name="update_btn" type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>