<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Api;

use Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceInterface;
use Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Creates, updates and reads widget instances.
 *
 * @api
 */
interface WidgetInstanceRepositoryInterface
{
    /**
     * Create a widget instance, or update the one whose instance_id the body carries.
     *
     * @param \Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceInterface $widget
     * @return \Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(WidgetInstanceInterface $widget);

    /**
     * Get a widget instance by its ID.
     *
     * @param int $instanceId
     * @return \Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($instanceId);

    /**
     * Get the widget instances that match the search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
