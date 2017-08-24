<?php
namespace GDO\PM;
trait PMMethod
{
	public function pmNavbar()
	{
		return Module_PM::instance()->templatePHP('navbar.php');
	}
}
