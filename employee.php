<?php
include 'components/connect.php';

session_start();

// not sure 
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

// //find var -> empty
// $search_query = '';

// //if choose to find -> find var not empty
// if (isset($_GET['txt_input'])) {
//     $search_query = $_GET['txt_input'];
// }

// //find var not empty -> $select_products LIKE
// if ($search_query != '') {
//     $select_products = $conn->prepare("SELECT * FROM `gadget` WHERE `name_gadget` LIKE :search_query");
//     $select_products->bindValue(':search_query', '%' . $search_query . '%');
// } else {
//     //else ->load all
//     $select_products = $conn->prepare("SELECT * FROM `gadget`");
// }

// $select_products->execute();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee</title>

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
        <a href="employee.php">employee</a>
    </section>
    <!-- section title ends -->

    <!-- section search starts -->
    <section class="search-section">
        <form class="search-div" action="home.php" method="GET" enctype="multipart/form-data">
            <input name="txt_input" placeholder="Enter name/phone number..." value="<?= isset($_GET['txt_input']) ? $_GET['txt_input'] : ''; ?>">
            <button style="background-color: white;" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </section>
    <!-- section search ends -->


    <!-- section products starts -->
    <section class="products">
        <div class="product-title">
            <h2>employees</h2>
            <a class="btn-add" href="create_gadget.php">+ Add New</a>
        </div>
        <div class="container-employee" style="overflow-x: auto; overflow-y: auto;">
            <table class="tbl-employee">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Date of Birth</th>
                        <th>Citizen Card</th>
                        <th>Gender</th>
                        <th>Phone Number</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Joey</td>
                        <td>10/10/2000</td>
                        <td>0123456789</td>
                        <td>Male</td>
                        <td>0123456978</td>
                        <td>Manager</td>
                        <td>
                            <a href="update_gadget.php?id=<?= $fetch_products['id_gadget']; ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="javascript:void(0);" onclick="confirmDelete(<?= $fetch_products['id_gadget']; ?>)"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Joey</td>
                        <td>10/10/2000</td>
                        <td>0123456789</td>
                        <td>Male</td>
                        <td>0123456978</td>
                        <td>Manager</td>
                        <td>
                            <a href="update_gadget.php?id=<?= $fetch_products['id_gadget']; ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="javascript:void(0);" onclick="confirmDelete(<?= $fetch_products['id_gadget']; ?>)"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Joey</td>
                        <td>10/10/2000</td>
                        <td>0123456789</td>
                        <td>Male</td>
                        <td>0123456978</td>
                        <td>Manager</td>
                        <td>
                            <a href="update_gadget.php?id=<?= $fetch_products['id_gadget']; ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="javascript:void(0);" onclick="confirmDelete(<?= $fetch_products['id_gadget']; ?>)"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Joey</td>
                        <td>10/10/2000</td>
                        <td>0123456789</td>
                        <td>Male</td>
                        <td>0123456978</td>
                        <td>Manager</td>
                        <td>
                            <a href="update_gadget.php?id=<?= $fetch_products['id_gadget']; ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="javascript:void(0);" onclick="confirmDelete(<?= $fetch_products['id_gadget']; ?>)"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Joey</td>
                        <td>10/10/2000</td>
                        <td>0123456789</td>
                        <td>Male</td>
                        <td>0123456978</td>
                        <td>Manager</td>
                        <td>
                            <a href="update_gadget.php?id=<?= $fetch_products['id_gadget']; ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="javascript:void(0);" onclick="confirmDelete(<?= $fetch_products['id_gadget']; ?>)"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Joey</td>
                        <td>10/10/2000</td>
                        <td>0123456789</td>
                        <td>Male</td>
                        <td>0123456978</td>
                        <td>Manager</td>
                        <td>
                            <a href="update_gadget.php?id=<?= $fetch_products['id_gadget']; ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="javascript:void(0);" onclick="confirmDelete(<?= $fetch_products['id_gadget']; ?>)"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Joey</td>
                        <td>10/10/2000</td>
                        <td>0123456789</td>
                        <td>Male</td>
                        <td>0123456978</td>
                        <td>Manager</td>
                        <td>
                            <a href="update_gadget.php?id=<?= $fetch_products['id_gadget']; ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="javascript:void(0);" onclick="confirmDelete(<?= $fetch_products['id_gadget']; ?>)"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Joey</td>
                        <td>10/10/2000</td>
                        <td>0123456789</td>
                        <td>Male</td>
                        <td>0123456978</td>
                        <td>Manager</td>
                        <td>
                            <a href="update_gadget.php?id=<?= $fetch_products['id_gadget']; ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="javascript:void(0);" onclick="confirmDelete(<?= $fetch_products['id_gadget']; ?>)"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Joey</td>
                        <td>10/10/2000</td>
                        <td>0123456789</td>
                        <td>Male</td>
                        <td>0123456978</td>
                        <td>Manager</td>
                        <td>
                            <a href="update_gadget.php?id=<?= $fetch_products['id_gadget']; ?>"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="javascript:void(0);" onclick="confirmDelete(<?= $fetch_products['id_gadget']; ?>)"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>

                </tbody>
            </table>
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