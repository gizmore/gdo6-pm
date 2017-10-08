<?php
namespace GDO\PM\Method;

use GDO\PM\GDO_PM;
use GDO\PM\GDO_PMFolder;
use GDO\Table\GDT_List;
use GDO\Table\MethodQueryList;
use GDO\User\GDO_User;
use GDO\Util\Common;

final class Folder extends MethodQueryList
{
	public function isUserRequired() { return true; }
	
	public function gdoTable() { return GDO_PM::table(); }
	
	/**
	 * @var GDO_PMFolder
	 */
	private $folder;
	
	public function init()
	{
	    $this->folder = GDO_PMFolder::table()->find(Common::getRequestInt('folder', 1));
	}
	
	public function getFilters()
	{
	    $table = GDO_PM::table();
		return array(
// 			GDT_RowNum::make(),
// 			GDT_Template::make()->module($this->module)->template('cell_pmunread.php'),
// 			GDT_PMFromTo::make('frmto'),
// 			$table->gdoColumn('pm_title'),
// 			GDT_Button::make('show'),
		);
	}
	
	public function gdoQuery()
	{
		$user = GDO_User::current();
		return GDO_PM::table()->select('*')->where('pm_owner='.$user->getID())->where('pm_folder='.$this->folder->getID())->where("pm_deleted_at IS NULL");
	}
	
	public function gdoDecorateList(GDT_List $list)
	{
		$list->title($this->folder->display('pmf_name'));
		$list->href(href('PM', 'Overview'));
// 		$list->actions()->addFields(array(
// 			GDT_Submit::make('delete')->label('btn_delete'),
// 			GDT_Submit::make('move')->label('btn_move'),
// 			GDT_PMFolder::make('folder')->user(GDO_User::current()),
// 		));
	}
}
