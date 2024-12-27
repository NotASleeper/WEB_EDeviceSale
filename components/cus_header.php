<?php
include 'components/message.php';
?>


<header class="header-container">
    <section class="flex">
        <a class="comp-name" href="home_cus.php">S.B.'s Store</a>
        <nav class="menu-bar">
            <!-- <a href="home_cus.php">home</a>
            <a href="order.php">order</a> -->

            <section class="search-section">
                <form class="search-div" action="" method="GET" enctype="multipart/form-data">
                    <input name="txt_input" placeholder="Searching..."
                        value="<?= isset($_GET['txt_input']) ? $_GET['txt_input'] : ''; ?>">
                    <button style="background-color: white;" type="submit"><i
                            class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </section>
        </nav>
        <div class="icon">
            <i id="order-btn" class="fa-solid fa-file-lines"></i>
            <a href="cart.php" class="cart-link">
                <i class="fa-solid fa-cart-shopping"></i>
            </a>
            <i id="user-btn" class="fas fa-user"></i>
            <i id="menu-btn" class="fas fa-solid fa-bars"></i>
        </div>

        <div class="profile">
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `customer` WHERE id_customer = ?");
            $select_profile->execute([$user_id]);
            if ($select_profile->rowCount() > 0) {
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

            ?>
            <!-- not sure  -->
            <h2 class="name"><?= $fetch_profile['name_customer']; ?></h2>

            <div>
                <a class="btn-success" href="edit_profile_cus.php">Edit profile</a>
                <a href="components/user_logout.php" onclick="return confirm('logout from this website?');"
                    class="btn-danger">Log out</a>
            </div>
            <?php
            } else {
            ?>
            <h2 class="name">Please login first</h2>
            <a class="btn-success" href="login.php">Log in</a>
            <?php
            }
            ?>

        </div>
    </section>
</header>