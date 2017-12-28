<?php
use GDO\PM\GDO_PM;
use GDO\UI\GDT_Button;
use GDO\UI\GDT_HTML;
use GDO\User\GDO_UserSettingBlob;
use GDO\UI\GDT_Card;
use GDO\Profile\GDT_ProfileLink;
$pm instanceof GDO_PM;

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

foreach ($actions as $action)
{
	$action instanceof GDT_Button; 
	$card->actions()->addField($action);
}

echo $card->render();
