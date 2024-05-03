<?php 
  include('includes/header.php'); 
  include('helpers.php'); 

  if(!isset($_SESSION['is_loggedin'])) {
    header('Location: login.php');
  }

  if($user['role'] == 'admin') {
    // select
    $posts = [];
    $sql = "SELECT * FROM `posts` WHERE `user_id` = ".$_SESSION['id'];
    $stm = $pdo->query($sql);

    while($post = $stm->fetch()) {
      $posts[] = $post;
    }

    // get categories
    $categories = [];
    $categories_sql = "SELECT * FROM `categories`";
    $categories_stm = $pdo->query($categories_sql);
      
    while($category = $categories_stm->fetch()) {
      $categories[] = $category;
    }

    // create
    if(isset($_POST['newpost_btn'])) {
      $title = $_POST['title'];
      $content = $_POST['content'];
      $post_categories = $_POST['category'];
      $media = $_FILES['media'];

      $sql = "INSERT INTO `posts` (`user_id`, `title`, `content`) VALUES (?, ?, ?)";
      $stm = $pdo->prepare($sql);


      if($stm->execute([$_SESSION['id'], $title, $content])) {
        $post_id = $pdo->lastInsertId();

        // upload + insert media
        if(strlen($media['name'][0]) > 0) {
          for($i = 0; $i < count($media['name']); $i++) {
            $filename = time().'_'.$media['name'][$i];
            $type = setEnumType($media['type'][$i]);
            move_uploaded_file($media['tmp_name'][$i], 'post_images/'.$filename);
            $msql = "INSERT INTO `media` (`post_id`, `type`, `name`) VALUES (?, ?, ?)";
            $mstm = $pdo->prepare($msql);
            $mstm->execute([$post_id, $type, $filename]);
          }
        }

        // assign categories
        if(count($post_categories) > 0) {
          foreach($post_categories as $category) {
            $csql = "INSERT INTO `category_post` (`category_id`, `post_id`) VALUES (?, ?)";
            $cstm = $pdo->prepare($csql);
            $cstm->execute([$category, $post_id]);
          }
        }

        header('Location: dashboard.php');
      }
    }

    // update


    // delete
    if(isset($_GET['action']) && ($_GET['action'] === 'delete')) {
      $id = $_GET['id'];
      $dsql = "DELETE FROM `posts` WHERE `id` = ?";
      $dstm = $pdo->prepare($dsql);
      if($dstm->execute([$id])) {
        header('Location: dashboard.php');
      }
    }


    // update
    if(isset($_POST['update_btn'])) {
      $post_id = $_POST['post_id'];
      $title = $_POST['title'];
      $content = $_POST['content'];
      $post_categories = $_POST['category'];
      $media = $_FILES['media'];

      $sql = "UPDATE `posts` SET `title` = ?, `content` = ? WHERE `id` = ?";
      $stm = $pdo->prepare($sql);


      if($stm->execute([$title, $content, $post_id])) {
        // upload + insert media
        if(strlen($media['name'][0]) > 0) {
          for($i = 0; $i < count($media['name']); $i++) {
            $filename = time().'_'.$media['name'][$i];
            $type = setEnumType($media['type'][$i]);
            move_uploaded_file($media['tmp_name'][$i], 'post_images/'.$filename);
            $msql = "INSERT INTO `media` (`post_id`, `type`, `name`) VALUES (?, ?, ?)";
            $mstm = $pdo->prepare($msql);
            $mstm->execute([$post_id, $type, $filename]);
          }
        }

        // assign categories
        if(count($post_categories) > 0) {
          foreach($post_categories as $category) {
            $csql = "INSERT INTO `category_post` (`category_id`, `post_id`) VALUES (?, ?)";
            $cstm = $pdo->prepare($csql);
            $cstm->execute([$category, $post_id]);
          }
        }

        header('Location: dashboard.php');
      }
    }
  } else {
    $posts = [];
  }

  
