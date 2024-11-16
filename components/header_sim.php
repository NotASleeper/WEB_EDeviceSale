<?php
if (isset($message)) {
    foreach ($message as $message) {
        echo '
        <div class="message">
        <span>' . $message . '</span>
        <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>';
    }
}
?>

<header class="header-container">
    <section class="flex">
        <a class="comp-name" href="home.php">S.B.'s Store</a>
    </section>
</header>