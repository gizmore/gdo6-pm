<?php
namespace GDO\PM\Method;

use GDO\PM\GDO_PM;
use GDO\PM\GDO_PMFolder;
use GDO\Table\GDT_List;
use GDO\Table\MethodQueryList;
use GDO\User\GDO_User;
use GDO\Util\Common;

/**
 * Display a PM folder.
 * 
 * @author gizmore
 * @version 6.10
 * @since 5.03
 *
 * @see GDO_PMFolder
 */
final class Folder extends MethodQueryList
{
	public function gdoTable() { return GDO_PM::table(); }

	public function isUserRequired() { return true; }
	public function isQuicksorted() { return true; }
	public function isQuicksearchable() { return true; }
	
	public function defaultOrderField() { return 'pm_sent_at'; }
	public function defaultOrderDirAsc() { return false; }
	
	/**
	 * @var GDO_PMFolder
	 */
	private $folder;
	
	public function init()
	{
		$this->folder = GDO_PMFolder::table()->find(Common::getRequestInt('folder', 1));
	}
	
	public function gdoFilters()
	{
		$table = GDO_PM::table();
		return array(
		    $table->gdoColumn('pm_to'),
		    $table->gdoColumn('pm_from'),
		    $table->gdoColumn('pm_sent_at'),
		    $table->gdoColumn('pm_title'),
		    $table->gdoColumn('pm_message'),
		);
	}
	
	public function gdoQuery()
	{
		$user = GDO_User::current();
		return GDO_PM::table()->select('*')->
		where('pm_owner='.$user->getID())->
		where('pm_folder='.$this->folder->getID())->
		where("pm_deleted_at IS NULL");
	}
	
	public function gdoDecorateList(GDT_List $list)
	{
		$list->title($this->folder->display('pmf_name'));
		$list->href(href('PM', 'Overview', '&folder=' . $this->folder->getID()));
	}
	
}
