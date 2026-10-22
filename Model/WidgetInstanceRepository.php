<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Model;

use Magendoo\WidgetInstanceApi\Api\Data\PageGroupInterface;
use Magendoo\WidgetInstanceApi\Api\Data\PageGroupInterfaceFactory;
use Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceInterface;
use Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceInterfaceFactory;
use Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceSearchResultsInterfaceFactory;
use Magendoo\WidgetInstanceApi\Api\WidgetInstanceRepositoryInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Widget\Model\ResourceModel\Widget\Instance as InstanceResource;
use Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory;
use Magento\Widget\Model\Widget\Instance;
use Magento\Widget\Model\Widget\InstanceFactory;

/**
 * Reads and writes widget instances through Magento's own Instance model, so a save through the
 * API produces exactly the page rows and layout updates the admin form would.
 */
class WidgetInstanceRepository implements WidgetInstanceRepositoryInterface
{
    /** Columns of widget_instance_page the API exposes, as the row stores them. */
    private const PAGE_COLUMNS = ['page_group', 'layout_handle', 'block_reference', 'page_for', 'entities', 'page_template'];

    public function __construct(
        private readonly InstanceFactory $instanceFactory,
        private readonly InstanceResource $resource,
        private readonly CollectionFactory $collectionFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly WidgetInstanceInterfaceFactory $widgetFactory,
        private readonly PageGroupInterfaceFactory $pageGroupFactory,
        private readonly WidgetInstanceSearchResultsInterfaceFactory $searchResultsFactory
    ) {
    }

    public function save(WidgetInstanceInterface $widget)
    {
        $instanceId = $widget->getInstanceId();
        if ($instanceId) {
            $model = $this->load($instanceId);
            if (!$model->getId()) {
                throw new CouldNotSaveException(
                    __('Could not save the widget instance: the widget instance with the "%1" ID doesn\'t exist.', $instanceId)
                );
            }
        } else {
            $model = $this->instanceFactory->create();
        }
        if ($widget->getInstanceType() === '') {
            throw new CouldNotSaveException(__('Could not save the widget instance: instance_type is required.'));
        }
        $model->setType($widget->getInstanceType());
        $model->setThemeId($widget->getThemeId());
        $model->setTitle($widget->getTitle());
        $model->setStoreIds($widget->getStoreIds());
        $model->setSortOrder($widget->getSortOrder());
        $model->setWidgetParameters($this->parametersAsArray($widget->getWidgetParameters()));
        $model->setData('page_groups', $this->pageGroupsForModel($widget->getPageGroups()));
        try {
            $this->resource->save($model);
        } catch (LocalizedException $exception) {
            throw new CouldNotSaveException(__('Could not save the widget instance: %1', $exception->getMessage()), $exception);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save the widget instance: %1', $exception->getMessage()), $exception);
        }
        return $this->toApi($this->load((int)$model->getId()));
    }

    public function getById($instanceId)
    {
        $model = $this->load((int)$instanceId);
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('The widget instance with the "%1" ID doesn\'t exist.', $instanceId));
        }
        return $this->toApi($model);
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $items = [];
        foreach ($collection->getItems() as $item) {
            // The collection does not load page rows; the resource model does.
            $items[] = $this->toApi($this->load((int)$item->getId()));
        }
        $results = $this->searchResultsFactory->create();
        $results->setSearchCriteria($searchCriteria);
        $results->setItems($items);
        $results->setTotalCount($collection->getSize());
        return $results;
    }

    private function load(int $instanceId): Instance
    {
        $model = $this->instanceFactory->create();
        $this->resource->load($model, $instanceId);
        return $model;
    }

    private function toApi(Instance $model): WidgetInstanceInterface
    {
        $pageGroups = [];
        foreach ((array)$model->getData('page_groups') as $row) {
            $group = $this->pageGroupFactory->create();
            foreach (self::PAGE_COLUMNS as $column) {
                $group->setData($column, (string)($row[$column] ?? ''));
            }
            $pageGroups[] = $group;
        }
        $parameters = $model->getWidgetParameters();
        return $this->widgetFactory->create()
            ->setInstanceId((int)$model->getId())
            ->setInstanceType((string)$model->getType())
            ->setThemeId((int)$model->getThemeId())
            ->setTitle((string)$model->getTitle())
            ->setStoreIds(array_map('intval', array_filter((array)$model->getStoreIds(), 'strlen')))
            ->setSortOrder((int)$model->getSortOrder())
            ->setWidgetParameters(is_array($parameters) ? $parameters : [])
            ->setPageGroups($pageGroups);
    }

    /**
     * The API carries parameters as a free-form object; the model wants an array.
     */
    private function parametersAsArray($parameters): array
    {
        if ($parameters instanceof \stdClass) {
            $parameters = (array)$parameters;
        }
        if (!is_array($parameters)) {
            return [];
        }
        return json_decode(json_encode($parameters), true) ?: [];
    }

    /**
     * The flat page rows of the API, in the nested shape the admin form posts and
     * Instance::beforeSave() expects. page_id 0 means every existing row is replaced.
     *
     * @param PageGroupInterface[] $pageGroups
     */
    private function pageGroupsForModel(array $pageGroups): array
    {
        $nested = [];
        foreach ($pageGroups as $group) {
            $name = $group->getPageGroup();
            if ($name === '') {
                throw new CouldNotSaveException(__('Could not save the widget instance: page_group is required.'));
            }
            $nested[] = [
                'page_group' => $name,
                $name => [
                    'page_id' => 0,
                    'layout_handle' => $group->getLayoutHandle(),
                    'block' => $group->getBlockReference(),
                    'for' => $group->getPageFor() !== '' ? $group->getPageFor() : 'all',
                    'entities' => $group->getEntities(),
                    'template' => $group->getPageTemplate(),
                ],
            ];
        }
        return $nested;
    }
}
