<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

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
        <form id="form-c-gadget">
            <input placeholder="Name" required>
            <input placeholder="Import Price" required>
            <input placeholder="Export Price" required>
            <input placeholder="Description" required>
            <input placeholder="Export Price" type="file" accept="image/*" required>
            <div class="div-container">
                <label style="color: white;">Category</label>
                <select class="gadget-select">
                    <option value="laptop">laptop</option>
                    <option value="smartphone">smartphone</option>
                    <option value="smartwatch">smartwatch</option>
                    <option value="accessory">accessory</option>
                </select>
            </div>
            <div class="div-con-type div-laptop">
                <label style="color: yellow;">Laptop Information</label>
                <input placeholder="CPU Technology" required>
                <input placeholder="Number of Cores" type="number" required>
                <input placeholder="Number of Threads" type="number" required>
                <input placeholder="RAM" type="number" required>
                <input placeholder="Hard Drive" type="number" required>
                <input placeholder="Screen" required>
                <input placeholder="Resolution" required>
                <input placeholder="Refresh Rate" required>
                <input placeholder="Dimension" required>
                <input placeholder="Weight" type="number" required>
                <input placeholder="Material" required>
                <input placeholder="Release Date" type="date" required>
            </div>

            <div class="div-con-type div-smartphone hidden">
                <label style="color: yellow;">Smartphone Information</label>
                <input placeholder="Display Technology" required>
                <input placeholder="Resolution" required>
                <input placeholder="Maximum brightness" required>
                <input placeholder="Rearcam Resolution" required>
                <input placeholder="Number of Flash" type="numbers" required>
                <input placeholder="Frontcam Resolution" required>
                <input placeholder="Operation System" required>
                <input placeholder="Chip" required>
                <input placeholder="RAM" type="number" required>
                <input placeholder="Storage Capacity" type="number" required>
                <input placeholder="Available Capacity" type="number" required>
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
                <input placeholder="Battery Life" type="number" required>
                <input placeholder="Charging Time" type="number" required>
                <input placeholder="Battery Capacity" type="number" required>
                <input placeholder="Brand" required>
            </div>

            <div class="div-con-type div-accessory hidden">
                <label style="color: yellow;">Accessory Information</label>
                <input placeholder="Model" required>
                <input placeholder="Functionality" required>
                <input placeholder="Usage Time" type="number" required>
                <input placeholder="Dimension" required>
                <input placeholder="Brand" required>
            </div>

            <div class="gadget-buttons">
                <button type="submit" class="btn-success">Add</button>
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