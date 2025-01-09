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

    //check if already exists
    $select_gadget = $conn->prepare("SELECT * FROM `gadget` WHERE name_gadget = ?");
    $select_gadget->execute([$name]);
    $row = $select_gadget->fetch(PDO::FETCH_ASSOC);
    //if existed
    if ($select_gadget->rowCount() > 0) {
        $message[] = "Name has already existed!";
    } else {
        //if new, not exists
        ///save img to folder
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

        //insert to db
        $insert_gadget = $conn->prepare("INSERT INTO `gadget` (name_gadget, category, imp_gadget, exp_gadget, quantity, des_gadget, pic_gadget) VALUES (?,?,?,?,?,?,?)");
        $insert_gadget->execute([$name, $category, $im_price, $ex_price, 0, $description, $new_file_name]);

        //insert in detailed db
        $confirm_gadget = $conn->prepare("SELECT * FROM `gadget` WHERE name_gadget = ?");
        $confirm_gadget->execute([$name]);
        $row = $confirm_gadget->fetch(PDO::FETCH_ASSOC);
        if ($confirm_gadget->rowCount() > 0) {
            $gadget_id = $row['id_gadget'];
            // $message[] = "$gadget_id";

            if ($category === 'laptop') {
                $lap_cpu = $_POST['lap_cpu'];   //lap_cpu
                $lap_cpu = filter_var($lap_cpu, FILTER_SANITIZE_STRING);
                if ($lap_cpu === '')
                    $lap_cpu = null;

                $lap_core = $_POST['lap_core'];   //lap_core
                $lap_core = filter_var($lap_core, FILTER_VALIDATE_INT);
                if ($lap_core === '')
                    $lap_core = null;

                $lap_thread = $_POST['lap_thread'];   //lap_thread
                $lap_thread = filter_var($lap_thread, FILTER_VALIDATE_INT);
                if ($lap_thread === '')
                    $lap_thread = null;

                $lap_ram = $_POST['lap_ram'];   //lap_ram
                $lap_ram = filter_var($lap_ram, FILTER_VALIDATE_INT);
                if ($lap_ram === '')
                    $lap_ram = null;

                $lap_harddrive = $_POST['lap_harddrive'];   //lap_harddrive
                $lap_harddrive = filter_var($lap_harddrive, FILTER_VALIDATE_INT);
                if ($lap_harddrive === '')
                    $lap_harddrive = null;

                $lap_screen = $_POST['lap_screen'];   //lap_screen
                $lap_screen = filter_var($lap_screen, FILTER_SANITIZE_STRING);
                if ($lap_screen === '')
                    $lap_screen = null;

                $lap_resolution = $_POST['lap_resolution'];   //lap_resolution
                $lap_resolution = filter_var($lap_resolution, FILTER_SANITIZE_STRING);
                if ($lap_resolution === '')
                    $lap_resolution = null;

                $lap_refresh = $_POST['lap_refresh'];   //lap_refresh
                $lap_refresh = filter_var($lap_refresh, FILTER_SANITIZE_STRING);
                if ($lap_refresh === '')
                    $lap_refresh = null;

                $lap_dimension = $_POST['lap_dimension'];   //lap_dimension
                $lap_dimension = filter_var($lap_dimension, FILTER_SANITIZE_STRING);
                if ($lap_dimension === '')
                    $lap_dimension = null;

                $lap_weight = $_POST['lap_weight'];   //lap_weight
                $lap_weight = filter_var($lap_weight, FILTER_VALIDATE_INT);
                if ($lap_weight === '')
                    $lap_weight = null;

                $lap_material = $_POST['lap_material'];   //lap_material
                $lap_material = filter_var($lap_material, FILTER_SANITIZE_STRING);
                if ($lap_material === '')
                    $lap_material = null;

                $lap_date = $_POST['lap_date'];   //lap_date
                $lap_date = filter_var($lap_date, FILTER_SANITIZE_STRING);
                if ($lap_date === '')
                    $lap_date = null;

                //insert to table laptop
                $insert_lap = $conn->prepare("INSERT INTO `laptop` (id_gadget, cpu_tech, num_of_core, num_of_thread, ram, hard_drive, screen, resolution, refresh_rate, dimension, weight, material, release_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $insert_lap->execute([$gadget_id, $lap_cpu, $lap_core, $lap_thread, $lap_ram, $lap_harddrive, $lap_screen, $lap_resolution, $lap_refresh, $lap_dimension, $lap_weight, $lap_material, $lap_date]);

                //confirm inserting
                $confirm_lap = $conn->prepare("SELECT * FROM `laptop` WHERE id_gadget = ?");
                $confirm_lap->execute([$gadget_id]);
                if ($confirm_lap->rowCount() > 0) {
                    $message[] = "Inserted successfully";
                } else {
                    $message[] = "Failed to insert";
                }
            } else if ($category === 'smartphone') {
                $phone_tech = $_POST['phone_tech'];   //phone_tech
                $phone_tech = filter_var($phone_tech, FILTER_SANITIZE_STRING);
                if ($phone_tech === '')
                    $phone_tech = null;

                $phone_resolution = $_POST['phone_resolution'];   //phone_resolution
                $phone_resolution = filter_var($phone_resolution, FILTER_SANITIZE_STRING);
                if ($phone_resolution === '')
                    $phone_resolution = null;

                $phone_bright = $_POST['phone_bright'];   //phone_bright
                $phone_bright = filter_var($phone_bright, FILTER_SANITIZE_STRING);
                if ($phone_bright === '')
                    $phone_bright = null;

                $phone_rearcam = $_POST['phone_rearcam'];   //phone_rearcam
                $phone_rearcam = filter_var($phone_rearcam, FILTER_SANITIZE_STRING);
                if ($phone_rearcam === '')
                    $phone_rearcam = null;

                $phone_flash = $_POST['phone_flash'];   //phone_flash
                $phone_flash = filter_var($phone_flash, FILTER_VALIDATE_INT);
                if ($phone_flash === '')
                    $phone_flash = null;

                $phone_frontcam = $_POST['phone_frontcam'];   //phone_frontcam
                $phone_frontcam = filter_var($phone_frontcam, FILTER_SANITIZE_STRING);
                if ($phone_frontcam === '')
                    $phone_frontcam = null;

                $phone_os = $_POST['phone_os'];   //phone_os
                $phone_os = filter_var($phone_os, FILTER_SANITIZE_STRING);
                if ($phone_os === '')
                    $phone_os = null;

                $phone_chip = $_POST['phone_chip'];   //phone_chip
                $phone_chip = filter_var($phone_chip, FILTER_SANITIZE_STRING);
                if ($phone_chip === '')
                    $phone_chip = null;

                $phone_ram = $_POST['phone_ram'];   //phone_ram
                $phone_ram = filter_var($phone_ram, FILTER_VALIDATE_INT);
                if ($phone_ram === '')
                    $phone_ram = null;

                $phone_storage = $_POST['phone_storage'];   //phone_storage
                $phone_storage = filter_var($phone_storage, FILTER_VALIDATE_INT);
                if ($phone_storage === '')
                    $phone_storage = null;

                $phone_capacity = $_POST['phone_capacity'];   //phone_capacity
                $phone_capacity = filter_var($phone_capacity, FILTER_VALIDATE_INT);
                if ($phone_capacity === '')
                    $phone_capacity = null;

                $phone_battery = $_POST['phone_battery'];   //phone_battery
                $phone_battery = filter_var($phone_battery, FILTER_SANITIZE_STRING);
                if ($phone_battery === '')
                    $phone_battery = null;

                $phone_battype = $_POST['phone_battype'];   //phone_battype
                $phone_battype = filter_var($phone_battype, FILTER_SANITIZE_STRING);
                if ($phone_battype === '')
                    $phone_battype = null;

                //insert to table smartphone
                $insert_phone = $conn->prepare("INSERT INTO `smartphone` (id_gadget, display_tech, 	resolution, maximun_brightness, rearcam_resolution, flash, frontcam_resolution, operation_sys, chip, ram, storage_capacity, available_capacity, battery_capacity, battery_type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $insert_phone->execute([$gadget_id, $phone_tech, $phone_resolution, $phone_bright, $phone_rearcam, $phone_flash, $phone_frontcam, $phone_os, $phone_chip, $phone_ram, $phone_storage, $phone_capacity, $phone_battery, $phone_battype]);

                //confirm inserting
                $confirm_phone = $conn->prepare("SELECT * FROM `smartphone` WHERE id_gadget = ?");
                $confirm_phone->execute([$gadget_id]);
                if ($confirm_phone->rowCount() > 0) {
                    $message[] = "Inserted successfully";
                } else {
                    $message[] = "Failed to insert";
                }
            } else if ($category === 'smartwatch') {
                $watch_tech = $_POST['watch_tech'];   //watch_tech
                $watch_tech = filter_var($watch_tech, FILTER_SANITIZE_STRING);
                if ($watch_tech === '')
                    $watch_tech = null;

                $watch_screen = $_POST['watch_screen'];   //watch_screen
                $watch_screen = filter_var($watch_screen, FILTER_SANITIZE_STRING);
                if ($watch_screen === '')
                    $watch_screen = null;

                $watch_resolution = $_POST['watch_resolution'];   //watch_resolution
                $watch_resolution = filter_var($watch_resolution, FILTER_SANITIZE_STRING);
                if ($watch_resolution === '')
                    $watch_resolution = null;

                $watch_facemat = $_POST['watch_facemat'];   //watch_facemat
                $watch_facemat = filter_var($watch_facemat, FILTER_SANITIZE_STRING);
                if ($watch_facemat === '')
                    $watch_facemat = null;

                $watch_framemat = $_POST['watch_framemat'];   //watch_framemat
                $watch_framemat = filter_var($watch_framemat, FILTER_SANITIZE_STRING);
                if ($watch_framemat === '')
                    $watch_framemat = null;

                $watch_batlife = $_POST['watch_batlife'];   //watch_batlife
                $watch_batlife = filter_var($watch_batlife, FILTER_VALIDATE_INT);
                if ($watch_batlife === '')
                    $watch_batlife = null;

                $watch_charging = $_POST['watch_charging'];   //watch_charging
                $watch_charging = filter_var($watch_charging, FILTER_VALIDATE_INT);
                if ($watch_charging === '')
                    $watch_charging = null;

                $watch_batcapa = $_POST['watch_batcapa'];   //watch_batcapa
                $watch_batcapa = filter_var($watch_batcapa, FILTER_VALIDATE_INT);
                if ($watch_batcapa === '')
                    $watch_batcapa = null;

                $watch_brand = $_POST['watch_brand'];   //watch_brand
                $watch_brand = filter_var($watch_brand, FILTER_SANITIZE_STRING);
                if ($watch_brand === '')
                    $watch_brand = null;

                //insert to table smartwatch
                $insert_watch = $conn->prepare("INSERT INTO `smartwatch` (id_gadget, display_tech, 	screen_size, resolution, face_material, frame_material, battery_life, charging_time, battery_capacity, brand) VALUES (?,?,?,?,?,?,?,?,?,?)");
                $insert_watch->execute([$gadget_id, $watch_tech, $watch_screen, $watch_resolution, $watch_facemat, $watch_framemat, $watch_batlife, $watch_charging, $watch_batcapa, $watch_brand]);

                //confirm inserting
                $confirm_watch = $conn->prepare("SELECT * FROM `smartwatch` WHERE id_gadget = ?");
                $confirm_watch->execute([$gadget_id]);
                if ($confirm_watch->rowCount() > 0) {
                    $message[] = "Inserted successfully";
                } else {
                    $message[] = "Failed to insert";
                }
            } else {
                $ac_model = $_POST['ac_model'];   //ac_model
                $ac_model = filter_var($ac_model, FILTER_SANITIZE_STRING);
                if ($ac_model === '')
                    $ac_model = null;

                $ac_func = $_POST['ac_func'];   //ac_func
                $ac_func = filter_var($ac_func, FILTER_SANITIZE_STRING);
                if ($ac_func === '')
                    $ac_func = null;

                $ac_usagetime = $_POST['ac_usagetime'];   //ac_usagetime
                $ac_usagetime = filter_var($ac_usagetime, FILTER_SANITIZE_STRING);
                if ($ac_usagetime === '')
                    $ac_usagetime = null;

                $ac_dimension = $_POST['ac_dimension'];   //ac_dimension
                $ac_dimension = filter_var($ac_dimension, FILTER_SANITIZE_STRING);
                if ($ac_dimension === '')
                    $ac_dimension = null;

                $ac_brand = $_POST['ac_brand'];   //ac_brand
                $ac_brand = filter_var($ac_brand, FILTER_SANITIZE_STRING);
                if ($ac_brand === '')
                    $ac_brand = null;

                //insert to table accessory
                $insert_ac = $conn->prepare("INSERT INTO `accessory` (id_gadget, model, functionality, usage_time, dimension, brand) VALUES (?,?,?,?,?,?)");
                $insert_ac->execute([$gadget_id, $ac_model, $ac_func, $ac_usagetime, $ac_dimension, $ac_brand]);

                //confirm inserting
                $confirm_ac = $conn->prepare("SELECT * FROM `accessory` WHERE id_gadget = ?");
                $confirm_ac->execute([$gadget_id]);
                if ($confirm_ac->rowCount() > 0) {
                    $message[] = "Inserted successfully";
                } else {
                    $message[] = "Failed to insert";
                }
            }

            // header('Location: home.php');
            // exit();
        } else {
            $message[] = "Failed to insert.";
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
    <title>Create Gadget</title>

    <!-- 28/10/2024 -->
    <link rel="icon" href="images/logocart.png" type="image/png">

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/gadget.css">
</head>

<body>
    <!-- starts header -->
    <?php include 'components\header_sim.php' ?>
    <!-- ends header -->

    <!-- section create_new_gadget starts -->
    <section class="gadget-detail-container ">
        <h1>CREATE A NEW GADGET</h1>
        <form action="" id="form-c-gadget" method="POST" enctype="multipart/form-data" class="gadget-detail-container">
            <div class="gadget-main-section">
                <div class="image-upload" style="display: flex; flex-direction: column">
                    <!-- Hiển thị ảnh placeholder ban đầu -->
                    <img id="image-preview" src="images/placeholder.jpg" alt="Image Preview">

                    <!-- Input file để chọn ảnh -->
                    <input name="image" type="file" accept="image/*" onchange="previewImage(event)" required>
                </div>
                <div class="gadget-general">
                    <h1 class="name" style="text-align: end;">General Infomation</h1>
                    <div class="item">
                        <label>Name</label>
                        <input name="name" placeholder="Name" value="" required>
                    </div>
                    <div class="item">
                        <label>Price import</label>
                        <input name="im_price" placeholder="Import Price" max="9999999999" min="0" value=""
                            type="number" onkeydown="return event.keyCode !== 69" required>
                    </div>
                    <div class="item">
                        <label>Price export</label>
                        <input name="ex_price" placeholder="Export Price" max="9999999999" min="0" type="number"
                            onkeydown="return event.keyCode !== 69" required>
                    </div>
                    <div class="item">
                        <label>Description</label>
                        <input name="description" placeholder="Description" maxlength="499" required>
                    </div>
                    <div class="item">
                        <label>Category</label>
                        <select class="gadget-select" name="gadget-select">
                            <option value="laptop">laptop</option>
                            <option value="smartphone">smartphone</option>
                            <option value="smartwatch">smartwatch</option>
                            <option value="accessory">accessory</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="gadget-details div-laptop">
                <label class="details-label">Laptop Information: </label>
                <div class="details-input">
                    <input name="lap_cpu" maxlength="99" placeholder="CPU Technology (Optional)" value="">
                    <input name="lap_core" placeholder="Number of Cores (Optional)" type="number"
                        onkeydown="return event.keyCode !== 69" value="">
                    <input name="lap_thread" placeholder="Number of Threads (Optional)" type="number"
                        onkeydown="return event.keyCode !== 69" value="">
                    <input name="lap_ram" placeholder="RAM (Optional)" type="number"
                        onkeydown="return event.keyCode !== 69" value="">
                    <input name="lap_harddrive" placeholder="Hard Drive (Optional)" type="number"
                        onkeydown="return event.keyCode !== 69" value="">
                    <input name="lap_screen" maxlength="99" placeholder="Screen (Optional)" value="">
                    <input name="lap_resolution" maxlength="99" placeholder="Resolution (Optional)" value="">
                    <input name="lap_refresh" maxlength="99" placeholder="Refresh Rate (Optional)" value="">
                    <input name="lap_dimension" maxlength="99" placeholder="Dimension (Optional)" value="">
                    <input name="lap_weight" placeholder="Weight (Optional)" type="number"
                        onkeydown="return event.keyCode !== 69" value="">
                    <input name="lap_material" maxlength="99" placeholder="Material (Optional)" value="">
                    <input name="lap_date" placeholder="Release Date (Optional)" type="date" value="">
                </div>
            </div>

            <div class="gadget-details div-smartphone hidden">
                <label class="details-label">Smartphone Information</label>
                <div class="details-input">
                    <input name="phone_tech" maxlength="99" placeholder="Display Technology (Optional)" value="">
                    <input name="phone_resolution" maxlength="99" placeholder="Resolution (Optional)" value="">
                    <input name="phone_bright" maxlength="99" placeholder="Maximum brightness (Optional)" value="">
                    <input name="phone_rearcam" maxlength="99" placeholder="Rearcam Resolution (Optional)" value="">
                    <input name="phone_flash" placeholder="Number of Flash (Optional)" type="numbers"
                        onkeydown="return event.keyCode !== 69" value="">
                    <input name="phone_frontcam" maxlength="99" placeholder="Frontcam Resolution (Optional)" value="">
                    <input name="phone_os" maxlength="99" placeholder="Operation System (Optional)" value="">
                    <input name="phone_chip" maxlength="99" placeholder="Chip (Optional)" value="">
                    <input name="phone_ram" placeholder="RAM (Optional)" type="number"
                        onkeydown="return event.keyCode !== 69" value="">
                    <input name="phone_storage" placeholder="Storage Capacity (Optional)" type="number"
                        onkeydown="return event.keyCode !== 69" value="">
                    <input name="phone_capacity" placeholder="Available Capacity (Optional)" type="number"
                        onkeydown="return event.keyCode !== 69" value="">
                    <input name="phone_battery" maxlength="99" placeholder="Battery Capacity (Optional)" value="">
                    <input name="phone_battype" maxlength="99" placeholder="Battery Type (Optional)" value="">
                </div>
            </div>

            <div class="gadget-details div-smartwatch hidden">
                <label class="details-label">Smartwatch Information</label>
                <div class="details-input">
                    <input name="watch_tech" maxlength="99" placeholder="Display Technology (Optional)" value="">
                    <input name="watch_screen" maxlength="99" placeholder="Screen Size (Optional)" value="">
                    <input name="watch_resolution" maxlength="99" placeholder="Resolution (Optional)" value="">
                    <input name="watch_facemat" maxlength="99" placeholder="Face Material (Optional)" value="">
                    <input name="watch_framemat" maxlength="99" placeholder="Frame Material (Optional)" value="">
                    <input name="watch_batlife" placeholder="Battery Life (Optional)" type="number"
                        onkeydown="return event.keyCode !== 69" value="">
                    <input name="watch_charging" placeholder="Charging Time (Optional)" type="number"
                        onkeydown="return event.keyCode !== 69" value="">
                    <input name="watch_batcapa" placeholder="Battery Capacity (Optional)" type="number"
                        onkeydown="return event.keyCode !== 69" value="">
                    <input name="watch_brand" maxlength="99" placeholder="Brand (Optional)" value="">
                </div>
            </div>

            <div class="gadget-details div-accessory hidden">
                <label class="details-label">Accessory Information</label>
                <div class="details-input">
                    <input name="ac_model" maxlength="99" placeholder="Model (Optional)" value="">
                    <input name="ac_func" maxlength="99" placeholder="Functionality (Optional)" value="">
                    <input name="ac_usagetime" placeholder="Usage Time (Optional)" type="number"
                        onkeydown="return event.keyCode !== 69" value="">
                    <input name="ac_dimension" maxlength="99" placeholder="Dimension (Optional)" value="">
                    <input name="ac_brand" placeholder="Brand (Optional)" value="">
                </div>
            </div>

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

    <script>
    // Hàm để cập nhật ảnh preview khi chọn tệp
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('image-preview');
            preview.src = reader.result;
        };
        // Đọc tệp được chọn
        reader.readAsDataURL(event.target.files[0]);
    }
    </script>
    <script src="js/index.js"></script>
</body>

</html>

<!-- onkeydown="return event.keyCode !== 69" -->

<!-- neu k filter + sani -> <script>alert('Hacked!');</script> -->