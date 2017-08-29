<?php
use GDO\PM\PM;
use GDO\Template\GDT_Template;
use GDO\UI\GDT_Icon;
$field instanceof GDT_Template;
$pm = $field->gdo;
$pm instanceof PM;
?>
<?php if (!$pm->isRead()) : ?>
<?= GDT_Icon::iconS('notifications_active'); ?>
<?php endif; ?>

