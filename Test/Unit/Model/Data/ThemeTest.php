<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Test\Unit\Model\Data;

use Magendoo\WidgetInstanceApi\Model\Data\Theme;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Theme::class)]
class ThemeTest extends TestCase
{
    public function testEmptyObjectReturnsTypedDefaults(): void
    {
        $theme = new Theme();

        $this->assertSame(0, $theme->getThemeId());
        $this->assertSame('', $theme->getCode());
    }

    public function testSettersReturnTheSameObjectAndGettersReadBack(): void
    {
        $theme = new Theme();

        $this->assertSame($theme, $theme->setThemeId(3));
        $this->assertSame($theme, $theme->setCode('Magento/luma'));

        $this->assertSame(3, $theme->getThemeId());
        $this->assertSame('Magento/luma', $theme->getCode());
    }

    public function testValuesAreCast(): void
    {
        $theme = new Theme();
        $theme->setThemeId('4')->setCode(5);

        $this->assertSame(4, $theme->getThemeId());
        $this->assertSame('5', $theme->getCode());
    }
}
