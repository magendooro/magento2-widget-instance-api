<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Model;

use Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

/**
 * Service Data Object with widget instance search results.
 */
class WidgetInstanceSearchResults extends SearchResults implements WidgetInstanceSearchResultsInterface
{
}
