<?php if(isset($_SESSION['dc_admin_success'])) : ?>
    <div class="notice notice-success is-dismissible">
        <p><?php echo $_SESSION['dc_admin_success']; ?></p>
    </div>
    <?php unset($_SESSION['dc_admin_success']); endif; ?>

<?php if(isset($_SESSION['dc_admin_danger'])) : ?>
    <div class="notice notice-error is-dismissible">
        <p><?php echo $_SESSION['dc_admin_danger']; ?></p>
    </div>
    <?php unset($_SESSION['dc_admin_danger']); endif; ?>

<?php if(isset($_SESSION['dc_admin_warning'])) : ?>
    <div class="notice notice-warning is-dismissible">
        <p><?php echo $_SESSION['dc_admin_warning']; ?></p>
    </div>
    <?php unset($_SESSION['dc_admin_warning']); endif; ?>
