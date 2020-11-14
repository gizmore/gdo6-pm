<?php
namespace GDO\PM;

use GDO\Core\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\DB\GDT_Int;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
use GDO\UI\GDT_Title;

/**
 * A PM folder.
 * There are two default folders that are shared in DB. 1 and 2 / Inbox and Outbox.
 * 
 * @author gizmore
 * @version 6.10
 * @since 3.05
 */
final class GDO_PMFolder extends GDO
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
			GDT_AutoInc::make('pmf_id'),
			GDT_User::make('pmf_user')->notNull(),
			GDT_Title::make('pmf_name')->notNull(),
			GDT_Int::make('pmf_count')->unsigned()->initial('0')->label('count'),
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
	public static function getFolders($userid)
	{
		static $folders;
		if (!isset($folders))
		{
			$folders = array_merge(
				GDO_PMFolder::getDefaultFolders(),
				self::table()->select()->where('pmf_user='.quote($userid))->exec()->fetchAllObjects()
			);
		}
		return $folders;
	}
	
	/**
	 * @param int $folderId
	 * @param GDO_User $user
	 * @return GDO_PMFolder
	 */
	public static function getByIdAndUser($folderId, GDO_User $user)
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
			$uid = GDO_User::current()->getID();
			$fid = self::INBOX;
			$inbox = self::blank(array(
				'pmf_id' => $fid,
				'pmf_uid' => $uid,
				'pmf_name' => t('inbox_name'),
				'pmf_count' => GDO_PM::table()->countWhere("pm_folder=$fid AND pm_owner=$uid AND pm_deleted_at IS NULL"),
			));
		}
		return $inbox;
	}
	
	public static function getOutBox()
	{
		static $outbox;
		if (!isset($outbox))
		{
			$uid = GDO_User::current()->getID();
			$fid = self::OUTBOX;
			$outbox = self::blank(array(
				'pmf_id' => $fid,
				'pmf_uid' => $uid,
				'pmf_name' => t('outbox_name'),
				'pmf_count' => GDO_PM::table()->countWhere("pm_folder=$fid AND pm_owner=$uid AND pm_deleted_at IS NULL"),
			));
		}
		return $outbox;
	}
}
