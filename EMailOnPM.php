<?php
namespace GDO\PM;

use GDO\Mail\Mail;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
use GDO\Core\GDT_Success;
use GDO\Mail\Module_Mail;

/**
 * Sends Email on PM.
 * 
 * @author gizmore
 * @version 6.10.2
 * @since 3.4.0
 */
final class EMailOnPM
{
	public static function deliver(GDO_PM $pm)
	{
	    $module = Module_PM::instance();
		$receiver = $pm->getReceiver();
		if ($module->cfgEmailOnPM())
		{
    		if ($module->userSettingValue($receiver, 'pm_email'))
    		{
    			if ($receiver->hasMail())
    			{
    				return self::sendMail($pm, $receiver);
    			}
    		}
		}
	}
	
	private static function sendMail(GDO_PM $pm, GDO_User $receiver)
	{
		$sender = $pm->getSender();
		
		$email = Mail::botMail();

		if (Module_Mail::instance()->userSettingValue($sender, 'allow_email'))
		{
		    $email->setReturn($sender->getMail());
		    $email->setReturnName($sender->displayNameLabel());
		}
		
		$sitename = sitename();
		$email->setSubject(tusr($receiver, 'mail_subj_pm', [$sitename, $sender->displayNameLabel()]));
		$email->setBody(tusr($receiver, 'mail_body_pm', array(
			$receiver->displayNameLabel(),
			$sender->displayNameLabel(),
			$sitename,
			$pm->display('pm_title'),
			$pm->displayMessage(),
			GDT_Link::anchor(href('PM', 'Delete', "&id={$pm->getID()}&token={$pm->gdoHashcode()}")),
		)));
		$email->sendToUser($receiver);
		return GDT_Success::responseWith('msg_pm_mail_sent', [$receiver->displayNameLabel()]);
	}

}
