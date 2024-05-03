<?php 
    include('includes/header.php'); 

    // total posts
    $tstm = $pdo->query("SELECT count(*) as 'total_posts' FROM `posts`");
    $tresult = $tstm->fetch();
    $total_posts = $tresult['total_posts'];

    // pagination
    $active_page = isset($_GET['page']) ? $_GET['page'] : 1;
    $per_page = 4;
    $total_pages = ceil($total_posts / $per_page);
    $offset = ($active_page - 1) * $per_page;

    
    // get posts
    $posts = [];
    $stm = $pdo->query("SELECT * FROM `posts` ORDER BY `id` DESC LIMIT $offset, $per_page");
        
    while($post = $stm->fetch(PDO::FETCH_ASSOC)) {
        $posts[] = $post;
    }

    // echo "<pre>";
    // print_r($posts);

    if(isset($_GET['q'])) {
        $q = $_GET['q'];
        if(strlen($q) >= 3) {
            $posts = [];
            $stm = $pdo->query("SELECT * FROM `posts` WHERE `posts`.`title` LIKE '%$q%' ORDER BY `posts`.`id` DESC");
                
            while($post = $stm->fetch()) {
                $posts[] = $post;
            }
        }
    }
?>

<section class="posts">
    <div class="container">
        <div class="row mt-5">
            <?php foreach($posts as $post): ?>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body">
                            <?php
                                $istm = $pdo->prepare("SELECT * FROM `media` WHERE `post_id` = ? LIMIT 1");
                                $istm->execute([$post['id']]);
                                $image = $istm->fetch();


                                $image_name = ($image === false) ? 'noimage.jpg' : $image['name'];
                            ?>
                            <img src="post_images/<?= $image_name ?>" alt="<?= $post['title'] ?>" class="img-fluid mb-4">
                            <h3><?= $post['title'] ?></h3>
                            <a href="post.php?id=<?= $post['id'] ?>">Read more</a>
                        </div>
                    </div>
                </div> <!-- ./row -->
            <?php endforeach; ?>
        </div>
        <?php if(($total_pages > 0) && !isset($_GET['q'])): ?>
        <div class="mt-5">
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <?php for($page = 1; $page <= $total_pages; $page++): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page ?>"><?= $page ?></a></li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include('includes/footer.php'); ?>