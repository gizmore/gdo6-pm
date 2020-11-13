<?php
namespace GDO\PM;

use GDO\Mail\Mail;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
use GDO\Core\GDT_Success;
use GDO\Account\Module_Account;

/**
 * Sends Email on PM.
 * 
 * @author gizmore
 * @version 6.10
 * @since 3.04
 */
final class EMailOnPM
{
	public static function deliver(GDO_PM $pm)
	{
		$receiver = $pm->getReceiver();
		if (Module_PM::instance()->userSettingValue($receiver, 'pm_email'))
		{
			if ($receiver->getMail())
			{
				return self::sendMail($pm, $receiver);
			}
		}
	}
	
	private static function sendMail(GDO_PM $pm, GDO_User $receiver)
	{
		$sender = $pm->getSender();
		
		$email = new Mail();
		$email->setSender(GWF_BOT_EMAIL);
		$email->setSenderName(GWF_BOT_NAME);
		if (Module_Account::instance()->userSettingValue($sender, 'user_allow_email'))
		{
			$email->setReturn($sender->getMail());
		}
		
		$sitename = sitename();
		$email->setSubject(tusr($receiver, 'mail_subj_pm', [$sitename, $sender->displayNameLabel()]));
		$email->setBody(tusr($receiver, 'mail_body_pm', array(
			$receiver->displayNameLabel(),
			$sender->displayNameLabel(),
			$sitename,
			$pm->display('pm_title'),
			$pm->display('pm_message'),
			GDT_Link::anchor(href('PM', 'Delete', "&id={$pm->getID()}&token={$pm->gdoHashcode()}")),
		)));
		$email->sendToUser($receiver);
		return GDT_Success::responseWith('msg_pm_mail_sent', [$receiver->displayNameLabel()]);
	}

}
