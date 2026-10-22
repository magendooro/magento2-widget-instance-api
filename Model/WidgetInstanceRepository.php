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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Widget\Model\ResourceModel\Widget\Instance as InstanceResource;
use Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory;
use Magento\Widget\Model\Widget\Instance;
use Magento\Widget\Model\Widget\InstanceFactory;

/**
 * Reads and writes widget instances through Magento's own Instance model, so a save through the
 * API produces exactly the page rows and layout updates the admin form would.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class WidgetInstanceRepository implements WidgetInstanceRepositoryInterface
{
    /**
     * @param InstanceFactory $instanceFactory
     * @param InstanceResource $resource
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param WidgetInstanceInterfaceFactory $widgetFactory
     * @param PageGroupInterfaceFactory $pageGroupFactory
     * @param WidgetInstanceSearchResultsInterfaceFactory $searchResultsFactory
     * @param WidgetInstanceValidator $validator
     * @param Json $json
     */
    public function __construct(
        private readonly InstanceFactory $instanceFactory,
        private readonly InstanceResource $resource,
        private readonly CollectionFactory $collectionFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly WidgetInstanceInterfaceFactory $widgetFactory,
        private readonly PageGroupInterfaceFactory $pageGroupFactory,
        private readonly WidgetInstanceSearchResultsInterfaceFactory $searchResultsFactory,
        private readonly WidgetInstanceValidator $validator,
        private readonly Json $json
    ) {
    }

    /**
     * Create a widget instance, or update the one whose instance_id the body carries.
     *
     * @param WidgetInstanceInterface $widget
     * @return WidgetInstanceInterface
     * @throws CouldNotSaveException
     */
    public function save(WidgetInstanceInterface $widget)
    {
        $this->validator->validate($widget);
        $instanceId = $widget->getInstanceId();
        if ($instanceId) {
            $model = $this->load($instanceId);
            if (!$model->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Could not save the widget instance: the widget instance with the "%1" ID doesn\'t exist.',
                        $instanceId
                    )
                );
            }
        } else {
            $model = $this->instanceFactory->create();
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
        } catch (\Exception $exception) {
            // The resource may fail with a localized or a database exception; both are save failures.
            throw new CouldNotSaveException(
                __('Could not save the widget instance: %1', $exception->getMessage()),
                $exception
            );
        }
        return $this->toApi($this->load((int)$model->getId()));
    }

    /**
     * Get a widget instance by its ID.
     *
     * @param int $instanceId
     * @return WidgetInstanceInterface
     * @throws NoSuchEntityException
     */
    public function getById($instanceId)
    {
        $model = $this->load((int)$instanceId);
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('The widget instance with the "%1" ID doesn\'t exist.', $instanceId));
        }
        return $this->toApi($model);
    }

    /**
     * Get the widget instances that match the search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceSearchResultsInterface
     */
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

    /**
     * Load the Instance model with its page rows.
     *
     * @param int $instanceId
     * @return Instance
     */
    private function load(int $instanceId): Instance
    {
        $model = $this->instanceFactory->create();
        $this->resource->load($model, $instanceId);
        return $model;
    }

    /**
     * Convert the Instance model to the API data object.
     *
     * @param Instance $model
     * @return WidgetInstanceInterface
     */
    private function toApi(Instance $model): WidgetInstanceInterface
    {
        $pageGroups = [];
        foreach ((array)$model->getData('page_groups') as $row) {
            $pageGroups[] = $this->pageGroupFactory->create()
                ->setPageGroup((string)($row[PageGroupInterface::PAGE_GROUP] ?? ''))
                ->setLayoutHandle((string)($row[PageGroupInterface::LAYOUT_HANDLE] ?? ''))
                ->setBlockReference((string)($row[PageGroupInterface::BLOCK_REFERENCE] ?? ''))
                ->setPageFor((string)($row[PageGroupInterface::PAGE_FOR] ?? ''))
                ->setEntities((string)($row[PageGroupInterface::ENTITIES] ?? ''))
                ->setPageTemplate((string)($row[PageGroupInterface::PAGE_TEMPLATE] ?? ''));
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
     *
     * @param mixed $parameters
     * @return array<string, mixed>
     */
    private function parametersAsArray(mixed $parameters): array
    {
        if ($parameters instanceof \stdClass) {
            $parameters = (array)$parameters;
        }
        if (!is_array($parameters)) {
            return [];
        }
        return $this->json->unserialize($this->json->serialize($parameters)) ?: [];
    }

    /**
     * The flat page rows of the API, in the nested shape the admin form posts and
     * Instance::beforeSave() expects. page_id 0 means every existing row is replaced.
     *
     * @param PageGroupInterface[] $pageGroups
     * @return array<int, array<string, mixed>>
     */
    private function pageGroupsForModel(array $pageGroups): array
    {
        $nested = [];
        foreach ($pageGroups as $group) {
            $name = $group->getPageGroup();
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
