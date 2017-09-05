<?php
use GDO\PM\GDO_PM;
use GDO\UI\GDT_Button;
use GDO\User\GDO_UserSetting;
use GDO\User\GDO_UserSettingBlob;
$pm instanceof GDO_PM;
?>
<md-card>
  <md-card-title>
    <md-card-title-text>
      <span class="md-headline"><?php $pm->edisplay('pm_title'); ?></span>
    </md-card-title-text>
  </md-card-title>
  <md-card-content>
    <section layout="row" flex layout-fill>
      <div>
        <b><?= t('pm_by', [$pm->getSender()->displayName()]); ?></b><br/><b><?= t('pm_to', [$pm->getReceiver()->displayName()]); ?></b>
      </div>
      <div>
        <b><?= t('pm_sent', [$pm->displayDate()]); ?></b>
      </div>
    </section>
    <section layout="column" flex layout-fill>
<?= $pm->gdoColumn('pm_message')->renderCell(); ?>
    </section>
    <hr/>
<?= GDO_UserSettingBlob::get('signature')->renderCell(); ?>
  </md-card-content>
  <md-card-actions layout="row" layout-align="end center">
    <?php foreach ($actions as $action) : $action instanceof GDT_Button; ?>
    <?= $action->renderCell(); ?>
    <?php endforeach; ?>
  </md-card-actions>
</md-card>
