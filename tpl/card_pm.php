<?php
use GDO\PM\GDO_PM;
use GDO\UI\GDT_Button;
use GDO\UI\GDT_HTML;
use GDO\UI\GDT_Card;
use GDO\Profile\GDT_ProfileLink;

/** @var $pm GDO_PM **/

$creator = $pm->getSender();

$card = new GDT_Card();
$avatar = GDT_ProfileLink::make()->forUser($creator)->withNickname()->render();
$title=<<<EOT
<div>
<div>{$avatar}</div>
<div>{$pm->displayDate()}</div>
</div>
EOT;
$card->title($title);

$html = <<<EOT
<hr/>
{$pm->display('pm_title')}
<hr/>
{$pm->displayMessage()}
<hr/>
{$pm->displaySignature()}
<hr/>
EOT;
$card->addField(GDT_HTML::withHTML($html));

$card->actions()->addFields(array(
	GDT_Button::make('delete')->gdo($pm)->icon('delete'),
	GDT_Button::make('reply')->gdo($pm)->icon('reply'),
	GDT_Button::make('quote')->gdo($pm)->icon('quote'),
));


echo $card->render();
