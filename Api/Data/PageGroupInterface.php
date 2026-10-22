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
     * @return string
     */
    public function getPageGroup();

    /**
     * @param string $pageGroup
     * @return $this
     */
    public function setPageGroup($pageGroup);

    /**
     * @return string
     */
    public function getLayoutHandle();

    /**
     * @param string $layoutHandle
     * @return $this
     */
    public function setLayoutHandle($layoutHandle);

    /**
     * @return string
     */
    public function getBlockReference();

    /**
     * @param string $blockReference
     * @return $this
     */
    public function setBlockReference($blockReference);

    /**
     * @return string
     */
    public function getPageFor();

    /**
     * @param string $pageFor
     * @return $this
     */
    public function setPageFor($pageFor);

    /**
     * @return string
     */
    public function getEntities();

    /**
     * @param string $entities
     * @return $this
     */
    public function setEntities($entities);

    /**
     * @return string
     */
    public function getPageTemplate();

    /**
     * @param string $pageTemplate
     * @return $this
     */
    public function setPageTemplate($pageTemplate);
}
