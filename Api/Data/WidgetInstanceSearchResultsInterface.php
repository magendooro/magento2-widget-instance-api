<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Api\Data;

/**
 * Search results of widget instances.
 *
 * @api
 */
interface WidgetInstanceSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get the widget instances of the result page.
     *
     * @return \Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceInterface[]
     */
    public function getItems();

    /**
     * Set the widget instances of the result page.
     *
     * @param \Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
