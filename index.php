<?php 
    include('includes/header.php'); 

    // get latest (slider) posts
    $slider_posts = [];
    $slider_stm = $pdo->query("SELECT * FROM `posts` INNER JOIN `media` ON `posts`.`id` = `media`.`post_id` ORDER BY `posts`.`id` DESC LIMIT 4");
        
    while($slide_post = $slider_stm->fetch()) {
        $slider_posts[] = $slide_post;
    }

    
    // get popular posts
    $popular_posts = [];
    $popular_stm = $pdo->query("SELECT * FROM `posts` INNER JOIN `media` ON `posts`.`id` = `media`.`post_id` ORDER BY `posts`.`views` DESC LIMIT 3");
        
    while($popular_post = $popular_stm->fetch()) {
        $popular_posts[] = $popular_post;
    }
?>

<?php if(count($slider_posts) > 0): ?>
<div id="latestPostsSlider" class="carousel slide">
    <div class="carousel-indicators">
        <?php foreach($slider_posts as $isp => $sp): ?>
            <button 
            type="button" 
            data-bs-target="#latestPostsSlider" 
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
        <?php foreach($slider_posts as $isp => $sp): ?>
            <div class="carousel-item <?= ($isp === 0) ? 'active' : '' ?>">
                <img src="post_images/<?= $sp['name'] ?>" class="d-block w-100" alt="<?= $sp['title'] ?>" />
                <div class="carousel-caption d-none d-md-block">
                    <h5><?= $sp['title'] ?></h5>
                    <p><?= substr($sp['content'], 0, 100) ."..." ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#latestPostsSlider" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#latestPostsSlider" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
<?php endif; ?>

<section class="popular-posts">
    <div class="container">
        <h2 class="text-center">Popular posts</h2>
        <div class="row mt-5">
            <?php foreach($popular_posts as $post): ?>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <img src="post_images/<?= $post['name'] ?>" alt="<?= $post['title'] ?>" class="img-fluid mb-4">
                        <h3><?= $post['title'] ?></h3>
                        <a href="post.php?id=<?= $post['id'] ?>">Read more</a>
                    </div>
                </div>
            </div> <!-- ./row -->
            <?php endforeach; ?>
        </div>
        <div class="mt-5 d-flex justify-content-center">
            <a href="posts.php" class="btn btn-sm btn-outline-primary">All posts</a>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>