<?php
use GDO\PM\PM;
use GDO\Template\GDO_Bar;
use GDO\UI\GDO_Link;
use GDO\User\User;
$navbar instanceof GDO_Bar;
$user = User::current();
$count = PM::countUnread($user);
$button = GDO_Link::make('btn_pm')->href(href('PM', 'Overview'));
if ($count > 0)
{
	$button->label('btn_pm_unread', [$count]);
}
$navbar->addField($button);
