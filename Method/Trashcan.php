<?php
namespace GDO\PM\Method;

use GDO\DB\Database;
use GDO\Form\GDT_Submit;
use GDO\PM\GDT_PMFromTo;
use GDO\PM\GDO_PM;
use GDO\PM\PMMethod;
use GDO\Table\GDT_RowNum;
use GDO\Table\GDT_Table;
use GDO\Table\MethodQueryTable;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
/**
 * Trashcan features restore, delete, and empty bin.
 * 
 * @author gizmore
 *
 */
final class Trashcan extends MethodQueryTable
{
	use PMMethod;
	
	public function isUserRequired() { return true; }
	
	public function getGDO() { return GDO_PM::table(); }
	
	public function execute()
	{
		if (isset($_REQUEST['delete']))
		{
			return $this->pmNavbar()->add($this->onDelete())->add(parent::execute());
		}
		elseif (isset($_REQUEST['restore']))
		{
			return $this->pmNavbar()->add($this->onRestore())->add(parent::execute());
		}
		elseif (isset($_REQUEST['trash']))
		{
			return $this->pmNavbar()->add($this->onEmpty())->add(parent::execute());
		}
		return $this->pmNavbar()->add(parent::execute());
	}
	
	public function getHeaders()
	{
		return array(
			GDT_RowNum::make(),
			GDT_PMFromTo::make(),
			GDT_Link::make('show'),
		);
	}
	
	public function getQuery()
	{
		$user = GDO_User::current();
		return GDO_PM::table()->select('*')->where('pm_owner='.$user->getID())->where("pm_deleted_at IS NOT NULL");
	}
	
	public function getResult()
	{
		return $this->filterQuery($this->getQueryPaginated())->select('*')->exec();
	}
	
	public function onDecorateTable(GDT_Table $table)
	{
		$table->rawlabel(t('name_trashcan'));
		$table->actions()->addFields(array(
			GDT_Submit::make('restore')->label('btn_restore'),
			GDT_Submit::make('delete')->label('btn_delete'),
			GDT_Submit::make('trash')->label('btn_empty'),
		));
	}
	
	###############
	### Actions ###
	###############
	public function onDelete()
	{
		if ($ids = $this->getRBX())
		{
			$user = GDO_User::current();
			GDO_PM::table()->deleteWhere("pm_owner={$user->getID()} AND pm_id IN($ids)")->exec();
			$affected = Database::instance()->affectedRows();
			return $this->message('msg_pm_destroyed', [$affected]);
		}
	}
	
	public function onRestore()
	{
		if ($ids = $this->getRBX())
		{
			$user = GDO_User::current();
			GDO_PM::table()->update()->set("pm_deleted_at = NULL")->where("pm_owner={$user->getID()} AND pm_id IN($ids)")->exec();
			$affected = Database::instance()->affectedRows();
			GDO_PM::updateOtherDeleted();
			return $this->message('msg_pm_restored', [$affected]);
		}
	}
	
	public function onEmpty()
	{
		$user = GDO_User::current();
		GDO_PM::table()->deleteWhere("pm_owner={$user->getID()} AND pm_deleted_at IS NOT NULL")->exec();
		$affected = Database::instance()->affectedRows();
		return $this->message('msg_pm_destroyed', [$affected]);
	}
	
}
