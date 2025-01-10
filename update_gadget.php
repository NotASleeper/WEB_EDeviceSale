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



if (isset($_GET['id'])) {
    $gadget_id = intval($_GET['id']);

    $select_gadget = $conn->prepare("SELECT * FROM `gadget` WHERE id_gadget = ?");
    $select_gadget->execute([$gadget_id]);
    if ($select_gadget->rowCount() == 0) {
        header('Location: home.php');
        exit();
    }
} else {
    header('Location: home.php');
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

    //check if already exists
    $select_gadget = $conn->prepare("SELECT * FROM `gadget` WHERE id_gadget = ?");
    $select_gadget->execute([$gadget_id]);
    $row = $select_gadget->fetch(PDO::FETCH_ASSOC);
    //if existed
    if ($select_gadget->rowCount() == 0) {
        $message[] = "Gadget does not exist!";
        exit();
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $old_img = $row['pic_gadget'];
            $target_dir = 'images/img_gadget/';
            $old_file_path = $target_dir . $old_img;

            // An image has been uploaded, proceed with handling the upload
            $file_name = $_FILES['image']['name'];
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

            $new_file_name = time() . '.' . $file_ext;
            $new_file_path = $target_dir . $new_file_name;

            if (copy($file_tmp, $new_file_path)) {
                //db
                if (file_exists($old_file_path)) {
                    if (unlink($old_file_path)) {
                    }
                }
                $update_gadget = $conn->prepare("UPDATE `gadget` SET pic_gadget = ? WHERE id_gadget = ?");
                $update_gadget->execute([$new_file_name, $gadget_id]);
            } else {
                // Handle the error if the file could not be uploaded
                $message[] = "Failed to upload the image.";
                exit();
            }
        }

        $category = $row['category'];

        //update db gadget
        $update_gadget = $conn->prepare("UPDATE `gadget` SET name_gadget = ?, imp_gadget = ?, exp_gadget = ?, des_gadget = ? WHERE id_gadget = ?");
        $update_gadget->execute([$name, $im_price, $ex_price, $description, $gadget_id]);

        //update in detailed db
        $confirm_gadget = $conn->prepare("SELECT * FROM `gadget` WHERE id_gadget = ?");
        $confirm_gadget->execute([$gadget_id]);
        $row = $confirm_gadget->fetch(PDO::FETCH_ASSOC);
        if ($confirm_gadget->rowCount() > 0) {

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
                $lap_weight = filter_var($lap_weight, FILTER_SANITIZE_NUMBER_FLOAT);
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

                //update to table laptop
                $update_lap = $conn->prepare("UPDATE `laptop` 
                SET cpu_tech = ?, 
                    num_of_core = ?, 
                    num_of_thread = ?, 
                    ram = ?, 
                    hard_drive = ?, 
                    screen = ?, 
                    resolution = ?, 
                    refresh_rate = ?, 
                    dimension = ?, 
                    weight = ?, 
                    material = ?, 
                    release_date = ? 
                WHERE id_gadget = ?");
                $update_lap->execute([$lap_cpu, $lap_core, $lap_thread, $lap_ram, $lap_harddrive, $lap_screen, $lap_resolution, $lap_refresh, $lap_dimension, $lap_weight, $lap_material, $lap_date, $gadget_id]);
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

                $update_phone = $conn->prepare("UPDATE `smartphone` 
                    SET display_tech = ?, 
                        resolution = ?, 
                        maximun_brightness = ?, 
                        rearcam_resolution = ?, 
                        flash = ?, 
                        frontcam_resolution = ?, 
                        operation_sys = ?, 
                        chip = ?, 
                        ram = ?, 
                        storage_capacity = ?, 
                        available_capacity = ?, 
                        battery_capacity = ?, 
                        battery_type = ? 
                    WHERE id_gadget = ?");
                $update_phone->execute([$phone_tech, $phone_resolution, $phone_bright, $phone_rearcam, $phone_flash, $phone_frontcam, $phone_os, $phone_chip, $phone_ram, $phone_storage, $phone_capacity, $phone_battery, $phone_battype, $gadget_id]);
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

                $update_watch = $conn->prepare("UPDATE `smartwatch` 
                    SET display_tech = ?, 
                        screen_size = ?, 
                        resolution = ?, 
                        face_material = ?, 
                        frame_material = ?, 
                        battery_life = ?, 
                        charging_time = ?, 
                        battery_capacity = ?, 
                        brand = ? 
                    WHERE id_gadget = ?");
                $update_watch->execute([$watch_tech, $watch_screen, $watch_resolution, $watch_facemat, $watch_framemat, $watch_batlife, $watch_charging, $watch_batcapa, $watch_brand, $gadget_id]);
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

                $update_ac = $conn->prepare("UPDATE `accessory` 
                    SET model = ?, 
                        functionality = ?, 
                        usage_time = ?, 
                        dimension = ?, 
                        brand = ? 
                    WHERE id_gadget = ?");
                $update_ac->execute([$ac_model, $ac_func, $ac_usagetime, $ac_dimension, $ac_brand, $gadget_id]);
            }

            // header('Location: home.php');
            // exit();

            //confirm
            $message[] = "Updated successfully";
        } else {
            $message[] = "Failed to Update";
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
    <title>Update Gadget</title>

    <!-- 28/10/2024 -->
    <link rel="icon" href="images/logocart.png" type="image/png">

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/gadget.css">
    <link rel="stylesheet" href="css/header_footer.css">

</head>
<!-- starts header -->
<?php include 'components\header_sim.php' ?>
<!-- ends header -->

<!-- section create_new_gadget starts -->
<section class="gadget-detail-container ">
    <h1>UPDATE GADGET</h1>
    <?php
    $select_gadget = $conn->prepare("SELECT * FROM `gadget` WHERE id_gadget = ?");
    $select_gadget->execute([$gadget_id]);
    if ($fetch_gadget = $select_gadget->fetch(PDO::FETCH_ASSOC)) {
    ?>
        <form action="" id="form-c-gadget" method="POST" enctype="multipart/form-data" class="gadget-detail-container">
            <div class="gadget-main-section">
                <div class="image-upload" style="display: flex; flex-direction: column">
                    <!-- Hiển thị ảnh placeholder ban đầu -->
                    <img id="image-preview"
                        src="images/img_gadget/<?php echo htmlspecialchars($fetch_gadget['pic_gadget'] ?? 'default.jpg'); ?>"
                        alt="Image Preview">

                    <!-- Input file để chọn ảnh -->
                    <input name="image" type="file" accept="image/*" onchange="previewImage(event)">
                </div>
                <div class="gadget-general">
                    <h1 class="name" style="text-align: end;">General Infomation</h1>
                    <div class="item">
                        <label>Name</label>
                        <input name="name" placeholder="Name" value="<?= $fetch_gadget['name_gadget']; ?>" required>
                    </div>
                    <div class="item">
                        <label>Price import</label>
                        <input name="im_price" placeholder="Import Price" max="9999999999" min="0"
                            value="<?= $fetch_gadget['imp_gadget']; ?>" type="number"
                            onkeydown="return event.keyCode !== 69" required>
                    </div>
                    <div class="item">
                        <label>Price export</label>
                        <input name="ex_price" placeholder="Export Price" max="9999999999" min="0"
                            value="<?= $fetch_gadget['exp_gadget']; ?>" type="number"
                            onkeydown="return event.keyCode !== 69" required>
                    </div>
                    <div class="item">
                        <label>Description</label>
                        <input name="description" placeholder="Description" value="<?= $fetch_gadget['des_gadget']; ?>"
                            maxlength="499" required>
                    </div>
                    <div class="item">
                        <label>Category</label>
                        <!-- <select class="gadget-select" name="gadget-select">
                        <option value="laptop">laptop</option>
                        <option value="smartphone">smartphone</option>
                        <option value="smartwatch">smartwatch</option>
                        <option value="accessory">accessory</option>
                    </select> -->
                        <input style="padding-right: 1rem;" value="<?= $fetch_gadget['category']; ?>" readonly />
                    </div>
                </div>
            </div>
            <?php
            if ($fetch_gadget['category'] === 'laptop') {
                $select_lap = $conn->prepare("SELECT * FROM `laptop` WHERE id_gadget = ?");
                $select_lap->execute([$gadget_id]);
                if ($fetch_lap = $select_lap->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <div class="gadget-details div-laptop">
                        <label class="details-label">Laptop Information: </label>
                        <div class="details-input">
                            <input name="lap_cpu" maxlength="99" placeholder="CPU Technology (Optional)"
                                value="<?= $fetch_lap['cpu_tech'] ?>">
                            <input name="lap_core" placeholder="Number of Cores (Optional)" type="number"
                                onkeydown="return event.keyCode !== 69" value="<?= $fetch_lap['num_of_core'] ?>">
                            <input name="lap_thread" placeholder="Number of Threads (Optional)" type="number"
                                onkeydown="return event.keyCode !== 69" value="<?= $fetch_lap['num_of_thread'] ?>">
                            <input name="lap_ram" placeholder="RAM (Optional)" type="number" onkeydown="return event.keyCode !== 69"
                                value="<?= $fetch_lap['ram'] ?>">
                            <input name="lap_harddrive" placeholder="Hard Drive (Optional)" type="number"
                                onkeydown="return event.keyCode !== 69" value="<?= $fetch_lap['hard_drive'] ?>">
                            <input name="lap_screen" maxlength="99" placeholder="Screen (Optional)"
                                value="<?= $fetch_lap['screen'] ?>">
                            <input name="lap_resolution" maxlength="99" placeholder="Resolution (Optional)"
                                value="<?= $fetch_lap['resolution'] ?>">
                            <input name="lap_refresh" maxlength="99" placeholder="Refresh Rate (Optional)"
                                value="<?= $fetch_lap['refresh_rate'] ?>">
                            <input name="lap_dimension" maxlength="99" placeholder="Dimension (Optional)"
                                value="<?= $fetch_lap['dimension'] ?>">
                            <input name="lap_weight" placeholder="Weight (Optional)" type="number"
                                onkeydown="return event.keyCode !== 69" value="<?= $fetch_lap['weight'] ?>">
                            <input name="lap_material" maxlength="99" placeholder="Material (Optional)"
                                value="<?= $fetch_lap['material'] ?>">
                            <input name="lap_date" placeholder="Release Date (Optional)" type="date"
                                value="<?= $fetch_lap['release_date'] ?>">
                        </div>
                    </div>
                <?php
                }
            } else if ($fetch_gadget['category'] === 'smartphone') {
                $select_phone = $conn->prepare("SELECT * FROM `smartphone` WHERE id_gadget = ?");
                $select_phone->execute([$gadget_id]);
                if ($fetch_phone = $select_phone->fetch(PDO::FETCH_ASSOC)) {
                ?>
                    <div class="gadget-details div-smartphone">
                        <label class="details-label">Smartphone Information</label>
                        <div class="details-input">
                            <input name="phone_tech" maxlength="99" placeholder="Display Technology (Optional)"
                                value="<?= $fetch_phone['display_tech'] ?>">
                            <input name="phone_resolution" maxlength="99" placeholder="Resolution (Optional)"
                                value="<?= $fetch_phone['resolution'] ?>">
                            <input name="phone_bright" maxlength="99" placeholder="Maximum brightness (Optional)"
                                value="<?= $fetch_phone['maximun_brightness'] ?>">
                            <input name="phone_rearcam" maxlength="99" placeholder="Rearcam Resolution (Optional)"
                                value="<?= $fetch_phone['rearcam_resolution'] ?>">
                            <input name="phone_flash" placeholder="Number of Flash (Optional)" type="numbers"
                                onkeydown="return event.keyCode !== 69" value="<?= $fetch_phone['flash'] ?>">
                            <input name="phone_frontcam" maxlength="99" placeholder="Frontcam Resolution (Optional)"
                                value="<?= $fetch_phone['frontcam_resolution'] ?>">
                            <input name="phone_os" maxlength="99" placeholder="Operation System (Optional)"
                                value="<?= $fetch_phone['operation_sys'] ?>">
                            <input name="phone_chip" maxlength="99" placeholder="Chip (Optional)"
                                value="<?= $fetch_phone['chip'] ?>">
                            <input name="phone_ram" placeholder="RAM (Optional)" type="number"
                                onkeydown="return event.keyCode !== 69" value="<?= $fetch_phone['ram'] ?>">
                            <input name="phone_storage" placeholder="Storage Capacity (Optional)" type="number"
                                onkeydown="return event.keyCode !== 69" value="<?= $fetch_phone['storage_capacity'] ?>">
                            <input name="phone_capacity" placeholder="Available Capacity (Optional)" type="number"
                                onkeydown="return event.keyCode !== 69" value="<?= $fetch_phone['available_capacity'] ?>">
                            <input name="phone_battery" maxlength="99" placeholder="Battery Capacity (Optional)"
                                value="<?= $fetch_phone['battery_capacity'] ?>">
                            <input name="phone_battype" maxlength="99" placeholder="Battery Type (Optional)"
                                value="<?= $fetch_phone['battery_type'] ?>">
                        </div>
                    </div>
                <?php
                }
            } else if ($fetch_gadget['category'] === 'smartwatch') {
                $select_watch = $conn->prepare("SELECT * FROM `smartwatch` WHERE id_gadget = ?");
                $select_watch->execute([$gadget_id]);
                if ($fetch_watch = $select_watch->fetch(PDO::FETCH_ASSOC)) {
                ?>
                    <div class="gadget-details div-smartwatch">
                        <label class="details-label">Smartwatch Information</label>
                        <div class="details-input">
                            <input name="watch_tech" maxlength="99" placeholder="Display Technology (Optional)"
                                value="<?= $fetch_watch['display_tech'] ?>">
                            <input name="watch_screen" maxlength="99" placeholder="Screen Size (Optional)"
                                value="<?= $fetch_watch['screen_size'] ?>">
                            <input name="watch_resolution" maxlength="99" placeholder="Resolution (Optional)"
                                value="<?= $fetch_watch['resolution'] ?>">
                            <input name="watch_facemat" maxlength="99" placeholder="Face Material (Optional)"
                                value="<?= $fetch_watch['face_material'] ?>">
                            <input name="watch_framemat" maxlength="99" placeholder="Frame Material (Optional)"
                                value="<?= $fetch_watch['frame_material'] ?>">
                            <input name="watch_batlife" placeholder="Battery Life (Optional)" type="number"
                                onkeydown="return event.keyCode !== 69" value="<?= $fetch_watch['battery_life'] ?>">
                            <input name="watch_charging" placeholder="Charging Time (Optional)" type="number"
                                onkeydown="return event.keyCode !== 69" value="<?= $fetch_watch['charging_time'] ?>">
                            <input name="watch_batcapa" placeholder="Battery Capacity (Optional)" type="number"
                                onkeydown="return event.keyCode !== 69" value="<?= $fetch_watch['battery_capacity'] ?>">
                            <input name="watch_brand" maxlength="99" placeholder="Brand (Optional)"
                                value="<?= $fetch_watch['brand'] ?>">
                        </div>
                    </div>
                <?php
                }
            } else {
                $select_ac = $conn->prepare("SELECT * FROM `accessory` WHERE id_gadget = ?");
                $select_ac->execute([$gadget_id]);
                if ($fetch_ac = $select_ac->fetch(PDO::FETCH_ASSOC)) {
                ?>
                    <div class="gadget-details div-accessory">
                        <label class="details-label">Accessory Information</label>
                        <div class="details-input">
                            <input name="ac_model" maxlength="99" placeholder="Model (Optional)" value="<?= $fetch_ac['model'] ?>">
                            <input name="ac_func" maxlength="99" placeholder="Functionality (Optional)"
                                value="<?= $fetch_ac['functionality'] ?>">
                            <input name="ac_usagetime" placeholder="Usage Time (Optional)" type="number"
                                onkeydown="return event.keyCode !== 69" value="<?= $fetch_ac['usage_time'] ?>">
                            <input name="ac_dimension" maxlength="99" placeholder="Dimension (Optional)"
                                value="<?= $fetch_ac['dimension'] ?>">
                            <input name="ac_brand" placeholder="Brand (Optional)" value="<?= $fetch_ac['brand'] ?>">
                        </div>
                    </div>
            <?php
                }
            }
            ?>
            <div class="gadget-buttons">
                <button type="submit" name="submit" class="btn-success">Update</button>
                <button class="btn-second-green addgg-clear">Clear</button>
            </div>
        <?php
    }
        ?>
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