<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Model\Data;

use Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class WidgetInstance extends AbstractSimpleObject implements WidgetInstanceInterface
{
    public function getInstanceId()
    {
        $id = $this->_get(self::INSTANCE_ID);
        return $id === null ? null : (int)$id;
    }

    public function setInstanceId($instanceId)
    {
        return $this->setData(self::INSTANCE_ID, $instanceId === null ? null : (int)$instanceId);
    }

    public function getInstanceType()
    {
        return (string)$this->_get(self::INSTANCE_TYPE);
    }

    public function setInstanceType($instanceType)
    {
        return $this->setData(self::INSTANCE_TYPE, (string)$instanceType);
    }

    public function getThemeId()
    {
        return (int)$this->_get(self::THEME_ID);
    }

    public function setThemeId($themeId)
    {
        return $this->setData(self::THEME_ID, (int)$themeId);
    }

    public function getTitle()
    {
        return (string)$this->_get(self::TITLE);
    }

    public function setTitle($title)
    {
        return $this->setData(self::TITLE, (string)$title);
    }

    public function getStoreIds()
    {
        return array_map('intval', (array)($this->_get(self::STORE_IDS) ?? []));
    }

    public function setStoreIds(array $storeIds)
    {
        return $this->setData(self::STORE_IDS, array_map('intval', $storeIds));
    }

    public function getSortOrder()
    {
        return (int)$this->_get(self::SORT_ORDER);
    }

    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, (int)$sortOrder);
    }

    public function getWidgetParameters()
    {
        $parameters = $this->_get(self::WIDGET_PARAMETERS);
        // An empty parameter set is an object on the wire, never a list.
        return $parameters === null || $parameters === [] ? new \stdClass() : $parameters;
    }

    public function setWidgetParameters($widgetParameters)
    {
        return $this->setData(self::WIDGET_PARAMETERS, $widgetParameters);
    }

    public function getPageGroups()
    {
        return (array)($this->_get(self::PAGE_GROUPS) ?? []);
    }

    public function setPageGroups(array $pageGroups)
    {
        return $this->setData(self::PAGE_GROUPS, $pageGroups);
    }
}
