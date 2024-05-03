<?php 
    include('includes/header.php'); 
    $user_id = $settings['owner_id'];
    
    if(isset($_SESSION['is_loggedin'])) {
        $user_id = $_SESSION['id'];
    }

    $sql = "SELECT * FROM `users` WHERE `id` = ?";
    $stm = $pdo->prepare($sql);
    
    if($stm->execute([$user_id])) {
        $user = $stm->fetch();
    } else {
        header('Location: index.php');
    }
?>

<section class="about">
    <div class="container">
        <div class="row">
            <div class="col-8 mx-auto">
                <img src="avatars/<?= $user['profile_image'] ?>" style="height: 180px" class="mx-auto rounded-circle d-block" alt="">
                <h2 class="mt-2"><?= $user['fullname'] ?></h2>
                <p class="my-4"><?= $user['bio'] ?></p>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>