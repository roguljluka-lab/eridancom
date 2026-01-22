<?php if(isset($_SESSION['dc_success'])) : ?>
    <div class="dc-alert dc-alert-info">
        <?php echo $_SESSION['dc_success']; ?>
    </div>
    <?php unset($_SESSION['dc_success']); endif; ?>

<?php if(isset($_SESSION['dc_danger'])) : ?>
    <div class="dc-alert dc-alert-danger">
        <?php echo $_SESSION['dc_danger']; ?>
    </div>
    <?php unset($_SESSION['dc_danger']); endif; ?>

<?php if(isset($_SESSION['dc_warning'])) : ?>
    <div class="dc-alert dc-alert-warning">
        <?php echo $_SESSION['dc_warning']; ?>
    </div>
    <?php unset($_SESSION['dc_warning']); endif; ?>
