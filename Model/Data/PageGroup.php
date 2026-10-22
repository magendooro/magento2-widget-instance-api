<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Model\Data;

use Magendoo\WidgetInstanceApi\Api\Data\PageGroupInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Data object of one widget placement; every value is a string, as widget_instance_page stores it.
 */
class PageGroup extends AbstractSimpleObject implements PageGroupInterface
{
    /**
     * Get the page group.
     *
     * @return string
     */
    public function getPageGroup()
    {
        return (string)$this->_get(self::PAGE_GROUP);
    }

    /**
     * Set the page group.
     *
     * @param string $pageGroup
     * @return $this
     */
    public function setPageGroup($pageGroup)
    {
        return $this->setData(self::PAGE_GROUP, (string)$pageGroup);
    }

    /**
     * Get the layout handle the widget is added to.
     *
     * @return string
     */
    public function getLayoutHandle()
    {
        return (string)$this->_get(self::LAYOUT_HANDLE);
    }

    /**
     * Set the layout handle the widget is added to.
     *
     * @param string $layoutHandle
     * @return $this
     */
    public function setLayoutHandle($layoutHandle)
    {
        return $this->setData(self::LAYOUT_HANDLE, (string)$layoutHandle);
    }

    /**
     * Get the name of the block the widget is placed in.
     *
     * @return string
     */
    public function getBlockReference()
    {
        return (string)$this->_get(self::BLOCK_REFERENCE);
    }

    /**
     * Set the name of the block the widget is placed in.
     *
     * @param string $blockReference
     * @return $this
     */
    public function setBlockReference($blockReference)
    {
        return $this->setData(self::BLOCK_REFERENCE, (string)$blockReference);
    }

    /**
     * Get whether the placement applies to all entities or to specific ones.
     *
     * @return string
     */
    public function getPageFor()
    {
        return (string)$this->_get(self::PAGE_FOR);
    }

    /**
     * Set whether the placement applies to all entities or to specific ones.
     *
     * @param string $pageFor
     * @return $this
     */
    public function setPageFor($pageFor)
    {
        return $this->setData(self::PAGE_FOR, (string)$pageFor);
    }

    /**
     * Get the comma-separated IDs of the specific entities.
     *
     * @return string
     */
    public function getEntities()
    {
        return (string)$this->_get(self::ENTITIES);
    }

    /**
     * Set the comma-separated IDs of the specific entities.
     *
     * @param string $entities
     * @return $this
     */
    public function setEntities($entities)
    {
        return $this->setData(self::ENTITIES, (string)$entities);
    }

    /**
     * Get the template the widget block renders with.
     *
     * @return string
     */
    public function getPageTemplate()
    {
        return (string)$this->_get(self::PAGE_TEMPLATE);
    }

    /**
     * Set the template the widget block renders with.
     *
     * @param string $pageTemplate
     * @return $this
     */
    public function setPageTemplate($pageTemplate)
    {
        return $this->setData(self::PAGE_TEMPLATE, (string)$pageTemplate);
    }
}
