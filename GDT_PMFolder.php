<?php
namespace GDO\PM;

use GDO\User\GDO_User;
use GDO\DB\GDT_ObjectSelect;

/**
 * A PM folder
 * @author gizmore
 */
final class GDT_PMFolder extends GDT_ObjectSelect
{
	public function __construct()
	{
		$this->name('folder');
		$this->label('folder');
		$this->icon('folder');
		$this->table(GDO_PMFolder::table());
	}
	
	public function user(GDO_User $user)
	{
		$this->gdo($user);
		$this->emptyLabel('choose_folder_move');
		return $this->choices($this->userChoices($user));
	}
	
	private function userChoices(GDO_User $user)
	{
		$choices = [];
		foreach (GDO_PMFolder::getFolders($user->getID()) as $folder)
		{
			$choices[$folder->getID()] = $folder;
		}
		return $choices;
	}
	
}