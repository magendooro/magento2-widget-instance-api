<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Test\Unit\Model\Data;

use Magendoo\WidgetInstanceApi\Model\Data\PageGroup;
use Magendoo\WidgetInstanceApi\Model\Data\WidgetInstance;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(WidgetInstance::class)]
class WidgetInstanceTest extends TestCase
{
    public function testEmptyObjectReturnsTypedDefaults(): void
    {
        $widget = new WidgetInstance();

        $this->assertNull($widget->getInstanceId());
        $this->assertSame('', $widget->getInstanceType());
        $this->assertSame(0, $widget->getThemeId());
        $this->assertSame('', $widget->getTitle());
        $this->assertSame([], $widget->getStoreIds());
        $this->assertSame(0, $widget->getSortOrder());
        $this->assertSame([], $widget->getWidgetParameters());
        $this->assertSame([], $widget->getPageGroups());
    }

    public function testSettersReturnTheSameObjectAndGettersReadBack(): void
    {
        $widget = new WidgetInstance();
        $group = new PageGroup();

        $this->assertSame($widget, $widget->setInstanceId(5));
        $this->assertSame($widget, $widget->setInstanceType('Magento\Cms\Block\Widget\Block'));
        $this->assertSame($widget, $widget->setThemeId(3));
        $this->assertSame($widget, $widget->setTitle('Home banner'));
        $this->assertSame($widget, $widget->setStoreIds([0, 1]));
        $this->assertSame($widget, $widget->setSortOrder(10));
        $this->assertSame($widget, $widget->setWidgetParameters(['block_id' => 'home']));
        $this->assertSame($widget, $widget->setPageGroups([$group]));

        $this->assertSame(5, $widget->getInstanceId());
        $this->assertSame('Magento\Cms\Block\Widget\Block', $widget->getInstanceType());
        $this->assertSame(3, $widget->getThemeId());
        $this->assertSame('Home banner', $widget->getTitle());
        $this->assertSame([0, 1], $widget->getStoreIds());
        $this->assertSame(10, $widget->getSortOrder());
        $this->assertSame(['block_id' => 'home'], $widget->getWidgetParameters());
        $this->assertSame([$group], $widget->getPageGroups());
    }

    public function testNullInstanceIdStaysNull(): void
    {
        $widget = new WidgetInstance();
        $widget->setInstanceId(5);
        $widget->setInstanceId(null);

        $this->assertNull($widget->getInstanceId());
    }

    public function testScalarsAreCast(): void
    {
        $widget = new WidgetInstance();
        $widget->setInstanceId('12')
            ->setThemeId('3')
            ->setSortOrder('4')
            ->setTitle(7)
            ->setInstanceType(8);

        $this->assertSame(12, $widget->getInstanceId());
        $this->assertSame(3, $widget->getThemeId());
        $this->assertSame(4, $widget->getSortOrder());
        $this->assertSame('7', $widget->getTitle());
        $this->assertSame('8', $widget->getInstanceType());
    }

    public function testStoreIdsAreCastToIntegers(): void
    {
        $widget = new WidgetInstance();
        $widget->setStoreIds(['0', '2']);

        $this->assertSame([0, 2], $widget->getStoreIds());
    }

    public function testEmptyWidgetParametersAreAnEmptyArray(): void
    {
        $widget = new WidgetInstance();
        $widget->setWidgetParameters([]);

        $this->assertSame([], $widget->getWidgetParameters());

        $widget->setWidgetParameters(null);

        $this->assertSame([], $widget->getWidgetParameters());
    }
}
