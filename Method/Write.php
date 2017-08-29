<?php
namespace GDO\PM\Method;

use GDO\Core\GDT_Hook;
use GDO\Date\Time;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\PM\EMailOnPM;
use GDO\PM\Module_PM;
use GDO\PM\PM;
use GDO\PM\PMFolder;
use GDO\PM\PMMethod;
use GDO\User\GDT_User;
use GDO\User\User;
use GDO\Util\Common;
use GDO\Util\Strings;
use GDO\Form\GDT_Validator;

final class Write extends MethodForm
{
	use PMMethod;
	
	private $reply;
	
	public function execute()
	{
		$user = User::current();
		$module = Module_PM::instance();

		# Get in reply to
		if ($this->reply = PM::table()->find(Common::getRequestString('reply'), false))
		{
			if ($this->reply->getOwnerID() !== $user->getID())
			{
				$this->reply = null;
			}
		}
		
		if ($module->cfgIsPMLimited())
		{
			$limit = $module->cfgLimitForUser($user);
			$cut = time() - $module->cfgLimitTimeout();
			$sent = PM::table()->countWhere("pm_from={$user->getID()} and pm_sent_at>$cut");
			if ($sent >= $limit)
			{
				return $this->pmNavbar()->add($this->error('err_pm_limit_reached', [$limit, Time::displayAgeTS($cut)]));
			}
		}
		return $this->pmNavbar()->add(parent::execute());
	}
	
	public function createForm(GDT_Form $form)
	{
		list($username, $title, $message) = $this->initialValues();
		$table = PM::table();
		$form->addFields(array(
			GDT_User::make('pm_write_to')->notNull()->var($username),
		    GDT_Validator::make()->validator('pm_write_to', [$this, 'validateCanSend']),
			$table->gdoColumn('pm_title')->var($title),
			$table->gdoColumn('pm_message')->var($message),
			GDT_Submit::make(),
			GDT_AntiCSRF::make(),
		));
	}
	
	private function initialValues()
	{
		$username = null; $title = null; $message = null;
		if ($this->reply)
		{
			# Recipient
			$username = $this->reply->getOtherUser(User::current())->displayName();
			# Message
			$message= $this->reply->getVar('pm_message');
			# Title
			$title = $this->reply->getVar('pm_title');
			$re = Module_PM::instance()->cfgRE();
			$title = $re . ' ' . trim(Strings::substrFrom($title, $re));
		}
		return [$username, $title, $message];
	}
	
	public function validateCanSend(GDT_Form $form, GDT_User $user, $value)
	{
	    if ($value === null)
	    {
	        return $this->error('err_not_null');
	    }
	    if ($value->getID() === User::current()->getID())
	    {
	        return $this->error('err_no_pm_self');
	    }
		return true;
	}
	
	public function formValidated(GDT_Form $form)
	{
		$this->deliver(User::current(), $form->getFormValue('pm_write_to'), $form->getFormVar('pm_title'), $form->getFormVar('pm_message'), $this->reply);
		return $this->message('msg_pm_sent');
	}
	
	public function deliver(User $from, User $to, string $title, string $message, PM $parent=null)
	{
		$pmFrom = PM::blank(array(
				'pm_parent' => $parent ? $parent->getPMFor($from)->getID() : null,
				'pm_read_at' => Time::getDate(),
				'pm_owner' => $from->getID(),
				'pm_from' => $from->getID(),
				'pm_to' => $to->getID(),
				'pm_folder' => PMFolder::OUTBOX,
				'pm_title' => $title,
				'pm_message' => $message,
		))->insert();
		$pmTo = PM::blank(array(
				'pm_parent' => $parent ? $parent->getPMFor($to)->getID() : null,
				'pm_owner' => $to->getID(),
				'pm_from' => $from->getID(),
				'pm_to' => $to->getID(),
				'pm_folder' => PMFolder::INBOX,
				'pm_title' => $title,
				'pm_message' => $message,
				'pm_other' => $pmFrom->getID(),
				'pm_other_read' => '1',
		))->insert();
		$pmFrom->saveVar('pm_other', $pmTo->getID());
		$to->tempUnset('gwf_pm_unread');
		
		# Copy to next func
		$this->pmTo = $pmTo;
	}
	
	/**
	 * @var PM
	 */
	private $pmTo;
	public function afterExecute()
	{
		if ($this->pmTo)
		{
			$pmTo = $this->pmTo;
			EMailOnPM::deliver($pmTo);
			GDT_Hook::call('PMSent', $pmTo);
		}
	}
}
