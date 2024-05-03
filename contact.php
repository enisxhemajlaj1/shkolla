<?php 
    include('includes/header.php'); 

    $contact_form_errors = [];

    if(isset($_POST['send_btn'])) {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];

        // validation
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $contact_form_errors[] = "Email is not valid!";
        }

        if(count($contact_form_errors) === 0) {
            $subject = "Email from $fullname - $subject";
            $message = "Email: $email <br /> Message: $message";
            
            if(mail('contact@platform.com', $subject, $message)) {
                header('Location: contact.php?status=1');
            } else {
                $contact_form_errors[] = "Something want wrong! Please try again.";
            }
        }
    }
?>

<section class="contact">
    <div class="container">
        <div class="row">
            <div class="col-8 mx-auto">
                <h2 class="mb-4">Contact me</h2>
                <?php if(count($contact_form_errors) > 0): ?>
                    <ul>
                    <?php foreach($contact_form_errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php if(isset($_GET['status']) && ($_GET['status'] == 1)): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        Thank you! Email was send successfully.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
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
                        <label for="subject">Subject</label>
                        <input type="text" name="subject" id="subject" class="form-control" />
                    </div>
                    <div class="form-group mb-3">
                        <label for="message">Message</label>
                        <textarea name="message" id="message" class="form-control"></textarea>
                    </div>
                    <button name="send_btn" type="submit" class="btn btn-sm btn-outline-primary">Send</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>