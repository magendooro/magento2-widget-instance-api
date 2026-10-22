<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Test\Unit\Model\Data;

use Magendoo\WidgetInstanceApi\Model\Data\PageGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PageGroup::class)]
class PageGroupTest extends TestCase
{
    public function testEmptyObjectReturnsEmptyStrings(): void
    {
        $group = new PageGroup();

        $this->assertSame('', $group->getPageGroup());
        $this->assertSame('', $group->getLayoutHandle());
        $this->assertSame('', $group->getBlockReference());
        $this->assertSame('', $group->getPageFor());
        $this->assertSame('', $group->getEntities());
        $this->assertSame('', $group->getPageTemplate());
    }

    public function testSettersReturnTheSameObjectAndGettersReadBack(): void
    {
        $group = new PageGroup();

        $this->assertSame($group, $group->setPageGroup('pages'));
        $this->assertSame($group, $group->setLayoutHandle('cms_index_index'));
        $this->assertSame($group, $group->setBlockReference('content'));
        $this->assertSame($group, $group->setPageFor('specific'));
        $this->assertSame($group, $group->setEntities('1,2'));
        $this->assertSame($group, $group->setPageTemplate('widget/static_block/default.phtml'));

        $this->assertSame('pages', $group->getPageGroup());
        $this->assertSame('cms_index_index', $group->getLayoutHandle());
        $this->assertSame('content', $group->getBlockReference());
        $this->assertSame('specific', $group->getPageFor());
        $this->assertSame('1,2', $group->getEntities());
        $this->assertSame('widget/static_block/default.phtml', $group->getPageTemplate());
    }

    public function testValuesAreCastToStrings(): void
    {
        $group = new PageGroup();
        $group->setEntities(12)->setLayoutHandle(3);

        $this->assertSame('12', $group->getEntities());
        $this->assertSame('3', $group->getLayoutHandle());
    }

    public function testExportsTheApiFieldNames(): void
    {
        $group = new PageGroup();
        $group->setPageGroup('all_pages')->setPageFor('all');

        $this->assertSame(['page_group' => 'all_pages', 'page_for' => 'all'], $group->__toArray());
    }
}
