<?php
namespace GDO\PM;

use GDO\DB\GDO;
use GDO\DB\GDO_AutoInc;
use GDO\DB\GDO_CreatedAt;
use GDO\DB\GDO_DeletedAt;
use GDO\DB\GDO_Object;
use GDO\Date\GDO_DateTime;
use GDO\Date\Time;
use GDO\Template\GDO_Template;
use GDO\Type\GDO_Checkbox;
use GDO\Type\GDO_Message;
use GDO\Type\GDO_String;
use GDO\User\GDO_User;
use GDO\User\User;

final class PM extends GDO # implements GDO_Searchable
{
	public function gdoCached() { return false; }
	
	###########
	### GDO ###
	###########
	public function gdoColumns()
	{
		return array(
			GDO_AutoInc::make('pm_id'),
			GDO_CreatedAt::make('pm_sent_at'),
			GDO_DeletedAt::make('pm_deleted_at'),
			GDO_DateTime::make('pm_read_at'),
			GDO_User::make('pm_owner')->notNull(),
			GDO_User::make('pm_from')->cascadeNull(),
			GDO_User::make('pm_to')->notNull(),
			GDO_Object::make('pm_folder')->table(PMFolder::table())->notNull(),
		    GDO_Object::make('pm_parent')->table(PM::table())->cascadeNull(),
		    GDO_Object::make('pm_other')->table(PM::table())->cascadeNull(),
			GDO_String::make('pm_title')->notNull()->label('title'),
			GDO_Message::make('pm_message')->notNull(),
			GDO_Checkbox::make('pm_other_read')->initial('0'),
			GDO_Checkbox::make('pm_other_deleted')->initial('0'),
		);
	}
	
	##############
	### Render ###
	##############
	public function renderList() { return GDO_Template::php('PM', 'listitem_pm.php', ['pm' => $this]); }
	
	##################
	### Convinient ###
	##################
	public function isRead() { return $this->getVar('pm_read_at') !== null; }
	public function displayDate() { return Time::displayDate($this->getVar('pm_sent_at')); }
	public function getTitle() { return $this->getVar('pm_title'); }
	
	/**
	 * @return User
	 */
	public function getSender() { return $this->getValue('pm_from'); }
	
	/**
	 * @return User
	 */
	public function getReceiver() { return $this->getValue('pm_to'); }
	
	/**
	 * @return User
	 */
	public function getOwner() { return $this->getValue('pm_owner'); }
	public function getOwnerID() { return $this->getVar('pm_owner'); }
	public function getOtherID() { return $this->getVar('pm_other'); }

	/**
	 * Get the other user that differs from param user.
	 * One of the two users has to match.
	 * @param User $user
	 * @return User
	 */
	public function getOtherUser(User $user)
	{
		if ($user->getID() === $this->getFromID())
		{
			return $this->getReceiver();
		}
		elseif ($user->getID() === $this->getToID())
		{
			return $this->getSender();
		}
	}
	
	/**
	 * @return PM
	 */
	public function getOtherPM() { return $this->getValue('pm_other'); }

	public function getFromID() { return $this->getVar('pm_from'); }
	public function getToID() { return $this->getVar('pm_to'); }
	
	/**
	 * @return PM
	 */
	public function getParent() { return $this->getValue('pm_parent'); }
	
	/**
	 * @param User $owner
	 * @return PM
	 */
	public function getPMFor(User $owner) { return $this->getOwnerID() === $owner->getID() ? $this : $this->getOtherPM(); }
	
	public function isFrom(User $user) { return $this->getFromID() === $user->getID(); }
	public function isTo(User $user) { return $this->getToID() === $user->getID(); }
	
	#############
	### HREFs ###
	#############
// 	public function display_show() { return $this->display('pm_title'); }
	public function href_show() { return href('PM', 'Read', "&id={$this->getID()}"); }
	public function href_delete() { return href('PM', 'Overview', "&delete=1&rbx[{$this->getID()}]=1"); }
	public function href_reply() { return href('PM', 'Write', '&reply='.$this->getID()); }
	public function href_quote() { return href('PM', 'Write', '&quote=yes&reply='.$this->getID()); }
	
	##############
	### Static ###
	##############
	public static function updateOtherDeleted()
	{
		self::table()->update()->set("pm_other_deleted=1")->
		where(" ( SELECT pm_id FROM ( SELECT * FROM gwf_pm ) b WHERE gwf_pm.pm_other = b.pm_id ) IS NULL ")->
		or(" ( SELECT pm_deleted_at FROM ( SELECT * FROM gwf_pm ) b WHERE b.pm_id = gwf_pm.pm_other ) IS NOT NULL ")->exec();
	}
	
	public static function getByIdAndUser(string $id, User $user)
	{
		$id = self::quoteS($id);
		return self::table()->select('*')->where("pm_id={$id} AND pm_owner={$user->getID()}")->exec()->fetchObject();
	}
	
	##############
	### Unread ###
	##############
	public static function countUnread(User $user)
	{
		if (null !== ($cache = $user->tempGet('gwf_pm_unread')))
		{
			$cache = self::table()->countWhere("pm_to={$user->getID()} AND pm_read_at IS NULL");
			$user->tempSet('gwf_pm_unread', $cache);
		}
		return $cache;
	}
}
