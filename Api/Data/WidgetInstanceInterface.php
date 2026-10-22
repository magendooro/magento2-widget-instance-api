<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Api\Data;

/**
 * A widget instance (Content > Widgets) as the REST API exposes it.
 *
 * @api
 */
interface WidgetInstanceInterface
{
    public const INSTANCE_ID = 'instance_id';
    public const INSTANCE_TYPE = 'instance_type';
    public const THEME_ID = 'theme_id';
    public const TITLE = 'title';
    public const STORE_IDS = 'store_ids';
    public const SORT_ORDER = 'sort_order';
    public const WIDGET_PARAMETERS = 'widget_parameters';
    public const PAGE_GROUPS = 'page_groups';

    /**
     * Get the widget instance ID, null while the instance is not saved.
     *
     * @return int|null
     */
    public function getInstanceId();

    /**
     * Set the widget instance ID.
     *
     * @param int $instanceId
     * @return $this
     */
    public function setInstanceId($instanceId);

    /**
     * The widget type class, for example Magento\Cms\Block\Widget\Block.
     *
     * @return string
     */
    public function getInstanceType();

    /**
     * Set the widget type class.
     *
     * @param string $instanceType
     * @return $this
     */
    public function setInstanceType($instanceType);

    /**
     * Get the ID of the theme the instance is assigned to.
     *
     * @return int
     */
    public function getThemeId();

    /**
     * Set the ID of the theme the instance is assigned to.
     *
     * @param int $themeId
     * @return $this
     */
    public function setThemeId($themeId);

    /**
     * Get the widget instance title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set the widget instance title.
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get the IDs of the store views the instance is shown in.
     *
     * @return int[]
     */
    public function getStoreIds();

    /**
     * Set the IDs of the store views the instance is shown in.
     *
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds(array $storeIds);

    /**
     * Get the sort order of the instance.
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Set the sort order of the instance.
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * The widget's parameters as a free-form object (the keys depend on the widget type).
     * UnstructuredArray is the web API's pass-through type: keys survive serialisation.
     *
     * @return UnstructuredArray
     * @phpstan-return array<string, mixed>|\stdClass
     */
    public function getWidgetParameters();

    /**
     * Set the widget's parameters as a free-form object.
     *
     * @param UnstructuredArray $widgetParameters
     * @phpstan-param array<string, mixed> $widgetParameters
     * @return $this
     */
    public function setWidgetParameters($widgetParameters);

    /**
     * Get the placements of the instance.
     *
     * @return \Magendoo\WidgetInstanceApi\Api\Data\PageGroupInterface[]
     */
    public function getPageGroups();

    /**
     * Set the placements of the instance.
     *
     * @param \Magendoo\WidgetInstanceApi\Api\Data\PageGroupInterface[] $pageGroups
     * @return $this
     */
    public function setPageGroups(array $pageGroups);
}
