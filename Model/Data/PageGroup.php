<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Model\Data;

use Magendoo\WidgetInstanceApi\Api\Data\PageGroupInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class PageGroup extends AbstractSimpleObject implements PageGroupInterface
{
    public function getPageGroup()
    {
        return (string)$this->_get(self::PAGE_GROUP);
    }

    public function setPageGroup($pageGroup)
    {
        return $this->setData(self::PAGE_GROUP, (string)$pageGroup);
    }

    public function getLayoutHandle()
    {
        return (string)$this->_get(self::LAYOUT_HANDLE);
    }

    public function setLayoutHandle($layoutHandle)
    {
        return $this->setData(self::LAYOUT_HANDLE, (string)$layoutHandle);
    }

    public function getBlockReference()
    {
        return (string)$this->_get(self::BLOCK_REFERENCE);
    }

    public function setBlockReference($blockReference)
    {
        return $this->setData(self::BLOCK_REFERENCE, (string)$blockReference);
    }

    public function getPageFor()
    {
        return (string)$this->_get(self::PAGE_FOR);
    }

    public function setPageFor($pageFor)
    {
        return $this->setData(self::PAGE_FOR, (string)$pageFor);
    }

    public function getEntities()
    {
        return (string)$this->_get(self::ENTITIES);
    }

    public function setEntities($entities)
    {
        return $this->setData(self::ENTITIES, (string)$entities);
    }

    public function getPageTemplate()
    {
        return (string)$this->_get(self::PAGE_TEMPLATE);
    }

    public function setPageTemplate($pageTemplate)
    {
        return $this->setData(self::PAGE_TEMPLATE, (string)$pageTemplate);
    }
}
