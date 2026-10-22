<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Model\Data;

use Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Data object of a widget instance; casts each value to the type the REST API promises.
 */
class WidgetInstance extends AbstractSimpleObject implements WidgetInstanceInterface
{
    /**
     * Get the widget instance ID, null while the instance is not saved.
     *
     * @return int|null
     */
    public function getInstanceId()
    {
        $id = $this->_get(self::INSTANCE_ID);
        return $id === null ? null : (int)$id;
    }

    /**
     * Set the widget instance ID.
     *
     * @param int|null $instanceId
     * @return $this
     */
    public function setInstanceId($instanceId)
    {
        return $this->setData(self::INSTANCE_ID, $instanceId === null ? null : (int)$instanceId);
    }

    /**
     * Get the widget type class.
     *
     * @return string
     */
    public function getInstanceType()
    {
        return (string)$this->_get(self::INSTANCE_TYPE);
    }

    /**
     * Set the widget type class.
     *
     * @param string $instanceType
     * @return $this
     */
    public function setInstanceType($instanceType)
    {
        return $this->setData(self::INSTANCE_TYPE, (string)$instanceType);
    }

    /**
     * Get the ID of the theme the instance is assigned to.
     *
     * @return int
     */
    public function getThemeId()
    {
        return (int)$this->_get(self::THEME_ID);
    }

    /**
     * Set the ID of the theme the instance is assigned to.
     *
     * @param int $themeId
     * @return $this
     */
    public function setThemeId($themeId)
    {
        return $this->setData(self::THEME_ID, (int)$themeId);
    }

    /**
     * Get the widget instance title.
     *
     * @return string
     */
    public function getTitle()
    {
        return (string)$this->_get(self::TITLE);
    }

    /**
     * Set the widget instance title.
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, (string)$title);
    }

    /**
     * Get the IDs of the store views the instance is shown in.
     *
     * @return int[]
     */
    public function getStoreIds()
    {
        return array_map('intval', (array)($this->_get(self::STORE_IDS) ?? []));
    }

    /**
     * Set the IDs of the store views the instance is shown in.
     *
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds(array $storeIds)
    {
        return $this->setData(self::STORE_IDS, array_map('intval', $storeIds));
    }

    /**
     * Get the sort order of the instance.
     *
     * @return int
     */
    public function getSortOrder()
    {
        return (int)$this->_get(self::SORT_ORDER);
    }

    /**
     * Set the sort order of the instance.
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, (int)$sortOrder);
    }

    /**
     * Get the widget's parameters; an empty set is an empty array.
     *
     * @return UnstructuredArray
     * @phpstan-return array<string, mixed>
     */
    public function getWidgetParameters()
    {
        $parameters = $this->_get(self::WIDGET_PARAMETERS);
        return $parameters === null || $parameters === [] ? [] : $parameters;
    }

    /**
     * Set the widget's parameters.
     *
     * @param UnstructuredArray $widgetParameters
     * @phpstan-param array<string, mixed> $widgetParameters
     * @return $this
     */
    public function setWidgetParameters($widgetParameters)
    {
        return $this->setData(self::WIDGET_PARAMETERS, $widgetParameters);
    }

    /**
     * Get the placements of the instance.
     *
     * @return \Magendoo\WidgetInstanceApi\Api\Data\PageGroupInterface[]
     */
    public function getPageGroups()
    {
        return (array)($this->_get(self::PAGE_GROUPS) ?? []);
    }

    /**
     * Set the placements of the instance.
     *
     * @param \Magendoo\WidgetInstanceApi\Api\Data\PageGroupInterface[] $pageGroups
     * @return $this
     */
    public function setPageGroups(array $pageGroups)
    {
        return $this->setData(self::PAGE_GROUPS, $pageGroups);
    }
}
