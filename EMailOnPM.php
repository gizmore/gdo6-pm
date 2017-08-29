<?php
namespace GDO\PM;

use GDO\Mail\Mail;
use GDO\Template\Response;
use GDO\UI\GDT_Link;
use GDO\User\User;
use GDO\User\UserSetting;
/**
 * Sends Email on PM.
 * 
 * @author gizmore
 *
 */
final class EMailOnPM
{
	public static function deliver(PM $pm)
	{
		$receiver = $pm->getReceiver();
		if (UserSetting::userGet($receiver, 'pm_email')->getValue())
		{
			if ($receiver->getMail())
			{
				self::sendMail($pm, $receiver);
			}
		}
	}
	
	private static function sendMail(PM $pm, User $receiver)
	{
		$sender = $pm->getSender();
		
		$email = new Mail();
		$email->setSender(GWF_BOT_EMAIL);
		$email->setSenderName(GWF_BOT_NAME);
		if (UserSetting::userGet($sender, 'user_allow_email'))
		{
			$email->setReturn($sender->getMail());
		}
		
		$sitename = sitename();
		$email->setSubject(tusr($receiver, 'mail_subj_pm', [$sitename, $sender->displayName()]));
		$email->setBody(tusr($receiver, 'mail_body_pm', array(
			$receiver->displayName(),
			$sender->displayName(),
			$sitename,
			$pm->display('pm_title'),
			$pm->display('pm_message'),
			GDT_Link::anchor(href('PM', 'Delete', "&id={$pm->getID()}&token={$pm->gdoHashcode()}")),
		)));
		$email->sendToUser($receiver);
		echo Response::message('msg_pm_mail_sent', [$receiver->displayName()])->render();
	}
}
