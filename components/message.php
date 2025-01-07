<?php
if (isset($message)) {
    foreach ($message as $message) {
        echo '
        <div class="message">
        <span>' . $message . '</span>
        <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>';
    }
}
?>

<script>
    // Tự động xóa thông báo sau 2 giây
    setTimeout(() => {
        document.querySelectorAll('.message').forEach(message => {
            message.remove();
        });
    }, 2000);
</script>
