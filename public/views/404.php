<?php
//session_start();
include_once __DIR__ . '/../templates/header.php';
?>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="error-container">
        <section class="error-content">
            <h1>404 - Page not found</h1>
            <p>We are sorry, the page you are looking for does not exist.</p>
            <a href="<?= BASE_URL ?>/" class="btn-back-home">Back to the home page</a>
        </section>
    </main>

<?php
//include_once __DIR__ . "/../templates/footer-404.php";
