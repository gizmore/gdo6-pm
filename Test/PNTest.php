<?php
namespace GDO\PM\Test;

use PHPUnit\Framework\TestCase;
use GDO\Tests\MethodTest;

final class PNTest extends TestCase
{
    public function testDefaultMethods()
    {
        MethodTest::make()->defaultMethod('PM', 'Folders');
        MethodTest::make()->defaultMethod('PM', 'Folder');
    }
    
}
