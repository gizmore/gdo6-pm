<?php
use GDO\Template\GDO_Bar;
use GDO\UI\GDO_Link;

$navbar = new GDO_Bar();
$navbar->addFields(array(
	GDO_Link::make('link_overview')->href(href('PM', 'Overview'))->icon('storage'),
	GDO_Link::make('link_settings')->href(href('Account', 'Settings', '&module=PM'))->icon('settings'),
	GDO_Link::make('link_trashcan')->href(href('PM', 'Trashcan'))->icon('delete'),
	GDO_Link::make('link_write_pm')->href(href('PM', 'Write'))->icon('create'),
));
echo $navbar->render();
