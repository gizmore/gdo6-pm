<?php
use GDO\PM\PM;
use GDO\Template\GDO_Template;
use GDO\UI\GDO_Icon;
$field instanceof GDO_Template;
$pm = $field->gdo;
$pm instanceof PM;
?>
<?php if (!$pm->isRead()) : ?>
<?= GDO_Icon::iconS('notifications_active'); ?>
<?php endif; ?>

