<?php
namespace GDO\PM;

use GDO\Form\GDO_Select;
use GDO\User\User;

final class GDO_PMFolder extends GDO_Select
{
	public function __construct()
	{
		$this->name('folder');
		$this->label('folder');
		$this->choices($this->userChoices($user));
	}
	
	public function user(User $user)
	{
		$this->gdo($user);
		$this->emptyLabel('choose_folder_move');
		return $this;
	}
	
	private function userChoices(User $user)
	{
		$choices = [];
		foreach (PMFolder::getFolders($user->getID()) as $folder)
		{
			$choices[$folder->getID()] = $folder->getName();
		}
		return $choices;
	}
}