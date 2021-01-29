<?php
use GDO\PM\GDO_PM;
use GDO\User\GDO_User;
use GDO\Profile\GDT_ProfileLink;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_ListItem;
use GDO\UI\GDT_Action;
use GDO\UI\GDT_Button;
use GDO\UI\GDT_Container;
use GDO\UI\GDT_Title;

/** @var $pm GDO_PM **/

$user = GDO_User::current();
$otherUser = $pm->getOtherUser($user);
$href = href('PM', 'Read', '&id='.$pm->getID());
$hrefDelete = href('PM', 'Overview', '&delete=1&id='.$pm->getID());

$li = GDT_ListItem::make('pm-'.$pm->getID());

$fromto = $pm->getSender()->getID() === $user->getID() ? 'pm_fromto_to' : 'pm_fromto_from'; 
$li->avatar(GDT_ProfileLink::make()->forUser($otherUser)->withAvatar());
$li->title(GDT_Link::make()->href($href)->label($pm->displayTitle()));
$li->subtitle(
    GDT_Container::make()->addFields([
        GDT_Title::make()->titleEscaped(false)->title($fromto, [GDT_ProfileLink::make()->forUser($otherUser)->withNickname()->render()]),
        $pm->gdoColumn('pm_sent_at'),
    ]));

$li->actions()->addFields(array(
	GDT_Button::make()->href($href)->icon('view')->label('btn_view'),
	GDT_Action::make()->href($hrefDelete)->icon('delete')->label('btn_delete'),
));

$li->addClass($pm->isRead() ? 'pm-read' : 'pm-unread');

echo $li->renderCell();
