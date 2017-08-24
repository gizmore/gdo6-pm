<?php 
use GDO\PM\GDO_PMFromTo;
use GDO\PM\PM;
use GDO\User\User;
$field instanceof GDO_PMFromTo;
$pm instanceof PM;
$user = User::current();
?>
<?php if ($pm->isFrom($user)) : ?>
TO <?= $pm->getReceiver()->displayName(); ?>
<?php else : ?>
FROM <?= $pm->getSender()->displayName(); ?>
<?php endif; ?>
