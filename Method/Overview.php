<?php
namespace GDO\PM\Method;

use GDO\Core\Method;
use GDO\DB\Database;
use GDO\Date\Time;
use GDO\PM\PM;
use GDO\PM\PMFolder;
use GDO\PM\PMMethod;
use GDO\User\User;
use GDO\Util\Common;
/**
 * Main PM Functionality / Navigation
 * @author gizmore
 */
final class Overview extends Method
{
	use PMMethod;
	
	public function isUserRequired() { return true; }
	
	public function execute()
	{
		if (isset($_REQUEST['delete']))
		{
			return $this->pmNavbar()->add($this->onDelete())->add($this->pmOverview());
		}
		elseif (isset($_REQUEST['move']))
		{
			return $this->pmNavbar()->add($this->onMove())->add($this->pmOverview());
		}
		return $this->pmNavbar()->add($this->pmOverview());
	}
	
	private function pmOverview()
	{
		$tVars = array(
			'folder' => $this->execMethod('Folder'),
			'folders' => $this->execMethod('Folders'),
		);
		return $this->templatePHP('overview.php', $tVars);
	}
	
	##############
	### Delete ###
	##############
	private function onDelete()
	{
		if ($ids = $this->getRBX())
		{
			$user = User::current();
			$now = Time::getDate();
			PM::table()->update()->set("pm_deleted_at='$now'")->where("pm_owner={$user->getID()} AND pm_id IN($ids)")->exec();
			$affected = Database::instance()->affectedRows();
			PM::updateOtherDeleted();
			return $this->message('msg_pm_deleted', [$affected]);
		}
	}
	
	private function onMove()
	{
		$user = User::current();
		if (!($folder = PMFolder::getByIdAndUser(Common::getFormString('folder'), $user)))
		{
			return $this->error('err_pm_folder');
		}
		if ($ids = $this->getRBX())
		{
			PM::table()->update()->set("pm_folder={$folder->getID()}")->where("pm_owner={$user->getID()} AND pm_id IN($ids)")->exec();
			$affected = Database::instance()->affectedRows();
			return $this->message('msg_pm_moved', [$affected, $folder->displayName()]);
		}
	}
}
