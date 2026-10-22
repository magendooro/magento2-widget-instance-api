<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Api\Data;

/**
 * @api
 */
interface WidgetInstanceSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceInterface[]
     */
    public function getItems();

    /**
     * @param \Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
