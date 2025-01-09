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

// Lấy danh sách gadget
$gadgets = $conn->prepare("SELECT id_gadget, name_gadget, imp_gadget FROM gadget");
$gadgets->execute();
$gadget_list = $gadgets->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {
    // Lấy dữ liệu từ form
    $id_import = $_POST['id_import'];
    // $id_import = filter_var($id_import, FILTER_SANITIZE_STRING);

    $id_gadget = $_POST['id_gadget'];
    $id_gadget = filter_var($id_gadget, FILTER_SANITIZE_STRING);

    $im_price = $_POST['im_price'];
    $im_price = filter_var($im_price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    $quantity = $_POST['quantity'];
    $quantity = filter_var($quantity, FILTER_SANITIZE_NUMBER_INT);

    // Tính tổng
    $total = $im_price * $quantity;

    // Kiểm tra trùng lặp chi tiết phiếu nhập
    $select_detail = $conn->prepare("SELECT * FROM `import_detail` WHERE id_import = ? AND id_gadget = ?");
    $select_detail->execute([$id_import, $id_gadget]);

    if ($select_detail->rowCount() > 0) {
        // Nếu chi tiết phiếu nhập đã tồn tại, cập nhật số lượng và tổng giá trị
        $existing_detail = $select_detail->fetch(PDO::FETCH_ASSOC);
        $new_quantity = $existing_detail['quantity'] + $quantity;
        $new_total = $existing_detail['im_price'] * $new_quantity;

        $update_detail = $conn->prepare("UPDATE `import_detail` SET quantity = ?, total = ? WHERE id_import = ? AND id_gadget = ?");
        $update_detail->execute([$new_quantity, $new_total, $id_import, $id_gadget]);

        // Cập nhật tổng tiền (sum) của phiếu nhập
        $update_import = $conn->prepare("UPDATE `import` 
        SET sum = sum + :total 
        WHERE id_import = :id_import");
        $update_import->bindValue(':total', $total);
        $update_import->bindValue(':id_import', $id_import);
        $update_import->execute();

        // Cập nhật số lượng của gadget
        $update_gadget = $conn->prepare("UPDATE `gadget` SET quantity = quantity + :quantity WHERE id_gadget = :id_gadget");
        $update_gadget->bindValue(':quantity', $quantity);
        $update_gadget->bindValue(':id_gadget', $id_gadget);
        $update_gadget->execute();
        
        header("Location: create_import.php?id_import=" . $id_import);
    } else {
        // Thêm chi tiết phiếu nhập vào cơ sở dữ liệu
        $insert_detail = $conn->prepare("INSERT INTO `import_detail` (id_import, id_gadget, im_price, quantity, total) VALUES (?,?,?,?,?)");
        $insert_detail->execute([$id_import, $id_gadget, $im_price, $quantity, $total]);

        // Kiểm tra kết quả
        if ($insert_detail->rowCount() > 0) {
            // Cập nhật tổng tiền (sum) của phiếu nhập
            $update_import = $conn->prepare("UPDATE `import` 
                SET sum = sum + :total 
                WHERE id_import = :id_import");
            $update_import->bindValue(':total', $total);
            $update_import->bindValue(':id_import', $id_import);
            $update_import->execute();

            // Cập nhật số lượng của gadget
        $update_gadget = $conn->prepare("UPDATE `gadget` SET quantity = quantity + :quantity WHERE id_gadget = :id_gadget");
        $update_gadget->bindValue(':quantity', $quantity);
        $update_gadget->bindValue(':id_gadget', $id_gadget);
        $update_gadget->execute();
            // $message[] = "Import detail added successfully!";
            header("Location: create_import.php?id_import=" . $id_import);
        } else {
            $message[] = "Failed to add import detail.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Import Detail</title>

    <link rel="icon" href="images/logocart.png" type="image/png">

    <!-- Font Awesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/style.css">
    <style>
    :root {
        --primary-color: #4a4a4a;
        --secondary-color: #f0f0f0;
        --accent-color: #ffd700;
        --success-color: #28a745;
        --error-color: #dc3545;
    }

    body {
        font-family: 'Arial', sans-serif;
        line-height: 1.6;
        color: var(--primary-color);
        background-color: var(--secondary-color);
        margin: 0;
    }

    .create-importdetail {
        background-image: url('images/sub_bg.png');
        min-height: 650px;
        max-width: 600px;
        margin: 0 auto;
        background-color: #ffffff;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .create-importdetail h1 {
        color: var(--accent-color);
        text-align: center;
        margin-bottom: 2rem;
        font-size: 2rem;
        text-transform: uppercase;
    }

    .create-importdetail form {
        display: grid;
        gap: 1.5rem;
    }

    .create-importdetail h3 {
        margin: 0;
        font-size: 1.5rem;
        color: var(--primary-color);
    }

    .create-importdetail input,
    select {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 1.4rem;
        transition: border-color 0.3s ease;
    }

    .create-importdetail input:focus,
    select:focus {
        outline: none;
        border-color: var(--accent-color);
        box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
    }

    .create-importdetail input[readonly] {
        background-color: #f9f9f9;
        cursor: not-allowed;
    }

    .create-importdetail .gadget-buttons {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .create-importdetail button {
        padding: 0.8rem 1.5rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.3s ease, transform 0.1s ease;
    }

    .create-importdetail button:hover {
        transform: translateY(-2px);
    }

    .create-importdetail button:active {
        transform: translateY(0);
    }

    .create-importdetail .btn-success {
        background-color: var(--success-color);
        color: white;
    }

    .create-importdetail .btn-success:hover {
        background-color: #218838;
    }

    .create-importdetail .btn-second-green {
        background-color: #6c757d;
        color: white;
    }

    .create-importdetail .btn-second-green:hover {
        background-color: #5a6268;
    }

    @media (max-width: 480px) {
        .create-importdetail {
            padding: 1rem;
        }

        .create-importdetail h1 {
            font-size: 1.5rem;
        }

        .create-importdetail .gadget-buttons {
            flex-direction: column;
        }

        .create-importdetail button {
            width: 100%;
        }
    }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include 'components\header_sim.php'; ?>

    <!-- Section Create Import Detail -->
    <section class="create-importdetail">
        <h1 style="color:rgb(250, 160, 41)">CREATE A NEW IMPORT DETAIL</h1>

        <form action="" id="form-c-gadget" method="POST" enctype="multipart/form-data">
            <h3>Import ID:</h3>
            <input name="id_import" placeholder="Import ID" maxlength="50" value="<?= $_GET['id_import'] ?>" readonly
                required>
            <h3>Gadget:</h3>
            <!-- <input name="id_gadget" placeholder="Gadget ID" maxlength="50" value="" required> -->
            <select name="id_gadget" id="id_gadget" onchange="updatePrice()" required>
                <option value="" disabled selected>Select a gadget</option>
                <?php foreach ($gadget_list as $gadget): ?>
                <option value="<?= $gadget['id_gadget']; ?>" data-price="<?= $gadget['imp_gadget']; ?>">
                    <?= $gadget['name_gadget']; ?>
                </option>
                <?php endforeach; ?>
            </select>
            <h3>Import Price:</h3>
            <input name="im_price" id="im_price" placeholder="Import Price" type="number" step="0.01" value="" readonly
                required>
            <h3>Quantity:</h3>
            <input name="quantity" placeholder="Quantity" type="number" value="" required>

            <div class="gadget-buttons" style="margin-top: 1rem;">
                <button type="submit" name="submit" class="btn-success">Add</button>
                <button type="reset" class="btn-second-green addgg-clear">Clear</button>
            </div>
        </form>
        <script>
        function updatePrice() {
            const gadgetSelect = document.getElementById('id_gadget');
            const priceInput = document.getElementById('im_price');

            const selectedOption = gadgetSelect.options[gadgetSelect.selectedIndex];
            const price = selectedOption.getAttribute('data-price');

            priceInput.value = price || '';
        }
        </script>
    </section>

    <!-- Footer -->
    <?php include 'components\footer.php'; ?>
</body>

</html>