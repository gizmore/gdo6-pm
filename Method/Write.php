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
use GDO\PM\GDO_PM;
use GDO\PM\GDO_PMFolder;
use GDO\PM\PMMethod;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
use GDO\Util\Common;
use GDO\Util\Strings;
use GDO\Form\GDT_Validator;

final class Write extends MethodForm
{
	use PMMethod;
	
	private $reply;
	
	public function execute()
	{
		$user = GDO_User::current();
		$module = Module_PM::instance();

		# Get in reply to
		if ($this->reply = GDO_PM::table()->find(Common::getRequestString('reply'), false))
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
			$sent = GDO_PM::table()->countWhere("pm_from={$user->getID()} and pm_sent_at>$cut");
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
		$table = GDO_PM::table();
		$form->addFields(array(
			GDT_User::make('pm_write_to')->notNull()->initial($username),
		    GDT_Validator::make()->validator('pm_write_to', [$this, 'validateCanSend']),
		    $table->gdoColumn('pm_title')->initial($title),
		    $table->gdoColumn('pm_message')->initial($message),
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
			$username = $this->reply->getOtherUser(GDO_User::current())->getID();
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
	    if ($value->getID() === GDO_User::current()->getID())
	    {
	        return $this->error('err_no_pm_self');
	    }
		return true;
	}
	
	public function formValidated(GDT_Form $form)
	{
		$this->deliver(GDO_User::current(), $form->getFormValue('pm_write_to'), $form->getFormVar('pm_title'), $form->getFormVar('pm_message'), $this->reply);
		return $this->message('msg_pm_sent');
	}
	
	public function deliver(GDO_User $from, GDO_User $to, $title, $message, GDO_PM $parent=null)
	{
	    $pmFrom = GDO_PM::blank(array(
				'pm_parent' => $parent ? $parent->getPMFor($from)->getID() : null,
				'pm_read_at' => Time::getDate(),
				'pm_owner' => $from->getID(),
				'pm_from' => $from->getID(),
				'pm_to' => $to->getID(),
	       	    'pm_folder' => GDO_PMFolder::OUTBOX,
				'pm_title' => $title,
				'pm_message' => $message,
		))->insert();
		$pmTo = GDO_PM::blank(array(
				'pm_parent' => $parent ? $parent->getPMFor($to)->getID() : null,
				'pm_owner' => $to->getID(),
				'pm_from' => $from->getID(),
				'pm_to' => $to->getID(),
		        'pm_folder' => GDO_PMFolder::INBOX,
				'pm_title' => $title,
				'pm_message' => $message,
				'pm_other' => $pmFrom->getID(),
				'pm_other_read' => '1',
		))->insert();
		$pmFrom->saveVar('pm_other', $pmTo->getID());
		$to->tempUnset('gdo_pm_unread');
		
		# Copy to next func
		$this->pmTo = $pmTo;
	}
	
	/**
	 * @var GDO_PM
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
