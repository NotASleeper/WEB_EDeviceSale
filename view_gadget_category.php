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



if (isset($_GET['category'])) {
    $category = $_GET['category'];
    //check if category exits;

    if ($category !== 'laptop' && $category !== 'smartphone' && $category !== 'smartwatch' && $category !== 'accessory') {
        header('Location: home.php');
        exit();
    }
} else {
    header('Location: home.php');
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
    $select_products = $conn->prepare("SELECT * FROM `gadget` WHERE `name_gadget` LIKE :search_query AND `category` = :category");
    $select_products->bindValue(':search_query', '%' . $search_query . '%');
    $select_products->bindValue(':category', $category);
} else {
    //else ->load all
    $select_products = $conn->prepare("SELECT * FROM `gadget` WHERE `category` = :category");
    $select_products->bindValue(':category', $category);
}

$select_products->execute();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Gadget Category</title>

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
        <a href="home.php">home</a><a style="color: black; text-transform: capitalize;">/<?= $category ?></a>
    </section>
    <!-- section title ends -->

    <!-- section search starts -->
    <section class="search-section">
        <form class="search-div" action="view_gadget_category.php" method="GET" enctype="multipart/form-data">
            <!-- Hidden input to retain the category parameter -->
            <input type="hidden" name="category" value="<?= htmlspecialchars($category); ?>">
            <input name="txt_input" placeholder="Search by name..." value="<?= isset($_GET['txt_input']) ? htmlspecialchars($_GET['txt_input']) : ''; ?>">
            <button style="background-color: white;" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </section>
    <!-- section search ends -->

    <!-- section products starts -->
    <section class="products">
        <div class="product-title">
            <h2>products</h2>
            <a class="btn-add" href="create_gadget.php">+ Add New</a>
        </div>
        <div class="container">
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
                        <h2 style='padding-bottom: 0.5rem'>NO PRODUCT AVAILABLE</h2>
                        <h2>Go back to home</h2>
                        <a href='home.php' class='btn-success' style='margin-top: 1rem; padding-top: 1rem'>Home</a>
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