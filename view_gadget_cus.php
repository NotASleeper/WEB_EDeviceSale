<?php
include 'components/connect.php';

session_start();

// not sure 
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';

    // //pls un-cmt this when done
    // header('location:login.php');
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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Gadget</title>
    <link rel="icon" href="images/logocart.png" type="image/png">

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- starts header -->
    <?php include 'components\cus_header.php' ?>
    <!-- ends header -->

    <!-- section view_gadget starts -->
    <section class="view-gadget">
        <?php
        $select_gadget = $conn->prepare("SELECT * FROM `gadget` WHERE id_gadget = ?");
        $select_gadget->execute([$gadget_id]);
        if ($fetch_gadget = $select_gadget->fetch(PDO::FETCH_ASSOC)) {
        ?>
            <h1 style="color: yellow">GADGET INFORMATION</h1>
            <table class="tbl-general">
                <thead>
                    <tr>
                        <th class="header" colspan="2">General Information</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-left">Gadget ID:</td>
                        <td class="text-right"><?= $fetch_gadget['id_gadget']; ?></td>
                    </tr>
                    <tr>
                        <td class="text-left">Gadget Name:</td>
                        <td class="text-right"><?= $fetch_gadget['name_gadget']; ?></td>
                    </tr>
                    <tr>
                        <td class="text-left">Category:</td>
                        <td class="text-right"><?= $fetch_gadget['category']; ?></td>
                    </tr>
                    <tr>
                        <td class="text-left">Import Price:</td>
                        <td class="text-right"><?= number_format($fetch_gadget['imp_gadget'], 0, '.', ','); ?></td>
                    </tr>
                    <tr>
                        <td class="text-left">Export Price:</td>
                        <td class="text-right"><?= number_format($fetch_gadget['exp_gadget'], 0, '.', ','); ?></td>
                    </tr>
                    <tr>
                        <td class="text-left">Quantity:</td>
                        <td class="text-right"><?= number_format($fetch_gadget['quantity'], 0, '.', ','); ?></td>
                    </tr>
                    <tr>
                        <td class="text-left">Description:</td>
                        <td class="text-right"><?= $fetch_gadget['des_gadget']; ?></td>
                    </tr>
                    <tr>
                        <td class="text-left">Picture:</td>
                        <td class="text-right"><img src="images/img_gadget/<?= $fetch_gadget['pic_gadget'] ?>" height="120px"></td>
                    </tr>
                </tbody>
            </table>
            <?php
            if ($fetch_gadget['category'] === 'laptop') {
                $select_lap = $conn->prepare("SELECT * FROM `laptop` WHERE id_gadget = ?");
                $select_lap->execute([$gadget_id]);
                if ($fetch_lap = $select_lap->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <!-- laptop -->
                    <table class="tbl-detail tbl-laptop">
                        <thead>
                            <tr>
                                <th class="header" colspan="2">Laptop Information</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-left">CPU Technology:</td>
                                <td class="text-right"><?= $fetch_lap['cpu_tech'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Number of Cores:</td>
                                <td class="text-right"><?= $fetch_lap['num_of_core'] ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Number of Threads:</td>
                                <td class="text-right"><?= $fetch_lap['num_of_thread'] ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">RAM:</td>
                                <td class="text-right"><?= $fetch_lap['ram'] ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Hard Drive Capability:</td>
                                <td class="text-right"><?= $fetch_lap['hard_drive'] ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Screen:</td>
                                <td class="text-right"><?= $fetch_lap['screen'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Resolution:</td>
                                <td class="text-right"><?= $fetch_lap['resolution'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Refresh Rate:</td>
                                <td class="text-right"><?= $fetch_lap['refresh_rate'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Dimension:</td>
                                <td class="text-right"><?= $fetch_lap['dimension'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Weight:</td>
                                <td class="text-right"><?= $fetch_lap['weight'] ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Material:</td>
                                <td class="text-right"><?= $fetch_lap['material'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Release Date:</td>
                                <td class="text-right"><?= date('m-d-Y', strtotime($fetch_lap['release_date'])) ?? 'Unknown' ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- laptop  -->
                <?php
                }
            } else if ($fetch_gadget['category'] === 'smartphone') {
                $select_phone = $conn->prepare("SELECT * FROM `smartphone` WHERE id_gadget = ?");
                $select_phone->execute([$gadget_id]);
                if ($fetch_phone = $select_phone->fetch(PDO::FETCH_ASSOC)) {
                ?>
                    <!-- smartphone -->
                    <table class="tbl-detail tbl-smartphone">
                        <thead>
                            <tr>
                                <th class="header" colspan="2">Smartphone Information</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-left">Display Technology:</td>
                                <td class="text-right"><?= $fetch_phone['display_tech'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Resolution:</td>
                                <td class="text-right"><?= $fetch_phone['resolution'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Maximum Brightness:</td>
                                <td class="text-right"><?= $fetch_phone['maximun_brightness'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Rearcam Resolution:</td>
                                <td class="text-right"><?= $fetch_phone['rearcam_resolution'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Number of Flash:</td>
                                <td class="text-right"><?= $fetch_phone['flash'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Frontcam Resolution:</td>
                                <td class="text-right"><?= $fetch_phone['frontcam_resolution'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Operation System:</td>
                                <td class="text-right"><?= $fetch_phone['operation_sys'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Chip:</td>
                                <td class="text-right"><?= $fetch_phone['chip'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">RAM:</td>
                                <td class="text-right"><?= $fetch_phone['ram'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Storage Capability:</td>
                                <td class="text-right"><?= number_format($fetch_phone['storage_capacity'], 0, '.', ','); ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Available Capability:</td>
                                <td class="text-right"><?= number_format($fetch_phone['available_capacity'], 0, '.', ','); ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Battery Capability:</td>
                                <td class="text-right"><?= $fetch_phone['battery_capacity'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Battery Type:</td>
                                <td class="text-right"><?= $fetch_phone['battery_type'] ?? 'Unknown' ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- smartphone -->
                <?php
                }
            } else if ($fetch_gadget['category'] === 'smartwatch') {
                $select_watch = $conn->prepare("SELECT * FROM `smartwatch` WHERE id_gadget = ?");
                $select_watch->execute([$gadget_id]);
                if ($fetch_watch = $select_watch->fetch(PDO::FETCH_ASSOC)) {
                ?>
                    <!-- smartwatch -->
                    <table class="tbl-detail tbl-smartwatch">
                        <thead>
                            <tr>
                                <th class="header" colspan="2">Smartwatch Information</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-left">Display Technology:</td>
                                <td class="text-right"><?= $fetch_watch['display_tech'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Screen Size:</td>
                                <td class="text-right"><?= $fetch_watch['screen_size'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Resolution:</td>
                                <td class="text-right"><?= $fetch_watch['resolution'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Face Material:</td>
                                <td class="text-right"><?= $fetch_watch['face_material'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Frame Material:</td>
                                <td class="text-right"><?= $fetch_watch['frame_material'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Battery Life:</td>
                                <td class="text-right"><?= $fetch_watch['battery_life'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Charging Time:</td>
                                <td class="text-right"><?= $fetch_watch['charging_time'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Battery Capacity:</td>
                                <td class="text-right"><?= $fetch_watch['battery_capacity'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Brand:</td>
                                <td class="text-right"><?= $fetch_watch['brand'] ?? 'Unknown' ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- smartwatch -->
                <?php }
            } else {
                $select_ac = $conn->prepare("SELECT * FROM `accessory` WHERE id_gadget = ?");
                $select_ac->execute([$gadget_id]);
                if ($fetch_ac = $select_ac->fetch(PDO::FETCH_ASSOC)) {
                ?>
                    <!-- accessory -->
                    <table class="tbl-detail tbl-accessory">
                        <thead>
                            <tr>
                                <th class="header" colspan="2">Accessory Information</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-left">Model:</td>
                                <td class="text-right"><?= $fetch_ac['model'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Functionality:</td>
                                <td class="text-right"><?= $fetch_ac['functionality'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Usage Time:</td>
                                <td class="text-right"><?= $fetch_ac['usage_time'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Dimension:</td>
                                <td class="text-right"><?= $fetch_ac['dimension'] ?? 'Unknown' ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Brand:</td>
                                <td class="text-right"><?= $fetch_ac['brand'] ?? 'Unknown' ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- accessory -->
        <?php
                }
            }
        }
        ?>

        <button name="back" class="btn-success" onclick="window.location.href='home_cus.php'">Back</button>

    </section>
    <!-- section view_gadget ends -->

    <!-- starts footer -->
    <?php include 'components\footer.php' ?>
    <!-- ends footer -->

    <script src=" js/index.js"></script>
</body>

</html>