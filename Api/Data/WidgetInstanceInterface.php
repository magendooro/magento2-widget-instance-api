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
     * @return int|null
     */
    public function getInstanceId();

    /**
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
     * @param string $instanceType
     * @return $this
     */
    public function setInstanceType($instanceType);

    /**
     * @return int
     */
    public function getThemeId();

    /**
     * @param int $themeId
     * @return $this
     */
    public function setThemeId($themeId);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return int[]
     */
    public function getStoreIds();

    /**
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds(array $storeIds);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * The widget's parameters as a free-form object (the keys depend on the widget type).
     * UnstructuredArray is the web API's pass-through type: keys survive serialisation.
     *
     * @return UnstructuredArray
     */
    public function getWidgetParameters();

    /**
     * @param UnstructuredArray $widgetParameters
     * @return $this
     */
    public function setWidgetParameters($widgetParameters);

    /**
     * @return \Magendoo\WidgetInstanceApi\Api\Data\PageGroupInterface[]
     */
    public function getPageGroups();

    /**
     * @param \Magendoo\WidgetInstanceApi\Api\Data\PageGroupInterface[] $pageGroups
     * @return $this
     */
    public function setPageGroups(array $pageGroups);
}
