<?php
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;

$navbar = GDT_Bar::make()->horizontal();
$navbar->addFields(array(
	GDT_Link::make('link_overview')->href(href('PM', 'Overview'))->icon('storage'),
	GDT_Link::make('link_settings')->href(href('Account', 'Settings', '&module=PM'))->icon('settings'),
	GDT_Link::make('link_trashcan')->href(href('PM', 'Trashcan'))->icon('delete'),
	GDT_Link::make('link_write_pm')->href(href('PM', 'Write'))->icon('create'),
));
echo $navbar->render();
