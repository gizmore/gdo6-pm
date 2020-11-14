<?php
namespace GDO\PM;

use GDO\UI\GDT_Page;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Button;

/**
 * PM Methods draw a navbar.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.02
 */
trait PMMethod
{
    public function beforeExecute()
    {
        $navbar = GDT_Bar::make()->horizontal();
        $navbar->addFields([
            GDT_Button::make('btn_overview')->href(href('PM', 'Overview'))->icon('table'),
            GDT_Button::make('link_settings')->href(href('Account', 'Settings', '&module=PM'))->icon('settings'),
            GDT_Button::make('link_trashcan')->href(href('PM', 'Trashcan'))->icon('delete'),
            GDT_Button::make('link_write_pm')->href(href('PM', 'Write'))->icon('create'),
        ]);
        GDT_Page::$INSTANCE->topTabs->addField($navbar);
    }
    
}
