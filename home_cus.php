<?php
include 'components/connect.php';

session_start();



if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
} else {
    $user_id = '';
    $role = '';
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
    <!-- font-for-chatbot-icon -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/chatbot.css">
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

    <!-- chatbot button -->
    <?php
    if (isset($_SESSION['user_id'])) {
    ?>
        <button id="chatbot-toggler">
            <span class="material-symbols-rounded">comment</span>
            <span class="material-symbols-rounded">close</span>
        </button>
    <?php
    }
    ?>


    <div class="chatbot-popup">
        <!-- chatbot header -->
        <div class="chat-header">
            <div class="header-info">
                <svg class="chatbot-logo" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 1024 1024">
                    <path d="M738.3 287.6H285.7c-59 0-106.8 47.8-106.8 106.8v303.1c0 59 47.8 106.8 106.8 106.8h81.5v111.1c0 .7.8 1.1 1.4.7l166.9-110.6 41.8-.8h117.4l43.6-.4c59 0 106.8-47.8 106.8-106.8V394.5c0-59-47.8-106.9-106.8-106.9zM351.7 448.2c0-29.5 23.9-53.5 53.5-53.5s53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5-53.5-23.9-53.5-53.5zm157.9 267.1c-67.8 0-123.8-47.5-132.3-109h264.6c-8.6 61.5-64.5 109-132.3 109zm110-213.7c-29.5 0-53.5-23.9-53.5-53.5s23.9-53.5 53.5-53.5 53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5zM867.2 644.5V453.1h26.5c19.4 0 35.1 15.7 35.1 35.1v121.1c0 19.4-15.7 35.1-35.1 35.1h-26.5zM95.2 609.4V488.2c0-19.4 15.7-35.1 35.1-35.1h26.5v191.3h-26.5c-19.4 0-35.1-15.7-35.1-35.1zM561.5 149.6c0 23.4-15.6 43.3-36.9 49.7v44.9h-30v-44.9c-21.4-6.5-36.9-26.3-36.9-49.7 0-28.6 23.3-51.9 51.9-51.9s51.9 23.3 51.9 51.9z"></path>
                </svg>
                <h2 class="logo-text">Chatbot</h2>
            </div>
            <button id="close-chatbot" class="material-symbols-rounded">
                keyboard_arrow_down
            </button>
        </div>

        <!-- chatbot body -->
        <div class="chat-body">
            <div class="messaged bot-message">
                <svg class="bot-avatar" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 1024 1024">
                    <path d="M738.3 287.6H285.7c-59 0-106.8 47.8-106.8 106.8v303.1c0 59 47.8 106.8 106.8 106.8h81.5v111.1c0 .7.8 1.1 1.4.7l166.9-110.6 41.8-.8h117.4l43.6-.4c59 0 106.8-47.8 106.8-106.8V394.5c0-59-47.8-106.9-106.8-106.9zM351.7 448.2c0-29.5 23.9-53.5 53.5-53.5s53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5-53.5-23.9-53.5-53.5zm157.9 267.1c-67.8 0-123.8-47.5-132.3-109h264.6c-8.6 61.5-64.5 109-132.3 109zm110-213.7c-29.5 0-53.5-23.9-53.5-53.5s23.9-53.5 53.5-53.5 53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5zM867.2 644.5V453.1h26.5c19.4 0 35.1 15.7 35.1 35.1v121.1c0 19.4-15.7 35.1-35.1 35.1h-26.5zM95.2 609.4V488.2c0-19.4 15.7-35.1 35.1-35.1h26.5v191.3h-26.5c-19.4 0-35.1-15.7-35.1-35.1zM561.5 149.6c0 23.4-15.6 43.3-36.9 49.7v44.9h-30v-44.9c-21.4-6.5-36.9-26.3-36.9-49.7 0-28.6 23.3-51.9 51.9-51.9s51.9 23.3 51.9 51.9z"></path>
                </svg>
                <div class="message-text">
                    Welcome to Techhub
                    <br>
                    How can I assist you?
                </div>
            </div>
        </div>

        <!-- chatbot footer -->
        <div class="chat-footer">
            <form action="#" class="chat-form">
                <textarea class="message-input" placeholder="Message..." required></textarea>
                <div class="chat-controls">
                    <button id="send-message" type="submit" class="material-symbols-rounded">
                        arrow_upward
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- starts footer -->
    <?php include 'components\footer.php' ?>
    <!-- ends footer -->

    <script src="js/index.js"></script>
    <script src="js/chatbot.js"></script>

</body>

</html>