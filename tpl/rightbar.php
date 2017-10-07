<?php
use GDO\PM\GDO_PM;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
$navbar instanceof GDT_Bar;
$user = GDO_User::current();
$count = GDO_PM::countUnread($user);
$button = GDT_Link::make('btn_pm')->href(href('PM', 'Overview'));
if ($count > 0)
{
	$button->label('btn_pm_unread', [$count]);
}
$navbar->addField($button);
