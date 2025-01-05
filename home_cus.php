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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header_footer.css">
</head>

<script>
function confirmAddToCart(form) {
    // Hiển thị hộp thoại để người dùng nhập số lượng
    var quantity = prompt("Nhập số lượng sản phẩm bạn muốn thêm:", "1");

    // Kiểm tra dữ liệu nhập
    if (quantity === null || quantity.trim() === "" || isNaN(quantity) || quantity <= 0) {
        alert("Số lượng không hợp lệ!");
        return false; // Không gửi biểu mẫu
    }

    // Gán số lượng vào trường ẩn của sản phẩm cụ thể
    var quantityInput = form.querySelector('input[name="quantity"]');
    quantityInput.value = quantity;
    return true; // Gửi biểu mẫu
}



function handleSelectChange(select) {
    if (select.value !== "") {
        // Nếu không phải "Xem tất cả", tự động submit form
        select.form.submit();
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const slideshow = document.querySelector('.banner-slideshow');

    // Mảng các đường dẫn ảnh
    const images = [
        './images/banner1.png',
        './images/banner2.jpg',
        './images/banner1.png'
    ];

    let currentIndex = 0; // Chỉ số ảnh hiện tại

    function changeBackground() {
        // Cập nhật ảnh nền của slideshow
        slideshow.style.backgroundImage = `url('${images[currentIndex]}')`;
        currentIndex = (currentIndex + 1) % images.length; // Chuyển sang ảnh tiếp theo
    }

    // Hiển thị ảnh đầu tiên ngay lập tức
    changeBackground();

    // Tự động chuyển ảnh mỗi 3 giây
    setInterval(changeBackground, 3000);
});
</script>

<body>
    <!-- starts header -->
    <?php include 'components\cus_header.php' ?>
    <!-- ends header -->

    <!-- section title starts -->
    <!-- <section class="section-title">
        <a href="home_cus.php">home</a>
    </section> -->
    <!-- section title ends -->

    <!-- section search starts -->
    <!-- <section class="search-section">
        <form class="search-div" action="home.php" method="GET" enctype="multipart/form-data">
            <input name="txt_input" placeholder="Enter name..."
                value="<?= isset($_GET['txt_input']) ? $_GET['txt_input'] : ''; ?>">
            <button style="background-color: white;" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </section> -->
    <!-- section search ends -->

    <!-- section preview-categories starts -->

    <section class="banner">
        <div class="welcome-message">
            <h1>Welcome to my shop!</h1>
            <button><a href="#products-list"><i class="fa-solid fa-cart-shopping"></i> Buying now </a></button>
        </div>
        <div class="banner-images">
            <div class="banner-slideshow"></div>
        </div>
    </section>



    <!-- section products starts -->
    <section class="products" id="products-list">
        <div class="product-title">
            <h2>Features products</h2>
            <form method="GET" class="cate-box" style="text-decoration: none; " enctype="multipart/form-data"
                action="view_gadget_category_cus.php">
                <select name="category" id="category-select" onchange="handleSelectChange(this)">
                    <option value="" selected>All products
                    </option>
                    <option value="smartphone">smartphone</option>
                    <option value="laptop">laptop</option>
                    <option value="smartwatch">smartwatch</option>
                    <option value="accessory">accessory</option>
                </select>
            </form>
        </div>
        <div class="container">
            <!-- 11-15-2024 -->
            <?php
            if ($select_products->rowCount() > 0) {
                while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <form class="product-box" action="add_cart.php" method="POST" enctype="multipart/form-data"
                onsubmit="return confirmAddToCart(this)">
                <input type="hidden" name="pid" value="<?= $fetch_products['id_gadget']; ?>">
                <input type="hidden" id="quantity-input-<?= $fetch_products['id_gadget']; ?>" name="quantity" value="1">
                <!-- <div>
                            <button type="submit" class="cart-icon">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </button>
                        </div> -->

                <a href="view_gadget_cus.php?id=<?= $fetch_products['id_gadget']; ?>">
                    <img src="images/img_gadget/<?= $fetch_products['pic_gadget']; ?>">
                </a>

                <div class="product-info">
                    <h2 class="gadget_title"><?= $fetch_products['name_gadget']; ?></h2>
                    <p><?= $fetch_products['category']; ?></p>
                    <h2 class="gadget_price"><?= number_format($fetch_products['exp_gadget'], 0, '.', ','); ?></h2>
                </div>
                <div class="product-action">
                    <button type="submit">
                        Add to cart
                    </button>
                </div>
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