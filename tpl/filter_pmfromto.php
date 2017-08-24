<?php
use GDO\PM\GDO_PMFromTo;
$field instanceof GDO_PMFromTo;
?>
<input
 name="f[<?= $field->name?>]"
 type="text"
 value="<?= $field->displayFilterValue(); ?>" />
