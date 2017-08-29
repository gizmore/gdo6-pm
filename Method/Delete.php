<?php
namespace GDO\PM\Method;

use GDO\Core\Method;
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
			return $this->pmNavbar()->add($this->error('err_pm'))->add($this->execMethod('Overview'));
		}
		return $this->pmNavbar()->add($this->onDelete($pm))->add($this->execMethod('Overview'));
	}
	
	public function deletePM(GDO_PM $pm)
	{
		$pm->saveVar('pm_deleted_at', time());
		$pm->getOtherPM()->saveVar('pm_other_deleted', '1');
		return $this->message('msg_pm_deleted');
	}
}
