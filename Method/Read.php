<?php
namespace GDO\PM\Method;

use GDO\Core\Method;
use GDO\Date\Time;
use GDO\PM\GDO_PM;
use GDO\PM\PMMethod;
use GDO\User\GDO_User;
use GDO\Util\Common;

final class Read extends Method
{
	use PMMethod;
	
	public function execute()
	{
	    if (!($pm = GDO_PM::getByIdAndUser(Common::getRequestString('id'), GDO_User::current())))
		{
			return $this->pmNavbar()->add($this->error('err_pm'));
		}
		return $this->pmNavbar()->add($this->pmRead($pm));
	}
	
	public function pmRead(GDO_PM $pm)
	{
		if (!$pm->isRead())
		{
			$pm->saveVar('pm_read_at', Time::getDate());
			$pm->getOtherPM()->saveVar('pm_other_read', '1');
		}
		return $this->templatePHP('card_pm.php', ['pm' => $pm]);
	}
	
}
