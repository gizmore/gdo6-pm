<?php
use GDO\PM\GDO_PM;
use GDO\Template\GDT_Template;
use GDO\UI\GDT_Icon;
$field instanceof GDT_Template;
$pm = $field->gdo;
$pm instanceof GDO_PM;
?>
<?php if (!$pm->isRead()) : ?>
<?= GDT_Icon::iconS('notifications_active'); ?>
<?php endif; ?>

