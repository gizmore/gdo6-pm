<?php
namespace GDO\PM;

use GDO\Form\GDT_Select;
use GDO\User\GDO_User;

final class GDT_PMFolder extends GDT_Select
{
	public function __construct()
	{
		$this->name('folder');
		$this->label('folder');
		$this->choices($this->userChoices($user));
	}
	
	public function user(GDO_User $user)
	{
		$this->gdo($user);
		$this->emptyLabel('choose_folder_move');
		return $this;
	}
	
	private function userChoices(GDO_User $user)
	{
		$choices = [];
		foreach (GDO_PMFolder::getFolders($user->getID()) as $folder)
		{
			$choices[$folder->getID()] = $folder->getName();
		}
		return $choices;
	}
}