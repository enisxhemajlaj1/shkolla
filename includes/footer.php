
    <footer class="mt-4 py-3">
        <div class="container">
            <div class="d-flex justify-content-between">
                <p>Copyrights &copy; <?= $settings['title'] ?>, <?= date('Y') ?></p>
                <p>Developed by <a href="#" class="text-decoration-none">BC10 Team</a></p>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        const search = document.querySelector("input[name='search']")

        if(search) {
            search.addEventListener('keyup', e => {
                switch(e.keyCode) {
                    case 13:
                        window.location.href = `http://localhost/bc10/blog/posts.php?q=${e.target.value}`
                        break
                }
            })
        }
    </script>
</body>
</html>