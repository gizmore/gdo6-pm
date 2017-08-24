<?php
namespace GDO\PM\Method;

use GDO\PM\PM;
use GDO\PM\PMFolder;
use GDO\Table\GDO_List;
use GDO\Table\MethodQueryList;
use GDO\User\User;
use GDO\Util\Common;

final class Folder extends MethodQueryList
{
	public function isUserRequired() { return true; }
	
	public function gdoTable() { return PM::table(); }
	
	/**
	 * @var PMFolder
	 */
	private $folder;
	
	public function init()
	{
		$this->folder = PMFolder::table()->find(Common::getRequestInt('folder', 1));
	}
	
	public function getFilters()
	{
		$table = PM::table();
		return array(
// 			GDO_RowNum::make(),
// 			GDO_Template::make()->module($this->module)->template('cell_pmunread.php'),
// 			GDO_PMFromTo::make('frmto'),
// 			$table->gdoColumn('pm_title'),
// 			GDO_Button::make('show'),
		);
	}
	
	public function gdoQuery()
	{
		$user = User::current();
		return PM::table()->select('*')->where('pm_owner='.$user->getID())->where('pm_folder='.$this->folder->getID())->where("pm_deleted_at IS NULL");
	}
	
	public function gdoDecorateList(GDO_List $list)
	{
		$list->rawlabel($this->folder->display('pmf_name'));
		$list->href(href('PM', 'Overview'));
// 		$list->actions()->addFields(array(
// 			GDO_Submit::make('delete')->label('btn_delete'),
// 			GDO_Submit::make('move')->label('btn_move'),
// 			GDO_PMFolder::make('folder')->user(User::current()),
// 		));
	}
}
