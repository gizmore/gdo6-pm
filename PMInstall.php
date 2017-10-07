<?php
namespace GDO\PM;

use GDO\Date\Time;
use GDO\User\GDO_User;

final class PMInstall
{
	public static function install(Module_PM $module)
	{
		self::installFolders($module).
		self::installPMBotID($module);
	}
	
	private static function installFolders(Module_PM $module)
	{
	    if (!GDO_PMFolder::table()->countWhere('true'))
		{
		    GDO_PMFolder::blank(['pmf_name' => 'INBOX'])->insert();
		    GDO_PMFolder::blank(['pmf_name' => 'OUTBOX'])->insert();
		}
	}
	
	private static function installPMBotID(Module_PM $module)
	{
		if (!($user = $module->cfgBotUser()))
		{
			if ($module->cfgOwnBot())
			{
				self::installPMBot($module);
			}
			else 
			{
				self::installAdminAsPMBot($module);
			}
		}
	}
	
	private static function installAdminAsPMBot(Module_PM $module)
	{
		$users = GDO_User::withPermission('admin');
		if ($user = @$users[0])
		{
			$module->saveConfigVar('pm_bot_uid', $user->getID());
		}
	}
	
	private static function installPMBot(Module_PM $module)
	{
		$user = GDO_User::blank(array(
			'user_name' => '_PM_BOT_',
			'user_real_name' => GWF_BOT_NAME,
			'user_type' => GDO_User::BOT,
			'user_email' => GWF_BOT_EMAIL,
			'user_register_time' => Time::getDate(),
		));
		$user->insert();
		$module->saveConfigVar('pm_bot_uid', $user->getID());
	}
}