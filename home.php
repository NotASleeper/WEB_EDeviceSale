<?php
include 'components/connect.php';

session_start();



if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role !== 'employee') {
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

<body>
    <!-- starts header -->
    <?php include 'components\header.php' ?>
    <!-- ends header -->

    <!-- section title starts -->
    <section class="section-title">
        <a href="home.php">home</a>
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
            <form method="GET" class="cate-box" style="text-decoration: none;" enctype="multipart/form-data" action="view_gadget_category.php">
                <input type="hidden" name="category" value="smartphone">
                <button type="submit" style="display: flex; flex-direction: column; align-items: center; background: none; border: none; padding: 0;">
                    <img src="images/icon_cellphone.png">
                    <h3>smartphone</h3>
                </button>
            </form>

            <form method="GET" class="cate-box" style="text-decoration: none;" enctype="multipart/form-data" action="view_gadget_category.php">
                <input type="hidden" name="category" value="laptop">
                <button type="submit" style="display: flex; flex-direction: column; align-items: center; background: none; border: none; padding: 0;">
                    <img src="images/icon_laptop.png">
                    <h3>laptop</h3>
                </button>
            </form>

            <form method="GET" class="cate-box" style="text-decoration: none;" enctype="multipart/form-data" action="view_gadget_category.php">
                <input type="hidden" name="category" value="smartwatch">
                <button type="submit" style="display: flex; flex-direction: column; align-items: center; background: none; border: none; padding: 0;">
                    <img src="images/icon_smartwatch.png">
                    <h3>smartwatch</h3>
                </button>
            </form>

            <form method="GET" class="cate-box" style="text-decoration: none;" enctype="multipart/form-data" action="view_gadget_category.php">
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
            <a class="btn-add" href="create_gadget.php">+ Add New</a>
        </div>
        <div class="container">
            <!-- <form class="product-box" action="" method="POST">
                <div>
                    <a><i class="fa-solid fa-pen-to-square"></i></a>
                    <a><i class="fa-solid fa-trash"></i></a>
                </div>
                <img src="images/lenovo.png">
                <h2 class="gadget_title">Laptop HP Pavilion 15 eg3098TU i3 1315U/8GB/256GB/Win11 (8C5L9PA)</h2>
                <p>Smartphone</p>
                <h2 class="gadget_price">1,000,000</h2>
            </form> -->
            <!-- 11-15-2024 -->
            <?php
            // $select_products = $conn->prepare("SELECT * FROM `gadget`");
            // $select_products->execute();
            if ($select_products->rowCount() > 0) {
                while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <form class="product-box" action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="pid" value="<?= $fetch_products['id_gadget']; ?>">
                        <div>
                            <a href="update_gadget.php?id=<?= $fetch_products['id_gadget']; ?>"><i class=" fa-solid fa-pen-to-square"></i></a>
                            <a href="javascript:void(0);" onclick="confirmDelete(<?= $fetch_products['id_gadget']; ?>)"><i class="fa-solid fa-trash"></i></a>
                        </div>

                        <a href="view_gadget.php?id=<?= $fetch_products['id_gadget']; ?>">
                            <img src="images/img_gadget/<?= $fetch_products['pic_gadget']; ?>">
                            <!-- <img src="images/img_gadget/1731763200.jpg"> -->
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
    <script>
        function confirmDelete(gadgetId) {
            //confirmation
            if (confirm("Are you sure you want to delete this gadget?")) {
                // Redirect to PHP file with the ID to delete
                window.location.href = 'delete_gadget.php?id=' + gadgetId;
            }
        }
    </script>
</body>

</html>





















<!-- <a class="cate-box" style="text-decoration: none;" href="view_gadget_category.php?category=smartwatch">
                <img src=" images/icon_tablet.png">
                <h3>tablet</h3>
            </a> -->