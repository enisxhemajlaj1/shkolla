<?php 
    
    include('includes/header.php'); 
    
    if(!isset($_GET['id']) && !isset($_SESSION['post_id'])) {
        die('<h1 class="mt-4 text-center">Error 404</h1>');   
    } else {
        $_SESSION['post_id'] = $_GET['id'];
    }

    if(isset($_SESSION['post_id'])) {
        $id = $_SESSION['post_id'];
    }

        

    $stm = $pdo->prepare("SELECT * FROM `posts` WHERE `posts`.`id` = ?");
    $stm->execute([$id]);
    $post = $stm->fetch();

    // author
    $author_stm = $pdo->prepare("SELECT * FROM `users` WHERE `users`.`id` = ?");
    $author_stm->execute([$post['user_id']]);
    $author = $author_stm->fetch();

    // media
    $media = [];
    $media_stm = $pdo->prepare("SELECT * FROM `media` WHERE `media`.`post_id` = ?");
    $media_stm->execute([$post['id']]);
    while($mrow = $media_stm->fetch()) {
        $media[] = $mrow;
    }

    // comments
    $comments = [];
    $comments_stm = $pdo->prepare("SELECT * FROM `comments` WHERE `comments`.`post_id` = ?");
    $comments_stm->execute([$post['id']]);
    while($comment = $comments_stm->fetch()) {
        $comments[] = $comment;
    }
   

    if(isset($_POST['comment_btn'])) {
        $content = $_POST['comment'];
        $user_id = $_POST['user_id'];
        $post_id = $_POST['post_id'];

        $sql = "INSERT INTO `comments` (`user_id`, `post_id`, `content`) VALUES (?, ?, ?)";
        $stm = $pdo->prepare($sql);

        if($stm->execute([$user_id, $post_id, $content])) {
            header('Location: post.php?id='.$post_id);
        }
    }

    // related post
    $post_categories = [];
    $cat_stm = $pdo->prepare("SELECT * FROM `categories` INNER JOIN `category_post` ON `category_post`.`category_id` = `categories`.`id` WHERE `category_post`.`post_id` = ?");
    $cat_stm->execute([$post['id']]);
    while($cat = $cat_stm->fetch()) {
        $post_categories[] = $cat['name'];
    }
    $cat_names = '';
    foreach($post_categories as $ipc => $pc) {
        if($ipc === count($post_categories) - 1) {
            $cat_names .= "'$pc'";
        } else {
            $cat_names .= "'$pc',";
        }
    }

    

    $related_posts = [];
    $rp_stm = $pdo->query("SELECT * FROM `posts` INNER JOIN `category_post` ON `category_post`.`post_id` = `posts`.`id` INNER JOIN `categories` ON `categories`.`id` = `category_post`.`category_id` WHERE `categories`.`name` IN ($cat_names) AND `posts`.`id` != ".$post['id']);
    while($rp = $rp_stm->fetch()) {
        $related_posts[] = $rp;
    }
    $related_posts = array_unique($related_posts, SORT_REGULAR);
?>

<section class="post">
    <div class="container">
        <div class="row">
            <div class="col-8">
                <h2><?= $post['title'] ?></h2>
                <p>Author: <?= $author['fullname'] ?> | Published at: <?= $post['created_at'] ?></p>

                <?php if(count($media) > 1): ?>
                <div id="postSlider" class="carousel slide">
                    <div class="carousel-indicators">
                        <?php foreach($media as $isp => $sp): ?>
                            <button 
                            type="button" 
                            data-bs-target="#postSlider" 
                            data-bs-slide-to="<?= $isp ?>" 
                            <?php if($isp === 0): ?>
                            class="active" 
                            aria-current="true"
                            <?php endif; ?>  
                            aria-label="Slide 1">
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <div class="carousel-inner">
                        <?php foreach($media as $isp => $sp): ?>
                            <div class="carousel-item <?= ($isp === 0) ? 'active' : '' ?>">
                                <img src="post_images/<?= $sp['name'] ?>" class="d-block w-100" alt="" />
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#postSlider" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#postSlider" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
                <?php else: ?>
                    <img src="post_images/<?= $media[0]['name'] ?>" class="d-block w-100" alt="" />
                <?php endif; ?>

                <div class="mt-4">
                    <?= $post['content'] ?>
                </div>

                <div class="comments mt-5">
                    <h4 class="mb-4">Comments</h4>
                    <!-- <p>0 comments</p> -->
                    <?php if(count($comments)): ?>
                        <?php foreach($comments as $c): ?>
                            <?php 
                                // author
                                $cauthor_stm = $pdo->prepare("SELECT * FROM `users` WHERE `users`.`id` = ?");
                                $cauthor_stm->execute([$c['user_id']]);
                                $cauthor = $cauthor_stm->fetch();
                            ?>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <p><?= $c['content'] ?></p>
                                    <p>from <i><?= $cauthor['fullname'] ?>  </i> @ <?= $c['created_at'] ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['id'])): ?>
                    <form action="<?= $_SERVER['PHP_SELF'] ?>" class="mt-4" method="post">
                        <textarea name="comment" id="comment" class="form-control mb-2" placeholder="Comment..."></textarea>
                        <input type="hidden" name="user_id" value="<?= $_SESSION['id'] ?>" />
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>" />
                        <button name="comment_btn" type="submit" class="btn btn-sm btn-outline-primary">Submit</button>
                    </form>
                    <?php else: ?>
                        <p>Please <a href="login.php">login</a> first!</p>
                    <?php endif; ?>
                </div>

            </div>
            <div class="col-lg-3 offset-lg-1 offset-sm-0 col-sm-12">
                <h4>Related posts</h4>
                <?php if(count($related_posts) > 0): ?>
                <div class="row mt-4">
                    <?php foreach($related_posts as $rpost): ?>
                        <div class="col-12">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <?php
                                        $irpstm = $pdo->prepare("SELECT * FROM `media` WHERE `post_id` = ? LIMIT 1");
                                        $irpstm->execute([$rpost['id']]);
                                        $rpimage = $irpstm->fetch();
                                    ?>
                                    <img src="post_images/<?= $rpimage['name'] ?>" alt="<?= $rpost['title'] ?>" class="img-fluid mb-4">
                                    <h3><?= $rpost['title'] ?></h3>
                                    <a href="post.php?id=<?= $rpost['id'] ?>">Read more</a>
                                </div>
                            </div>
                        </div> <!-- ./row -->
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                    No related posts!
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>