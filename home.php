<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- starts header -->
     <!-- kukujgghgj -->
    <?php include 'components\header.php' ?>
    <!-- ends header -->

    <!-- section title starts -->
    <section class="section-title">
        <a href="">home</a>
    </section>
    <!-- section title ends -->

    <!-- section search starts -->
    <section class="search-section">
        <div class="search-div">
            <input name="txt_input" placeholder="Search...">
            <i class="fa-solid fa-magnifying-glass"></i>        
        </div>
    </section>
    <!-- section search ends -->
    
    <!-- section preview-categories starts -->
    <section class="preview-categories">
        <h2>categories</h2>
        <div class="container">
            <a class="cate-box">
                <img src="images/icon_cellphone.png">
                <h3>smartphone</h3>
            </a>
            <a class="cate-box">
                <img src="images/icon_laptop.png">
                <h3>laptop</h3>
            </a>
            <a class="cate-box">
                <img src="images/icon_tablet.png">
                <h3>tablet</h3>
            </a>
            <a class="cate-box">
                <img src="images/icon_smartwatch.png">
                <h3>smartwatch</h3>
            </a>
            <a class="cate-box">
                <img src="images/icon_accessory.png">
                <h3>accessory</h3>
            </a>
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
            <div class="product-box">
                <div>
                    <a><i class="fa-solid fa-pen-to-square"></i></a>
                    <a><i class="fa-solid fa-trash"></i></a>
                </div>
                <img>
                <h2 class="gadget_title">Laptop HP Pavilion 15 eg3098TU i3 1315U/8GB/256GB/Win11 (8C5L9PA)</h2>
                <p>efwe</p>
                <h2 class="gadget_price">1,000,000</h2>
            </div>

        </div>
    </section>
    <!-- section products ends -->

    <!-- starts footer -->
    <?php include 'components\footer.php' ?>
    <!-- ends footer -->

    <script src="js/index.js"></script>
</body>
</html>