<?php 
use GDO\PM\GDT_PMFromTo;
use GDO\PM\GDO_PM;
use GDO\User\GDO_User;
$field instanceof GDT_PMFromTo;
$pm instanceof GDO_PM;
$user = GDO_User::current();
?>
<?php if ($pm->isFrom($user)) : ?>
TO <?= $pm->getReceiver()->displayName(); ?>
<?php else : ?>
FROM <?= $pm->getSender()->displayName(); ?>
<?php endif; ?>