?>

<section class="dashboard">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Dashboard</h3>
            <?php if($user['role'] == 'admin'): ?>
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newPostModal">
                <i class="bi bi-plus-circle"></i> New post
            </button>
            <?php endif; ?>
        </div>
        <div class="mt-4">
            <?php if(count($posts) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Views</th>
                        <th>Created at</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                      <?php foreach($posts as $post): ?>
                        <tr>
                          <td><?= $post['id'] ?></td>
                          <td><?= $post['title'] ?></td>
                          <td><?= $post['views'] ?></td>
                          <td><?= $post['created_at'] ?></td>
                          <td>
                              <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updatePostModal<?= $post['id'] ?>">
                                  <i class="bi bi-pencil-square"></i>
                              </button>
                              <!-- Update Post Modal -->
                              <div class="modal fade" id="updatePostModal<?= $post['id'] ?>" tabindex="-1" aria-labelledby="updatePostModal<?= $post['id'] ?>Label" aria-hidden="true">
                                <div class="modal-dialog">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h1 class="modal-title fs-5" id="updatePostModal<?= $post['id'] ?>Label">Update post</h1>
                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
                                      <div class="modal-body">
                                        <div class="form-group mb-3">
                                            <label for="title">Title</label>
                                            <input type="text" name="title" id="title" value="<?= $post['title'] ?>" class="form-control" />
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="category">Category</label>
                                            <select name="category[]" multiple id="category" class="form-control">
                                              <option value="">Select category</option>
                                              <?php if(count($categories) > 0): ?>
                                                <?php foreach($categories as $cat): ?>
                                                  <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                                                <?php endforeach; ?>
                                              <?php endif; ?>
                                            </select>
                                        </div>
                                        <textarea name="content" class="editor"><?= $post['content'] ?></textarea>
                                        <div class="form-group my-3">
                                            <label for="media">Media</label>
                                            <input type="file" multiple name="media[]" id="media" class="form-control" />
                                        </div>
                                      </div>
                                      <div class="modal-footer">  
                                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>" />
                                        <button type="submit" name="update_btn" class="btn btn-sm btn-outline-primary">Submit</button>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                              </div>
                              <a href="?action=delete&id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                  <i class="bi bi-trash"></i>
                              </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
              <?php if($user['role'] == 'admin'): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                  0 posts
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              <?php else: ?>
                <?php
                  $cstm = $pdo->prepare("SELECT count(*) AS 'total_comments' FROM `comments` WHERE `comments`.`user_id` = ?");
                  $cstm->execute([$_SESSION['id']]);
                  $r = $cstm->fetch(PDO::FETCH_ASSOC);
                  $tc = $r['total_comments'];
                ?>
                <p>You have leave <?= $tc ?> comment(s) on the platform.</p>
              <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>


<?php if($user['role'] == 'admin'): ?>
<!-- New Post Modal -->
<div class="modal fade" id="newPostModal" tabindex="-1" aria-labelledby="newPostModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="newPostModalLabel">New post</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
      <div class="modal-body">
        <div class="form-group mb-3">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" />
        </div>
        <div class="form-group mb-3">
            <label for="category">Category</label>
            <select name="category[]" multiple id="category" class="form-control">
                <option value="">Select category</option>
                <?php if(count($categories) > 0): ?>
                  <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <textarea name="content" class="editor"></textarea>
        <div class="form-group my-3">
            <label for="media">Media</label>
            <input type="file" multiple name="media[]" id="media" class="form-control" />
        </div>
      </div>
      <div class="modal-footer">
        <button name="newpost_btn" type="submit" class="btn btn-sm btn-outline-primary">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="https://cdn.ckeditor.com/ckeditor5/41.2.0/classic/ckeditor.js"></script>
<script>
    document.querySelectorAll('.editor').forEach(textarea => ClassicEditor.create(textarea))
</script>
<?php include('includes/footer.php'); ?>