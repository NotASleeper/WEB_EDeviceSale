<?php
if (isset($message)) {
    foreach ($message as $message) {
        echo `
        <div class="message">
        <span>` . $message . `</span>
        <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>`;
    }
}
?>



<header class="header-container">
    <section class="flex">
        <a class="comp-name" href="home.php">S.B.'s Store</a>
        <nav class="menu-bar">
            <a href="home.php">home</a>
            <!-- <a>search</a> -->
            <a>import</a>
            <a>export</a>
            <a>customer</a>
            <a>employee</a>
            <a>report</a>
        </nav>
        <div class="icon">

            <i id="user-btn" class="fas fa-user"></i>
            <i id="menu-btn" class="fas fa-solid fa-bars"></i>
        </div>

        <div class="profile">
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `employee` WHERE id_employee = ?");
            $select_profile->execute([$user_id]);
            if ($select_profile->rowCount() > 0) {
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

            ?>
                <!-- not sure  -->
                <h2 class="name"><? $fetch_profile['name_employee'] ?></h2>
                <div>
                    <a class="btn-success">Edit profile</a>
                    <a class="btn-danger">Log out</a>
                </div>
            <?php
            } else {
            ?>
                <h2 class="name">Please login first</h2>
                <button class="btn-success">Log in</button>
            <?php
            }
            ?>

        </div>
    </section>
</header>