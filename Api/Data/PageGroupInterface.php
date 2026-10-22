<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Api\Data;

/**
 * One placement of a widget instance: a page group with its layout handle and block reference.
 *
 * Mirrors the columns of widget_instance_page without the row and instance ids, so the same
 * placement reads identically on two installations.
 *
 * @api
 */
interface PageGroupInterface
{
    public const PAGE_GROUP = 'page_group';
    public const LAYOUT_HANDLE = 'layout_handle';
    public const BLOCK_REFERENCE = 'block_reference';
    public const PAGE_FOR = 'page_for';
    public const ENTITIES = 'entities';
    public const PAGE_TEMPLATE = 'page_template';

    /**
     * Get the page group, for example all_pages or anchor_categories.
     *
     * @return string
     */
    public function getPageGroup();

    /**
     * Set the page group.
     *
     * @param string $pageGroup
     * @return $this
     */
    public function setPageGroup($pageGroup);

    /**
     * Get the layout handle the widget is added to.
     *
     * @return string
     */
    public function getLayoutHandle();

    /**
     * Set the layout handle the widget is added to.
     *
     * @param string $layoutHandle
     * @return $this
     */
    public function setLayoutHandle($layoutHandle);

    /**
     * Get the name of the block the widget is placed in.
     *
     * @return string
     */
    public function getBlockReference();

    /**
     * Set the name of the block the widget is placed in.
     *
     * @param string $blockReference
     * @return $this
     */
    public function setBlockReference($blockReference);

    /**
     * Get whether the placement applies to all entities or to specific ones: all or specific.
     *
     * @return string
     */
    public function getPageFor();

    /**
     * Set whether the placement applies to all entities or to specific ones: all or specific.
     *
     * @param string $pageFor
     * @return $this
     */
    public function setPageFor($pageFor);

    /**
     * Get the comma-separated IDs of the specific entities.
     *
     * @return string
     */
    public function getEntities();

    /**
     * Set the comma-separated IDs of the specific entities.
     *
     * @param string $entities
     * @return $this
     */
    public function setEntities($entities);

    /**
     * Get the template the widget block renders with.
     *
     * @return string
     */
    public function getPageTemplate();

    /**
     * Set the template the widget block renders with.
     *
     * @param string $pageTemplate
     * @return $this
     */
    public function setPageTemplate($pageTemplate);
}
