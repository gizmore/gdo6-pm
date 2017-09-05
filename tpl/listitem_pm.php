<?php
use GDO\PM\GDO_PM;
use GDO\UI\GDT_IconButton;
use GDO\User\GDO_User;
use GDO\Avatar\GDO_Avatar;

$pm instanceof GDO_PM;
$user = GDO_User::current();
$otherUser = $pm->getOtherUser($user);
$href = href('PM', 'Read', '&id='.$pm->getID());
$hrefDelete = href('PM', 'Overview', '&delete=1&id='.$pm->getID());
?>
<?php if ($pm->isFrom($user)) : ?>
<md-list-item class="md-3-line" ng-click="null" href="<?= $href; ?>">
  <?= GDO_Avatar::renderAvatar($otherUser); ?>
  <div class="md-list-item-text" layout="column">
    <h3><?= $otherUser->displayName(); ?></h3>
    <h4><?= html($pm->getTitle()); ?></h4>
    <p><?= t('pm_sent', [$pm->displayDate()]); ?></p>
  </div>
  <?= GDT_IconButton::make()->icon('delete')->href($hrefDelete); ?>
</md-list-item>
<?php else : ?>
<md-list-item class="md-3-line" ng-click="null" href="<?= $href; ?>">
  <?= GDO_Avatar::renderAvatar($otherUser); ?>
  <div class="md-list-item-text" layout="column">
    <h3><?= $otherUser->displayName(); ?></h3>
    <h4><?= html($pm->getTitle()); ?></h4>
    <p><?= t('pm_received', [$pm->displayDate()]); ?></p>
  </div>
  <?= GDT_IconButton::make()->icon('delete')->href($hrefDelete); ?>
</md-list-item>
<?php endif; ?>
