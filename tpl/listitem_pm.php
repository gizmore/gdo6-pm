<?php
use GDO\PM\GDO_PM;
use GDO\UI\GDT_IconButton;
use GDO\User\GDO_User;
use GDO\Avatar\GDO_Avatar;
use GDO\Profile\GDT_ProfileLink;
use GDO\UI\GDT_Link;

$pm instanceof GDO_PM;
$user = GDO_User::current();
$otherUser = $pm->getOtherUser($user);
$href = href('PM', 'Read', '&id='.$pm->getID());
$hrefDelete = href('PM', 'Overview', '&delete=1&id='.$pm->getID());
?>
<li class="gdt-list-item">
  <div><?=GDT_ProfileLink::make()->forUser($otherUser)->render()?></div>
  <div class="gdt-content">
    <h3><?= GDT_Link::make()->href(href('PM', 'Read', "&id={$pm->getID()}"))->label($pm->getTitle())->render(); ?></h3>
    <h4><?= $otherUser->displayName(); ?></h4>
    <p><?= t('pm_sent', [$pm->displayDate()]); ?></p>
  </div>
</li>
