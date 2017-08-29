<?php
use GDO\PM\GDT_PMFromTo;
$field instanceof GDT_PMFromTo;
?>
<input
 name="f[<?= $field->name?>]"
 type="text"
 value="<?= $field->displayFilterValue(); ?>" />
