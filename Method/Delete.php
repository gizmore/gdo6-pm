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
			return $this->pmNavbar()->addField($this->error('err_pm'))->addField(Overview::make()->execute());
		}
		return $this->pmNavbar()->addField($this->onDelete($pm))->addField(Overview::make()->execute());
	}
	
	public function deletePM(GDO_PM $pm)
	{
	    $pm->saveVar('pm_read_at', Time::getDate());
		$pm->saveVar('pm_deleted_at', Time::getDate());
		$pm->getOtherPM()->saveVar('pm_other_deleted_at', Time::getDate());
		return $this->message('msg_pm_deleted');
	}
}
