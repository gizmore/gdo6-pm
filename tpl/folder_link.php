<?php
use GDO\Template\GDO_Template;
use GDO\UI\GDO_Link;
$field instanceof GDO_Template;
$gdo = $field->gdo;
?>
<?= GDO_Link::make()->label($gdo->getName())->href(href('PM', 'Overview', '&folder='.$gdo->getID())); ?>