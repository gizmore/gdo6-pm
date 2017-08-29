<?php
use GDO\PM\PM;
use GDO\Template\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\User\User;
$navbar instanceof GDT_Bar;
$user = User::current();
$count = PM::countUnread($user);
$button = GDT_Link::make('btn_pm')->href(href('PM', 'Overview'));
if ($count > 0)
{
	$button->label('btn_pm_unread', [$count]);
}
$navbar->addField($button);
