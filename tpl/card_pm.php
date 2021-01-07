<?php
use GDO\PM\GDO_PM;
use GDO\UI\GDT_Button;
use GDO\UI\GDT_HTML;
use GDO\Profile\GDT_ProfileLink;
use GDO\UI\GDT_Card;
use GDO\UI\GDT_Title;
use GDO\UI\GDT_Container;

/** @var $pm GDO_PM **/

$creator = $pm->getSender();

$card = GDT_Card::make('pm-'.$pm->getID());

$card->avatar(GDT_ProfileLink::make()->forUser($creator)->withAvatar());
$card->title(GDT_Title::make()->labelRaw($pm->displayTitle()));
$card->subtitle(GDT_Container::make()->addFields([
    GDT_ProfileLink::make()->forUser($creator)->withNickname(),
    $pm->gdoColumn('pm_sent_at'),
]));

$html = <<<EOT
<div>
  <h3>{$pm->displayTitle()}<h3>
  <hr/>
  <div>{$pm->displayMessage()}</div>
  <hr/>
  <div>{$pm->displaySignature()}</div>
EOT;
$card->content(GDT_HTML::withHTML($html));

if (!isset($noactions))
{
    $card->actions()->addFields(array(
    	GDT_Button::make('quote')->gdo($pm)->icon('quote'),
    	GDT_Button::make('reply')->gdo($pm)->icon('reply'),
    	GDT_Button::make('delete')->gdo($pm)->icon('delete'),
    ));
}

echo $card->renderCell();
