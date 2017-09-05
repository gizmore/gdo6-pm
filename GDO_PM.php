<?php
namespace GDO\PM;

use GDO\DB\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\DB\GDT_CreatedAt;
use GDO\DB\GDT_DeletedAt;
use GDO\DB\GDT_Object;
use GDO\Date\GDT_DateTime;
use GDO\Date\Time;
use GDO\Template\GDT_Template;
use GDO\Type\GDT_Checkbox;
use GDO\Type\GDT_Message;
use GDO\Type\GDT_String;
use GDO\User\GDT_User;
use GDO\User\GDO_User;

final class GDO_PM extends GDO # implements GDT_Searchable
{
	public function gdoCached() { return false; }
	
	###########
	### GDO ###
	###########
	public function gdoColumns()
	{
		return array(
			GDT_AutoInc::make('pm_id'),
			GDT_CreatedAt::make('pm_sent_at'),
			GDT_DeletedAt::make('pm_deleted_at'),
			GDT_DateTime::make('pm_read_at'),
			GDT_User::make('pm_owner')->notNull(),
			GDT_User::make('pm_from')->cascadeNull(),
			GDT_User::make('pm_to')->notNull(),
		    GDT_Object::make('pm_folder')->table(GDO_PMFolder::table())->notNull(),
		    GDT_Object::make('pm_parent')->table(GDO_PM::table())->cascadeNull(),
		    GDT_Object::make('pm_other')->table(GDO_PM::table())->cascadeNull(),
			GDT_String::make('pm_title')->notNull()->label('title'),
			GDT_Message::make('pm_message')->notNull(),
			GDT_Checkbox::make('pm_other_read')->initial('0'),
			GDT_Checkbox::make('pm_other_deleted')->initial('0'),
		);
	}
	
	##############
	### Render ###
	##############
	public function renderList() { return GDT_Template::php('PM', 'listitem_pm.php', ['pm' => $this]); }
	
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
	 * @param GDO_User $user
	 * @return User
	 */
	public function getOtherUser(GDO_User $user)
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
	 * @return self
	 */
	public function getOtherPM() { return $this->getValue('pm_other'); }

	public function getFromID() { return $this->getVar('pm_from'); }
	public function getToID() { return $this->getVar('pm_to'); }
	
	/**
	 * @return self
	 */
	public function getParent() { return $this->getValue('pm_parent'); }
	
	/**
	 * @param GDO_User $owner
	 * @return self
	 */
	public function getPMFor(GDO_User $owner) { return $this->getOwnerID() === $owner->getID() ? $this : $this->getOtherPM(); }
	
	public function isFrom(GDO_User $user) { return $this->getFromID() === $user->getID(); }
	public function isTo(GDO_User $user) { return $this->getToID() === $user->getID(); }
	
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
		where(" ( SELECT pm_id FROM ( SELECT * FROM gdo_pm ) b WHERE gdo_pm.pm_other = b.pm_id ) IS NULL ")->
		or(" ( SELECT pm_deleted_at FROM ( SELECT * FROM gdo_pm ) b WHERE b.pm_id = gdo_pm.pm_other ) IS NOT NULL ")->exec();
	}
	
	public static function getByIdAndUser(string $id, GDO_User $user)
	{
		$id = self::quoteS($id);
		return self::table()->select('*')->where("pm_id={$id} AND pm_owner={$user->getID()}")->exec()->fetchObject();
	}
	
	##############
	### Unread ###
	##############
	public static function countUnread(GDO_User $user)
	{
		if (null !== ($cache = $user->tempGet('gdo_pm_unread')))
		{
			$cache = self::table()->countWhere("pm_to={$user->getID()} AND pm_read_at IS NULL");
			$user->tempSet('gdo_pm_unread', $cache);
		}
		return $cache;
	}
}
