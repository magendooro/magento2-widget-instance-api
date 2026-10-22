<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Test\Unit\Model;

use Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceInterface;
use Magendoo\WidgetInstanceApi\Model\Data\PageGroup;
use Magendoo\WidgetInstanceApi\Model\Data\WidgetInstance;
use Magendoo\WidgetInstanceApi\Model\WidgetInstanceValidator;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Widget\Model\Widget;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(WidgetInstanceValidator::class)]
class WidgetInstanceValidatorTest extends TestCase
{
    private const WIDGET_TYPE = 'Magento\Cms\Block\Widget\Block';

    /**
     * @var WidgetInstanceValidator
     */
    private WidgetInstanceValidator $validator;

    protected function setUp(): void
    {
        $widget = $this->createStub(Widget::class);
        $widget->method('getWidgetByClassType')->willReturnCallback(
            static fn (string $type): ?array => $type === self::WIDGET_TYPE ? ['name' => 'CMS Static Block'] : null
        );
        $productType = $this->createStub(ProductType::class);
        $productType->method('getTypes')->willReturn(['simple' => [], 'virtual' => []]);
        $this->validator = new WidgetInstanceValidator($widget, $productType);
    }

    public function testAcceptsWidgetWithoutPageGroups(): void
    {
        $this->expectNotToPerformAssertions();

        $this->validator->validate($this->widget(self::WIDGET_TYPE, []));
    }

    public function testAcceptsFullyPopulatedPageGroup(): void
    {
        $this->expectNotToPerformAssertions();
        $group = $this->group('pages', 'specific', '1,22,333', 'cms_index_index', 'main.content-1');

        $this->validator->validate($this->widget(self::WIDGET_TYPE, [$group]));
    }

    #[DataProvider('validPageGroupProvider')]
    public function testAcceptsPageGroup(string $pageGroup): void
    {
        $this->expectNotToPerformAssertions();

        $this->validator->validate($this->widget(self::WIDGET_TYPE, [$this->group($pageGroup)]));
    }

    #[DataProvider('validPageForProvider')]
    public function testAcceptsPageFor(string $pageFor): void
    {
        $this->expectNotToPerformAssertions();

        $this->validator->validate($this->widget(self::WIDGET_TYPE, [$this->group('all_pages', $pageFor)]));
    }

    #[DataProvider('validEntitiesProvider')]
    public function testAcceptsEntities(string $entities): void
    {
        $this->expectNotToPerformAssertions();

        $this->validator->validate($this->widget(self::WIDGET_TYPE, [$this->group('pages', 'all', $entities)]));
    }

    #[DataProvider('validLayoutHandleProvider')]
    public function testAcceptsLayoutHandle(string $layoutHandle): void
    {
        $this->expectNotToPerformAssertions();
        $group = $this->group('pages', 'all', '', $layoutHandle);

        $this->validator->validate($this->widget(self::WIDGET_TYPE, [$group]));
    }

    #[DataProvider('validBlockReferenceProvider')]
    public function testAcceptsBlockReference(string $blockReference): void
    {
        $this->expectNotToPerformAssertions();
        $group = $this->group('pages', 'all', '', '', $blockReference);

        $this->validator->validate($this->widget(self::WIDGET_TYPE, [$group]));
    }

    #[DataProvider('invalidInstanceTypeProvider')]
    public function testRejectsInstanceType(string $instanceType): void
    {
        $this->assertRejected('instance_type', $this->widget($instanceType, [$this->group('all_pages')]));
    }

    #[DataProvider('invalidPageGroupProvider')]
    public function testRejectsPageGroup(string $pageGroup): void
    {
        $this->assertRejected('page_group', $this->widget(self::WIDGET_TYPE, [$this->group($pageGroup)]));
    }

    public function testRejectsInvalidPageGroupAfterValidOne(): void
    {
        $groups = [$this->group('all_pages'), $this->group('bogus')];

        $this->assertRejected('page_group', $this->widget(self::WIDGET_TYPE, $groups));
    }

    #[DataProvider('invalidPageForProvider')]
    public function testRejectsPageFor(string $pageFor): void
    {
        $this->assertRejected(
            'page_for',
            $this->widget(self::WIDGET_TYPE, [$this->group('all_pages', $pageFor)])
        );
    }

    #[DataProvider('invalidEntitiesProvider')]
    public function testRejectsEntities(string $entities): void
    {
        $this->assertRejected(
            'entities',
            $this->widget(self::WIDGET_TYPE, [$this->group('pages', 'specific', $entities)])
        );
    }

