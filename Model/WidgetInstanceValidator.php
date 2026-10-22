<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Model;

use Magendoo\WidgetInstanceApi\Api\Data\PageGroupInterface;
use Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceInterface;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Widget\Model\Widget;

/**
 * Checks the inputs of a widget instance save before Magento's Instance model sees them.
 */
class WidgetInstanceValidator
{
    private const ENTITIES_PATTERN = '/^[0-9,]+\z/';
    private const LAYOUT_HANDLE_PATTERN = '/^[a-z0-9_]+\z/';
    private const BLOCK_REFERENCE_PATTERN = '/^[A-Za-z0-9_.-]+\z/';

    /**
     * @param Widget $widget
     * @param ProductType $productType
     */
    public function __construct(
        private readonly Widget $widget,
        private readonly ProductType $productType
    ) {
    }

    /**
     * Check the widget type and every page group of the widget instance.
     *
     * @param WidgetInstanceInterface $widget
     * @return void
     * @throws CouldNotSaveException
     */
    public function validate(WidgetInstanceInterface $widget): void
    {
        $this->validateInstanceType($widget->getInstanceType());
        $allowedGroups = $this->allowedPageGroups();
        foreach ($widget->getPageGroups() as $group) {
            $this->validatePageGroup($group, $allowedGroups);
        }
    }

    /**
     * Check that the widget type is declared in an enabled module's widget.xml.
     *
     * @param string $type
     * @return void
     * @throws CouldNotSaveException
     */
    private function validateInstanceType(string $type): void
    {
        if ($type === '') {
            throw new CouldNotSaveException(__('Could not save the widget instance: instance_type is required.'));
        }
        if ($this->widget->getWidgetByClassType($type) === null) {
            throw $this->invalid(
                WidgetInstanceInterface::INSTANCE_TYPE,
                $type,
                'Use a widget type declared in a widget.xml of an enabled module.'
            );
        }
    }

    /**
     * Check one page group row.
     *
     * @param PageGroupInterface $group
     * @param string[] $allowedGroups
     * @return void
     * @throws CouldNotSaveException
     */
    private function validatePageGroup(PageGroupInterface $group, array $allowedGroups): void
    {
        $name = $group->getPageGroup();
        if ($name === '') {
            throw new CouldNotSaveException(__('Could not save the widget instance: page_group is required.'));
        }
        if (!in_array($name, $allowedGroups, true)) {
            throw $this->invalid(
                PageGroupInterface::PAGE_GROUP,
                $name,
                'Use one of: ' . implode(', ', $allowedGroups) . '.'
            );
        }
        $pageFor = $group->getPageFor();
        if ($pageFor !== '' && $pageFor !== 'all' && $pageFor !== 'specific') {
            throw $this->invalid(PageGroupInterface::PAGE_FOR, $pageFor, 'Use all or specific.');
        }
        $this->validatePattern(
            PageGroupInterface::ENTITIES,
            $group->getEntities(),
            self::ENTITIES_PATTERN,
            'Use digits and commas only, for example 1,2,3.'
        );
        $this->validatePattern(
            PageGroupInterface::LAYOUT_HANDLE,
            $group->getLayoutHandle(),
            self::LAYOUT_HANDLE_PATTERN,
            'Use lowercase letters, digits and underscores only.'
        );
        $this->validatePattern(
            PageGroupInterface::BLOCK_REFERENCE,
            $group->getBlockReference(),
            self::BLOCK_REFERENCE_PATTERN,
            'Use letters, digits, underscores, dots and hyphens only.'
        );
    }

    /**
     * Check a non-empty value against a pattern; empty values stay allowed.
     *
     * @param string $field
     * @param string $value
     * @param string $pattern
     * @param string $remedy
     * @return void
     * @throws CouldNotSaveException
     */
    private function validatePattern(string $field, string $value, string $pattern, string $remedy): void
    {
        if ($value !== '' && preg_match($pattern, $value) !== 1) {
            throw $this->invalid($field, $value, $remedy);
        }
    }

    /**
     * The page groups Instance::_construct() registers: the fixed ones and one per product type.
     *
     * @return string[]
     */
    private function allowedPageGroups(): array
    {
        $groups = ['all_pages', 'pages', 'page_layouts', 'anchor_categories', 'notanchor_categories', 'all_products'];
        foreach (array_keys($this->productType->getTypes()) as $typeId) {
            $groups[] = $typeId . '_products';
        }
        return $groups;
    }

    /**
     * Build the exception that names the rejected field.
     *
     * @param string $field
     * @param string $value
     * @param string $remedy
     * @return CouldNotSaveException
     */
    private function invalid(string $field, string $value, string $remedy): CouldNotSaveException
    {
        return new CouldNotSaveException(
            __('Could not save the widget instance: %1 "%2" is not valid. %3', $field, $value, $remedy)
        );
    }
}
