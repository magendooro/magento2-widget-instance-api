<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Test\Unit\Model;

use Magendoo\WidgetInstanceApi\Api\Data\ThemeInterfaceFactory;
use Magendoo\WidgetInstanceApi\Model\Data\Theme;
use Magendoo\WidgetInstanceApi\Model\ThemeList;
use Magento\Framework\DataObject;
use Magento\Theme\Model\ResourceModel\Theme\Collection;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ThemeList::class)]
class ThemeListTest extends TestCase
{
    public function testGetThemesMapsFrontendThemesToIdAndCode(): void
    {
        $themes = $this->themeList([
            new DataObject(['id' => 1, 'theme_path' => 'Magento/blank']),
            new DataObject(['id' => '3', 'theme_path' => 'Magento/luma']),
        ]);

        $result = $themes->getThemes();

        $this->assertCount(2, $result);
        $this->assertSame(1, $result[0]->getThemeId());
        $this->assertSame('Magento/blank', $result[0]->getCode());
        $this->assertSame(3, $result[1]->getThemeId());
        $this->assertSame('Magento/luma', $result[1]->getCode());
    }

    public function testGetThemesReturnsAnEmptyListWithoutThemes(): void
    {
        $this->assertSame([], $this->themeList([])->getThemes());
    }

    /**
     * @param DataObject[] $items
     * @return ThemeList
     */
    private function themeList(array $items): ThemeList
    {
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())->method('addAreaFilter')->with('frontend')->willReturnSelf();
        $collection->expects($this->once())->method('setOrder')->with('theme_id', 'ASC')->willReturnSelf();
        $collection->expects($this->once())->method('getIterator')->willReturn(new \ArrayIterator($items));
        $collectionFactory = $this->createStub(CollectionFactory::class);
        $collectionFactory->method('create')->willReturn($collection);
        $themeFactory = $this->createStub(ThemeInterfaceFactory::class);
        $themeFactory->method('create')->willReturnCallback(static fn (): Theme => new Theme());

        return new ThemeList($collectionFactory, $themeFactory);
    }
}
