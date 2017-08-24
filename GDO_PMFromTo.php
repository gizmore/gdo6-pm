<?php
namespace GDO\PM;
use GDO\Type\GDO_Base;
use GDO\UI\WithLabel;

final class GDO_PMFromTo extends GDO_Base
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
