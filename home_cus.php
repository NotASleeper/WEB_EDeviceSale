<?php
include 'components/connect.php';

session_start();



if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role !== 'customer') {
    echo "Bạn không có quyền xem trang này!";
    exit();
}



//find var -> empty
$search_query = '';

//if choose to find -> find var not empty
if (isset($_GET['txt_input'])) {
    $search_query = $_GET['txt_input'];
}

//find var not empty -> $select_products LIKE
if ($search_query != '') {
    $select_products = $conn->prepare("SELECT * FROM `gadget` WHERE `name_gadget` LIKE :search_query");
    $select_products->bindValue(':search_query', '%' . $search_query . '%');
} else {
    //else ->load all
    $select_products = $conn->prepare("SELECT * FROM `gadget`");
}

$select_products->execute();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <!-- 28/10/2024 -->
    <link rel="icon" href="images/logocart.png" type="image/png">

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/style.css">
</head>

<script>
    function confirmAddToCart() {
        // Hiển thị thông báo xác nhận
        var confirmation = window.confirm("Bạn có muốn tiếp tục mua sản phẩm này không?");

        // Trả về true nếu người dùng nhấn OK, false nếu nhấn Cancel
        return confirmation;
    }
</script>

<body>
    <!-- starts header -->
    <?php include 'components\cus_header.php' ?>
    <!-- ends header -->

    <!-- section title starts -->
    <section class="section-title">
        <a href="home_cus.php">home</a>
    </section>
    <!-- section title ends -->

    <!-- section search starts -->
    <section class="search-section">
        <form class="search-div" action="home.php" method="GET" enctype="multipart/form-data">
            <input name="txt_input" placeholder="Enter name..." value="<?= isset($_GET['txt_input']) ? $_GET['txt_input'] : ''; ?>">
            <button style="background-color: white;" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </section>
    <!-- section search ends -->

    <!-- section preview-categories starts -->
    <section class="preview-categories">
        <h2>categories</h2>
        <div class="container">
            <form method="GET" class="cate-box" style="text-decoration: none;" enctype="multipart/form-data" action="view_gadget_category_cus.php">
                <input type="hidden" name="category" value="smartphone">
                <button type="submit" style="display: flex; flex-direction: column; align-items: center; background: none; border: none; padding: 0;">
                    <img src="images/icon_cellphone.png">
                    <h3>smartphone</h3>
                </button>
            </form>

            <form method="GET" class="cate-box" style="text-decoration: none;" enctype="multipart/form-data" action="view_gadget_category_cus.php">
                <input type="hidden" name="category" value="laptop">
                <button type="submit" style="display: flex; flex-direction: column; align-items: center; background: none; border: none; padding: 0;">
                    <img src="images/icon_laptop.png">
                    <h3>laptop</h3>
                </button>
            </form>

            <form method="GET" class="cate-box" style="text-decoration: none;" enctype="multipart/form-data" action="view_gadget_category_cus.php">
                <input type="hidden" name="category" value="smartwatch">
                <button type="submit" style="display: flex; flex-direction: column; align-items: center; background: none; border: none; padding: 0;">
                    <img src="images/icon_smartwatch.png">
                    <h3>smartwatch</h3>
                </button>
            </form>

            <form method="GET" class="cate-box" style="text-decoration: none;" enctype="multipart/form-data" action="view_gadget_category_cus.php">
                <input type="hidden" name="category" value="accessory">
                <button type="submit" style="display: flex; flex-direction: column; align-items: center; background: none; border: none; padding: 0;">
                    <img src="images/icon_accessory.png">
                    <h3>accessory</h3>
                </button>
            </form>
        </div>
    </section>
    <!-- section preview-categories ends -->

    <!-- section products starts -->
    <section class="products">
        <div class="product-title">
            <h2>products</h2>
        </div>
        <div class="container">
            <!-- 11-15-2024 -->
            <?php
            if ($select_products->rowCount() > 0) {
                while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <form class="product-box" action="add_cart.php" method="POST" enctype="multipart/form-data" onsubmit="return confirmAddToCart()">
                        <input type="hidden" name="pid" value="<?= $fetch_products['id_gadget']; ?>">
                        <div>
                            <button type="submit" class="cart-icon">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </button>
                        </div>

                        <a href="view_gadget_cus.php?id=<?= $fetch_products['id_gadget']; ?>">
                            <img src="images/img_gadget/<?= $fetch_products['pic_gadget']; ?>">
                        </a>

                        <h2 class="gadget_title"><?= $fetch_products['name_gadget']; ?></h2>
                        <p><?= $fetch_products['category']; ?></p>
                        <h2 class="gadget_price"><?= number_format($fetch_products['exp_gadget'], 0, '.', ','); ?></h2>
                    </form>
            <?php
                }
            } else {
                echo "
                <section class='sec-delete-gadget'>
                    <h2>NO PRODUCT FOUND</h2>
                </section>
            ";
            }
            ?>
        </div>
    </section>
    <!-- section products ends -->

    <!-- starts footer -->
    <?php include 'components\footer.php' ?>
    <!-- ends footer -->

    <script src="js/index.js"></script>
</body>

</html>





















<!-- <a class="cate-box" style="text-decoration: none;" href="view_gadget_category.php?category=smartwatch">
                <img src=" images/icon_tablet.png">
                <h3>tablet</h3>
            </a> -->