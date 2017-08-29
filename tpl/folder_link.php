<?php
use GDO\Template\GDT_Template;
use GDO\UI\GDT_Link;
$field instanceof GDT_Template;
$gdo = $field->gdo;
?>
<?= GDT_Link::make()->label($gdo->getName())->href(href('PM', 'Overview', '&folder='.$gdo->getID())); ?>