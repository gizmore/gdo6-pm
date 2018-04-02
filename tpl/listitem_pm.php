<?php
use GDO\PM\GDO_PM;
use GDO\UI\GDT_IconButton;
use GDO\User\GDO_User;
use GDO\Profile\GDT_ProfileLink;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_ListItem;
use GDO\UI\GDT_Headline;
use GDO\UI\GDT_Paragraph;
use GDO\UI\GDT_Action;
use GDO\UI\GDT_Button;

$pm instanceof GDO_PM;
$user = GDO_User::current();
$otherUser = $pm->getOtherUser($user);
$href = href('PM', 'Read', '&id='.$pm->getID());
$hrefDelete = href('PM', 'Overview', '&delete=1&id='.$pm->getID());

$li = GDT_ListItem::make();
$li->image(GDT_ProfileLink::make()->forUser($otherUser));
$li->title(GDT_Link::make()->href($href)->label($pm->getTitle()));
$li->subtitle(GDT_Headline::make()->level(4)->html($otherUser->displayNameLabel()));
$li->subtext(GDT_Paragraph::make()->html(t('pm_sent', [$pm->displayDate()])));
$li->actions()->addFields(array(
	GDT_Button::make()->href($href)->icon('view')->label('btn_view'),
	GDT_Action::make()->href($hrefDelete)->icon('delete')->label('btn_delete'),
));

echo $li->render();
