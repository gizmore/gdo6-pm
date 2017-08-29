<?php
namespace GDO\PM\Method;

use GDO\Core\Method;
use GDO\Date\Time;
use GDO\PM\PM;
use GDO\PM\PMMethod;
use GDO\UI\GDT_Button;
use GDO\User\User;
use GDO\Util\Common;

final class Read extends Method
{
	use PMMethod;
	
	public function execute()
	{
		if (!($pm = PM::getByIdAndUser(Common::getRequestString('id'), User::current())))
		{
			return $this->pmNavbar()->add($this->error('err_pm'));
		}
		return $this->pmNavbar()->add($this->pmRead($pm));
	}
	
	public function pmRead(PM $pm)
	{
		if (!$pm->isRead())
		{
			$pm->saveVar('pm_read_at', Time::getDate());
			$pm->getOtherPM()->saveVar('pm_other_read', '1');
		}
		$actions = array(
			GDT_Button::make('delete')->gdo($pm)->icon('delete'),
			GDT_Button::make('reply')->gdo($pm)->icon('reply'),
			GDT_Button::make('quote')->gdo($pm)->icon('quote'),
		);
		return $this->templatePHP('card_pm.php', ['pm' => $pm, 'actions' => $actions]);
	}
	
}
