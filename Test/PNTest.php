<?php
namespace GDO\PM\Test;

use GDO\Tests\MethodTest;
use GDO\Tests\TestCase;

final class PNTest extends TestCase
{
    public function testDefaultMethods()
    {
        MethodTest::make()->defaultMethod('PM', 'Folders');
        $this->assert200("Test PM::Folders");
        MethodTest::make()->defaultMethod('PM', 'Folder');
        $this->assert200("Test PM::Folder");
    }
    
}
