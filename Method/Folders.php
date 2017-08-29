<?php
namespace GDO\PM\Method;

use GDO\DB\ArrayResult;
use GDO\PM\PMFolder;
use GDO\Table\MethodTable;
use GDO\Template\GDT_Template;
use GDO\User\User;

final class Folders extends MethodTable
{
	public function isFiltered() { return false; }
	public function isPaginated() { return false; }
	public function isUserRequired() { return true; }
	
	public function getHeaders()
	{
		$table = PMFolder::table();
		return array(
			GDT_Template::make()->template('PM', 'folder_link.php')->label('folder'),
			$table->gdoColumn('pmf_count'),
		);
	}
	
	public function getResult()
	{
		$folders = PMFolder::getFolders(User::current()->getID());
		return new ArrayResult($folders, PMFolder::table());
	}
}
