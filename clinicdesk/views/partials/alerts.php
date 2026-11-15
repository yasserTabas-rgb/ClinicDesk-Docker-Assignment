<?php if (!empty($_SESSION['flash'])): ?>
    <?php
    $flash = $_SESSION['flash'];
    $type  = $flash['type'];   // success | danger | warning | info
    $msg   = $flash['message'];
    unset($_SESSION['flash']); // show once only
    ?>
    <div class="alert alert-<?= e($type) ?> alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
        <?= e($msg) ?>
    </div>
<?php endif; ?>