<?php
namespace GDO\PM;

use GDO\DB\GDO;
use GDO\DB\GDO_AutoInc;
use GDO\Type\GDO_Int;
use GDO\Type\GDO_Name;
use GDO\User\GDO_User;
use GDO\User\User;

final class PMFolder extends GDO
{
	# Constants
	const INBOX = 1;
	const OUTBOX = 2;
	
	###########
	### GDO ###
	###########
	public function gdoColumns()
	{
		return array(
			GDO_AutoInc::make('pmf_id'),
			GDO_User::make('pmf_user'),
			GDO_Name::make('pmf_name')->notNull(),
			GDO_Int::make('pmf_count')->unsigned()->initial('0')->label('count'),
		);
	}
	public function getID() { return $this->getVar('pmf_id'); }
	public function getUserID() { return $this->getVar('pmf_user'); }
	public function getName() { return $this->getVar('pmf_name'); }
	public function displayName() { return $this->display('pmf_name'); }
// 	public function isRealFolder() { return $this->getID() > 2; }
	
	/**
	 * @param string $userid
	 * @return array
	 */
	public static function getFolders(string $userid)
	{
		static $folders;
		if (!isset($folders))
		{
			$folders = array_merge(
				PMFolder::getDefaultFolders(),
				self::table()->select('*')->where('pmf_user='.quote($userid))->exec()->fetchAllObjects()
			);
		}
		return $folders;
	}
	
	/**
	 * @param int $folderId
	 * @param User $user
	 * @return PMFolder
	 */
	public static function getByIdAndUser(string $folderId, User $user)
	{
		$folderId = (int)$folderId;
		switch ($folderId)
		{
			case self::INBOX: return self::getInBox();
			case self::OUTBOX: return self::getOutBox();
		}
		if ($folder = self::table()->find($folderId, false))
		{
			if ($folder->getUserID() === $user->getID())
			{
				return $folder;
			}
		}
	}
	
	
	#######################
	### Default Folders ###
	#######################
	public static function getDefaultFolders()
	{
		return [self::getInBox(), self::getOutBox()];
	}
	
	public static function getInBox()
	{
		static $inbox;
		if (!isset($inbox))
		{
			$uid = User::current()->getID();
			$fid = self::INBOX;
			$inbox = self::blank(array(
				'pmf_id' => $fid,
				'pmf_uid' => $uid,
				'pmf_name' => t('inbox_name'),
				'pmf_count' => PM::table()->countWhere("pm_folder=$fid AND pm_owner=$uid AND pm_deleted_at IS NULL"),
			));
		}
		return $inbox;
	}
	
	public static function getOutBox()
	{
		static $outbox;
		if (!isset($outbox))
		{
			$uid = User::current()->getID();
			$fid = self::OUTBOX;
			$outbox = self::blank(array(
				'pmf_id' => $fid,
				'pmf_uid' => $uid,
				'pmf_name' => t('outbox_name'),
				'pmf_count' => PM::table()->countWhere("pm_folder=$fid AND pm_owner=$uid AND pm_deleted_at IS NULL"),
			));
		}
		return $outbox;
	}
}
