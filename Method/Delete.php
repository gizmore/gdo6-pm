<?php
namespace GDO\PM\Method;

use GDO\Core\Method;
use GDO\Date\Time;
use GDO\PM\GDO_PM;
use GDO\PM\PMMethod;
use GDO\Util\Common;

final class Delete extends Method
{
	use PMMethod;
	
	public function execute()
	{
		if ( (!($pm = GDO_PM::getById(Common::getRequestString('pm')))) || 
				($pm->gdoHashcode() !== Common::getRequestString('token')) )
		{
			return $this->pmNavbar()->add($this->error('err_pm'))->add(Overview::make()->execute());
		}
		return $this->pmNavbar()->add($this->onDelete($pm))->add(Overview::make()->execute());
	}
	
	public function deletePM(GDO_PM $pm)
	{
		$pm->saveVar('pm_deleted_at', Time::getDate());
		$pm->getOtherPM()->saveVar('pm_other_deleted', '1');
		return $this->message('msg_pm_deleted');
	}
}
