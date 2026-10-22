<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Api;

use Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceInterface;
use Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
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
     * @param int $instanceId
     * @return \Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($instanceId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
