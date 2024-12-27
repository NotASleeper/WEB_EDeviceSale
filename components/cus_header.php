<?php
include 'components/message.php';
?>


<header class="header-container">
    <section class="flex">
        <a class="comp-name" href="home_cus.php">S.B.'s Store</a>
        <nav class="menu-bar">
            <a href="home_cus.php">home</a>
            <a href="view_gadget_category_cus.php">
                <form method="GET" class="cate-box" style="text-decoration: none;" enctype="multipart/form-data" action="view_gadget_category_cus.php">
                    <input type="hidden" name="category" value="smartphone">
                    <button type="submit" style="display: flex; flex-direction: column; align-items: center; background: none; border: none; padding: 0;">
                        Smartphone
                    </button>
                </form>
            </a>
            <a href="view_gadget_category_cus.php">
                <form method="GET" class="cate-box" style="text-decoration: none;" enctype="multipart/form-data" action="view_gadget_category_cus.php">
                    <input type="hidden" name="category" value="laptop">
                    <button type="submit" style="display: flex; flex-direction: column; align-items: center; background: none; border: none; padding: 0;">
                        Laptop
                    </button>
                </form>
            </a>

            <a href="view_gadget_category_cus.php">
                <form method="GET" class="cate-box" style="text-decoration: none;" enctype="multipart/form-data" action="view_gadget_category_cus.php">
                    <input type="hidden" name="category" value="smartwatch">
                    <button type="submit" style="display: flex; flex-direction: column; align-items: center; background: none; border: none; padding: 0;">
                        Smartwatch
                    </button>
                </form>
            </a>

            <a href="view_gadget_category_cus.php">
                <form method="GET" class="cate-box" style="text-decoration: none;" enctype="multipart/form-data" action="view_gadget_category_cus.php">
                    <input type="hidden" name="category" value="accessory">
                    <button type="submit" style="display: flex; flex-direction: column; align-items: center; background: none; border: none; padding: 0;">
                        Accessory
                    </button>
                </form>
            </a>
            <a href="order.php">order</a>
        </nav>
        <div class="icon">
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
                    <a href="components/user_logout.php"
                        onclick="return confirm('logout from this website?');"
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