    #[DataProvider('invalidLayoutHandleProvider')]
    public function testRejectsLayoutHandle(string $layoutHandle): void
    {
        $this->assertRejected(
            'layout_handle',
            $this->widget(self::WIDGET_TYPE, [$this->group('pages', 'all', '', $layoutHandle)])
        );
    }

    #[DataProvider('invalidBlockReferenceProvider')]
    public function testRejectsBlockReference(string $blockReference): void
    {
        $this->assertRejected(
            'block_reference',
            $this->widget(self::WIDGET_TYPE, [$this->group('pages', 'all', '', '', $blockReference)])
        );
    }

    /**
     * @return array<string, array{string}>
     */
    public static function validPageGroupProvider(): array
    {
        return [
            'all pages' => ['all_pages'],
            'pages' => ['pages'],
            'page layouts' => ['page_layouts'],
            'anchor categories' => ['anchor_categories'],
            'non-anchor categories' => ['notanchor_categories'],
            'all products' => ['all_products'],
            'simple products' => ['simple_products'],
            'virtual products' => ['virtual_products'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function validPageForProvider(): array
    {
        return ['empty defaults to all' => [''], 'all' => ['all'], 'specific' => ['specific']];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function validEntitiesProvider(): array
    {
        return ['empty' => [''], 'one id' => ['7'], 'several ids' => ['1,2,3'], 'only commas' => [',']];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function validLayoutHandleProvider(): array
    {
        return [
            'empty' => [''],
            'handle' => ['cms_index_index'],
            'with digits' => ['catalog_product_view_type_simple2'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function validBlockReferenceProvider(): array
    {
        return [
            'empty' => [''],
            'name' => ['content'],
            'dots and hyphens' => ['main.content-1'],
            'upper case and underscore' => ['Top_Links'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidInstanceTypeProvider(): array
    {
        return [
            'empty' => [''],
            'unknown class' => ['Vendor\Missing\Block\Widget'],
            'different case' => ['magento\cms\block\widget\block'],
            'leading backslash' => ['\Magento\Cms\Block\Widget\Block'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidPageGroupProvider(): array
    {
        return [
            'empty' => [''],
            'unknown' => ['bogus'],
            'different case' => ['ALL_PAGES'],
            'trailing space' => ['all_pages '],
            'product type that is not installed' => ['configurable_products'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidPageForProvider(): array
    {
        return ['unknown' => ['none'], 'different case' => ['ALL'], 'trailing space' => ['specific ']];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidEntitiesProvider(): array
    {
        return [
            'letters' => ['1,a'],
            'space' => ['1 2'],
            'semicolon' => ['1;2'],
            'negative' => ['-1'],
            'trailing newline' => ["1,2\n"],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidLayoutHandleProvider(): array
    {
        return [
            'space' => ['A B'],
            'upper case' => ['Catalog_Product'],
            'hyphen' => ['cms-index'],
            'dot' => ['cms.index'],
            'trailing newline' => ["cms_index\n"],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidBlockReferenceProvider(): array
    {
        return [
            'space' => ['main content'],
            'slash' => ['content/main'],
            'colon' => ['a:b'],
            'trailing newline' => ["content\n"],
        ];
    }

    private function assertRejected(string $field, WidgetInstanceInterface $widget): void
    {
        try {
            $this->validator->validate($widget);
        } catch (CouldNotSaveException $exception) {
            $this->assertStringContainsString($field, $exception->getMessage());
            return;
        }
        $this->fail('Expected a CouldNotSaveException naming ' . $field . '.');
    }

    /**
     * @param string $type
     * @param PageGroup[] $groups
     * @return WidgetInstance
     */
    private function widget(string $type, array $groups): WidgetInstance
    {
        $widget = new WidgetInstance();
        $widget->setInstanceType($type)->setPageGroups($groups);
        return $widget;
    }

    private function group(
        string $pageGroup,
        string $pageFor = '',
        string $entities = '',
        string $layoutHandle = '',
        string $blockReference = ''
    ): PageGroup {
        $group = new PageGroup();
        $group->setPageGroup($pageGroup)
            ->setPageFor($pageFor)
            ->setEntities($entities)
            ->setLayoutHandle($layoutHandle)
            ->setBlockReference($blockReference);
        return $group;
    }
}
