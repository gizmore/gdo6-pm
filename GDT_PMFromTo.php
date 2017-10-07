<?php
namespace GDO\PM;
use GDO\Core\GDT;
use GDO\UI\WithLabel;

final class GDT_PMFromTo extends GDT
{
    use WithLabel;
    
    public function displayHeaderLabel() { return ''; }
    
	public function renderCell()
	{
		return Module_PM::instance()->templatePHP('cell_pmfromto.php', ['field'=>$this, 'pm'=>$this->gdo]);
	}
	
	public function renderFilter()
	{
		return Module_PM::instance()->templatePHP('filter_pmfromto.php', ['field'=>$this, 'pm'=>$this->gdo]);
	}
}