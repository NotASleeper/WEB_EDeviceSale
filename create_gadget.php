<?php
include 'components/connect.php';

if (isset($_POST['submit'])) {
    $name = $_POST['name'];     //name
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $im_price = $_POST['im_price'];     //import price
    $im_price = filter_var($im_price, FILTER_SANITIZE_STRING);

    $ex_price = $_POST['ex_price'];     //export price
    $ex_price = filter_var($ex_price, FILTER_SANITIZE_STRING);

    $description = $_POST['description'];     //description
    $description = filter_var($description, FILTER_SANITIZE_STRING);

    $category = $_POST['gadget-select'] ?? ""; //category

    $select_gadget = $conn->prepare("SELECT * FROM `gadget` WHERE name_gadget = ?");
    $select_gadget->execute([$name]);
    $row = $select_gadget->fetch(PDO::FETCH_ASSOC);
    if ($select_gadget->rowCount() > 0) {
        echo "<script>
                alert('Name has already existed!');
            </script>";
    } else {
        $file_name = $_FILES['image']['name'];  //ten file goc
        $file_tmp = $_FILES['image']['tmp_name'];   //ten 
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION); //(jpg, png, ...)

        $new_file_name = time() . '.' . $file_ext;
        $target_dir = 'images/img_gadget/';
        $new_file_path = $target_dir . $new_file_name;

        if (copy($file_tmp, $new_file_path)) {
            // $message[] = "File copied successfully as: " . $new_file_name;
        } else {
            $message[] = "Failed to copy file.";
        }

        // echo "<script>
        //         alert('$file_tmp');
        //     </script>";
        $quan = 0;
        $insert_gadget = $conn->prepare("INSERT INTO `gadget` (name_gadget, category, imp_gadget, exp_gadget, quantity, des_gadget, pic_gadget) VALUES (?,?,?,?,?,?,?)");
        $insert_gadget->execute([$name, $category, $im_price, $ex_price, 0, $description, $new_file_name]);

        $confirm_gadget = $conn->prepare("SELECT * FROM `gadget` WHERE name_gadget = ?");
        $confirm_gadget->execute([$name]);
        if ($confirm_gadget->rowCount() > 0) {
            $message[] = "Inserted";
            // header('Location: home.php');
            // exit();
        }
    }

    // echo "<script>
    //     alert('Name: $name, Import Price: $im_price, Export Price: $ex_price, Description: $description, Category: $category');
    // </script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CreateGadget</title>

    <!-- 28/10/2024 -->
    <link rel="icon" href="images/logocart.png" type="image/png">

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- starts header -->
    <?php include 'components\header_sim.php' ?>
    <!-- ends header -->

    <!-- section create_new_gadget starts -->
    <section class="create-gadget">
        <h1 style="color: yellow">CREATE A NEW GADGET</h1>
        <form action="" id="form-c-gadget" method="POST" enctype="multipart/form-data">
            <input name="name" placeholder="Name" required>
            <input name="im_price" placeholder="Import Price" type="number" onkeydown="return event.keyCode !== 69" required>
            <input name="ex_price" placeholder="Export Price" type="number" onkeydown="return event.keyCode !== 69" required>
            <input name="description" placeholder="Description" required>

            <input name="image" type="file" accept="image/*" required>
            <div class="div-container">
                <label style="color: white;">Category</label>
                <select class="gadget-select" name="gadget-select">
                    <option value="laptop">laptop</option>
                    <option value="smartphone">smartphone</option>
                    <option value="smartwatch">smartwatch</option>
                    <option value="accessory">accessory</option>
                </select>
            </div>
            <!-- <div class="div-con-type div-laptop">
                <label style="color: yellow;">Laptop Information</label>
                <input placeholder="CPU Technology" required>
                <input placeholder="Number of Cores" type="number" onkeydown="return event.keyCode !== 69" required>
                <input placeholder="Number of Threads" type="number" onkeydown="return event.keyCode !== 69" required>
                <input placeholder="RAM" type="number" onkeydown="return event.keyCode !== 69" required>
                <input placeholder="Hard Drive" type="number" onkeydown="return event.keyCode !== 69" required>
                <input placeholder="Screen" required>
                <input placeholder="Resolution" required>
                <input placeholder="Refresh Rate" required>
                <input placeholder="Dimension" required>
                <input placeholder="Weight" type="number" onkeydown="return event.keyCode !== 69" required>
                <input placeholder="Material" required>
                <input placeholder="Release Date" type="date" required>
            </div>

            <div class="div-con-type div-smartphone hidden">
                <label style="color: yellow;">Smartphone Information</label>
                <input placeholder="Display Technology" required>
                <input placeholder="Resolution" required>
                <input placeholder="Maximum brightness" required>
                <input placeholder="Rearcam Resolution" required>
                <input placeholder="Number of Flash" type="numbers" onkeydown="return event.keyCode !== 69" required>
                <input placeholder="Frontcam Resolution" required>
                <input placeholder="Operation System" required>
                <input placeholder="Chip" required>
                <input placeholder="RAM" type="number" onkeydown="return event.keyCode !== 69" required>
                <input placeholder="Storage Capacity" type="number" onkeydown="return event.keyCode !== 69" required>
                <input placeholder="Available Capacity" type="number" onkeydown="return event.keyCode !== 69" required>
                <input placeholder="Battery Capacity" required>
                <input placeholder="Battery Type" required>
            </div>

            <div class="div-con-type div-smartwatch hidden">
                <label style="color: yellow;">Smartwatch Information</label>
                <input placeholder="Display Technology" required>
                <input placeholder="Screen Size" required>
                <input placeholder="Resolution" required>
                <input placeholder="Face Material" required>
                <input placeholder="Frame Material" required>
                <input placeholder="Battery Life" type="number" onkeydown="return event.keyCode !== 69" required>
                <input placeholder="Charging Time" type="number" onkeydown="return event.keyCode !== 69" required>
                <input placeholder="Battery Capacity" type="number" onkeydown="return event.keyCode !== 69" required>
                <input placeholder="Brand" required>
            </div>

            <div class="div-con-type div-accessory hidden">
                <label style="color: yellow;">Accessory Information</label>
                <input placeholder="Model" required>
                <input placeholder="Functionality" required>
                <input placeholder="Usage Time" type="number" onkeydown="return event.keyCode !== 69" required>
                <input placeholder="Dimension" required>
                <input placeholder="Brand" required>
            </div> -->

            <div class="gadget-buttons">
                <button type="submit" name="submit" class="btn-success">Add</button>
                <button class="btn-second-green addgg-clear">Clear</button>
            </div>

        </form>
    </section>
    <!-- section create_new_gadget ends -->

    <!-- starts footer -->
    <?php include 'components\footer.php' ?>
    <!-- ends footer -->

    <script src="js/index.js"></script>
</body>

</html>

<!-- onkeydown="return event.keyCode !== 69" -->

<!-- neu k filter + sani -> <script>alert('Hacked!');</script> -->