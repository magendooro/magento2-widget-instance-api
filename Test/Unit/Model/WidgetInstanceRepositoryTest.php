<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Test\Unit\Model;

use Magendoo\WidgetInstanceApi\Api\Data\PageGroupInterfaceFactory;
use Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceInterfaceFactory;
use Magendoo\WidgetInstanceApi\Api\Data\WidgetInstanceSearchResultsInterfaceFactory;
use Magendoo\WidgetInstanceApi\Model\Data\PageGroup;
use Magendoo\WidgetInstanceApi\Model\Data\WidgetInstance;
use Magendoo\WidgetInstanceApi\Model\WidgetInstanceRepository;
use Magendoo\WidgetInstanceApi\Model\WidgetInstanceSearchResults;
use Magendoo\WidgetInstanceApi\Model\WidgetInstanceValidator;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Widget\Model\ResourceModel\Widget\Instance as InstanceResource;
use Magento\Widget\Model\ResourceModel\Widget\Instance\Collection;
use Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory;
use Magento\Widget\Model\Widget\Instance;
use Magento\Widget\Model\Widget\InstanceFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(WidgetInstanceRepository::class)]
class WidgetInstanceRepositoryTest extends TestCase
{
    private const WIDGET_TYPE = 'Magento\Cms\Block\Widget\Block';

    /**
     * @var InstanceResource&MockObject
     */
    private MockObject $resource;

    /**
     * @var WidgetInstanceValidator&MockObject
     */
    private MockObject $validator;

    protected function setUp(): void
    {
        $this->resource = $this->createMock(InstanceResource::class);
        $this->validator = $this->createMock(WidgetInstanceValidator::class);
    }

    public function testSaveCreatesInstanceThroughTheInstanceModel(): void
    {
        $model = $this->instanceModel();
        $nested = null;
        $widget = $this->widget()->setPageGroups([
            $this->pageGroup('pages', 'cms_index_index', 'content', 'specific', '1,2', 'widget/block.phtml'),
        ]);
        $this->validator->expects($this->once())->method('validate')->with($widget);
        $this->resource->expects($this->once())->method('save')->with($this->identicalTo($model))->willReturnCallback(
            function (Instance $saved) use (&$nested): InstanceResource {
                $nested = $saved->getData('page_groups');
                $saved->setData('instance_id', 7);
                return $this->resource;
            }
        );
        $reload = function (Instance $loaded) use ($model): InstanceResource {
            $loaded->setData('page_groups', [[
                'page_group' => 'pages',
                'layout_handle' => 'cms_index_index',
                'block_reference' => 'content',
                'page_for' => 'specific',
                'entities' => '1,2',
                'page_template' => 'widget/block.phtml',
            ]]);
            $this->assertSame($model, $loaded);
            return $this->resource;
        };
        $this->resource->expects($this->once())
            ->method('load')
            ->with($this->identicalTo($model), 7)
            ->willReturnCallback($reload);

        $result = $this->repository($model)->save($widget);

        $this->assertSame(self::WIDGET_TYPE, $model->getType());
        $this->assertSame(3, $model->getThemeId());
        $this->assertSame('Home banner', $model->getTitle());
        $this->assertSame([0, 1], $model->getStoreIds());
        $this->assertSame(2, $model->getSortOrder());
        $this->assertSame(['block_id' => 'home'], $model->getWidgetParameters());
        $this->assertSame([[
            'page_group' => 'pages',
            'pages' => [
                'page_id' => 0,
                'layout_handle' => 'cms_index_index',
                'block' => 'content',
                'for' => 'specific',
                'entities' => '1,2',
                'template' => 'widget/block.phtml',
            ],
        ]], $nested);
        $this->assertSame(7, $result->getInstanceId());
        $this->assertSame(self::WIDGET_TYPE, $result->getInstanceType());
        $this->assertSame(3, $result->getThemeId());
        $this->assertSame('Home banner', $result->getTitle());
        $this->assertSame([0, 1], $result->getStoreIds());
        $this->assertSame(2, $result->getSortOrder());
        $this->assertSame(['block_id' => 'home'], $result->getWidgetParameters());
        $this->assertCount(1, $result->getPageGroups());
        $this->assertSame('pages', $result->getPageGroups()[0]->getPageGroup());
        $this->assertSame('cms_index_index', $result->getPageGroups()[0]->getLayoutHandle());
        $this->assertSame('content', $result->getPageGroups()[0]->getBlockReference());
        $this->assertSame('specific', $result->getPageGroups()[0]->getPageFor());
        $this->assertSame('1,2', $result->getPageGroups()[0]->getEntities());
        $this->assertSame('widget/block.phtml', $result->getPageGroups()[0]->getPageTemplate());
    }

    public function testSaveUpdatesTheLoadedInstanceAndDefaultsPageForToAll(): void
    {
        $model = $this->instanceModel(5);
        $nested = null;
        $widget = $this->widget()
            ->setInstanceId(5)
            ->setPageGroups([$this->pageGroup('all_pages', '', 'main.content', '', '', '')]);
        $this->validator->expects($this->once())->method('validate')->with($widget);
        $this->resource->expects($this->atLeastOnce())->method('load')->with($model, 5);
        $this->resource->expects($this->once())->method('save')->with($model)->willReturnCallback(
            function (Instance $saved) use (&$nested): InstanceResource {
                $nested = $saved->getData('page_groups');
                return $this->resource;
            }
        );

        $result = $this->repository($model)->save($widget);

        $this->assertSame(5, $result->getInstanceId());
        $this->assertSame('Home banner', $model->getTitle());
        $this->assertSame([[
            'page_group' => 'all_pages',
            'all_pages' => [
                'page_id' => 0,
                'layout_handle' => '',
                'block' => 'main.content',
                'for' => 'all',
                'entities' => '',
                'template' => '',
            ],
        ]], $nested);
    }

    public function testSaveWithEmptyParametersAndPageGroupsStoresEmptyArrays(): void
    {
        $model = $this->instanceModel(5);
        $widget = $this->widget()->setInstanceId(5)->setWidgetParameters([])->setPageGroups([]);
        $this->validator->expects($this->once())->method('validate');
        $this->resource->expects($this->atLeastOnce())->method('load');
        $this->resource->expects($this->once())->method('save')->with($model);

        $result = $this->repository($model)->save($widget);

        $this->assertSame([], $model->getWidgetParameters());
        $this->assertSame([], $result->getPageGroups());
        $this->assertSame([], $result->getWidgetParameters());
    }

    public function testSaveConvertsObjectParametersToArrays(): void
    {
        $model = $this->instanceModel(5);
        $parameters = new \stdClass();
        $parameters->block_id = 'home';
        $parameters->nested = new \stdClass();
        $parameters->nested->key = 'value';
        $widget = $this->widget()->setInstanceId(5)->setWidgetParameters($parameters);
        $this->validator->expects($this->once())->method('validate');
        $this->resource->expects($this->atLeastOnce())->method('load');
        $this->resource->expects($this->once())->method('save');

        $this->repository($model)->save($widget);

        $this->assertSame(['block_id' => 'home', 'nested' => ['key' => 'value']], $model->getWidgetParameters());
    }

    public function testSaveOfMissingInstanceIdPersistsNothing(): void
    {
        $widget = $this->widget()->setInstanceId(99);
        $this->validator->expects($this->once())->method('validate');
        $this->resource->expects($this->once())->method('load');
        $this->resource->expects($this->never())->method('save');

        $this->expectException(CouldNotSaveException::class);
        $this->expectExceptionMessage('"99"');

        $this->repository($this->instanceModel())->save($widget);
    }

    public function testSaveRejectedByTheValidatorPersistsNothing(): void
    {
        $widget = $this->widget();
        $this->validator->expects($this->once())->method('validate')->with($widget)->willThrowException(
            new CouldNotSaveException(__('Could not save the widget instance: page_group "bogus" is not valid.'))
        );
        $this->resource->expects($this->never())->method('load');
        $this->resource->expects($this->never())->method('save');

        $this->expectException(CouldNotSaveException::class);
        $this->expectExceptionMessage('page_group');

        $this->repository($this->instanceModel())->save($widget);
    }

    #[DataProvider('resourceFailureProvider')]
    public function testSaveWrapsResourceFailures(\Exception $failure): void
    {
        $widget = $this->widget();
        $this->validator->expects($this->once())->method('validate');
        $this->resource->expects($this->never())->method('load');
        $this->resource->expects($this->once())->method('save')->willThrowException($failure);

        try {
            $this->repository($this->instanceModel())->save($widget);
        } catch (CouldNotSaveException $exception) {
            $this->assertStringContainsString('Duplicate entry', $exception->getMessage());
            $this->assertSame($failure, $exception->getPrevious());
            return;
        }
        $this->fail('Expected a CouldNotSaveException.');
    }

    public function testGetByIdReturnsTheInstance(): void
    {
        $model = $this->instanceModel(5);
        $this->validator->expects($this->never())->method('validate');
        $this->resource->expects($this->once())->method('load')->with($model, 5);

        $result = $this->repository($model)->getById(5);

        $this->assertSame(5, $result->getInstanceId());
        $this->assertSame(self::WIDGET_TYPE, $result->getInstanceType());
        $this->assertSame([], $result->getPageGroups());
    }

    public function testGetByIdReturnsEmptyParametersAsAnEmptyArray(): void
    {
        $model = $this->instanceModel(5);
        $model->setData('widget_parameters', null);
        $this->resource->expects($this->once())->method('load')->with($model, 5);

        $result = $this->repository($model)->getById(5);

        $this->assertSame([], $result->getWidgetParameters());
    }

    public function testGetByIdOfUnknownInstanceThrows(): void
    {
        $this->validator->expects($this->never())->method('validate');
        $this->resource->expects($this->once())->method('load');

        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('"42"');

        $this->repository($this->instanceModel())->getById(42);
    }

    public function testGetListReturnsEveryInstanceOfTheCollection(): void
    {
        $model = $this->instanceModel(5);
        $searchCriteria = $this->createStub(SearchCriteriaInterface::class);
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())->method('getItems')->willReturn([$model]);
        $collection->expects($this->once())->method('getSize')->willReturn(12);
        $processor = $this->createMock(CollectionProcessorInterface::class);
        $processor->expects($this->once())->method('process')->with(
            $this->identicalTo($searchCriteria),
            $this->identicalTo($collection)
        );
        $this->validator->expects($this->never())->method('validate');
        $this->resource->expects($this->once())->method('load')->with($model, 5);

        $results = $this->repository($model, $collection, $processor)->getList($searchCriteria);

        $this->assertSame($searchCriteria, $results->getSearchCriteria());
        $this->assertSame(12, $results->getTotalCount());
        $this->assertCount(1, $results->getItems());
        $this->assertSame(5, $results->getItems()[0]->getInstanceId());
    }

    public function testGetListOfAnEmptyCollectionReturnsNoItems(): void
    {
        $searchCriteria = $this->createStub(SearchCriteriaInterface::class);
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())->method('getItems')->willReturn([]);
        $collection->expects($this->once())->method('getSize')->willReturn(0);
        $processor = $this->createMock(CollectionProcessorInterface::class);
        $processor->expects($this->once())->method('process');
        $this->validator->expects($this->never())->method('validate');
        $this->resource->expects($this->never())->method('load');

        $results = $this->repository($this->instanceModel(), $collection, $processor)->getList($searchCriteria);

        $this->assertSame([], $results->getItems());
        $this->assertSame(0, $results->getTotalCount());
    }

    /**
     * @return array<string, array{\Exception}>
     */
    public static function resourceFailureProvider(): array
    {
        return [
            'localized exception' => [new LocalizedException(__('Duplicate entry'))],
            'database exception' => [new \RuntimeException('Duplicate entry')],
        ];
    }

    private function repository(
        Instance $model,
        ?Collection $collection = null,
        ?CollectionProcessorInterface $processor = null
    ): WidgetInstanceRepository {
        $instanceFactory = $this->createStub(InstanceFactory::class);
        $instanceFactory->method('create')->willReturn($model);
        $collectionFactory = $this->createStub(CollectionFactory::class);
        $collectionFactory->method('create')->willReturn($collection);
        $widgetFactory = $this->createStub(WidgetInstanceInterfaceFactory::class);
        $widgetFactory->method('create')->willReturnCallback(static fn (): WidgetInstance => new WidgetInstance());
        $pageGroupFactory = $this->createStub(PageGroupInterfaceFactory::class);
        $pageGroupFactory->method('create')->willReturnCallback(static fn (): PageGroup => new PageGroup());
        $searchResultsFactory = $this->createStub(WidgetInstanceSearchResultsInterfaceFactory::class);
        $searchResultsFactory->method('create')->willReturnCallback(
            static fn (): WidgetInstanceSearchResults => new WidgetInstanceSearchResults()
        );

        return new WidgetInstanceRepository(
            $instanceFactory,
            $this->resource,
            $collectionFactory,
            $processor ?? $this->createStub(CollectionProcessorInterface::class),
            $widgetFactory,
            $pageGroupFactory,
            $searchResultsFactory,
            $this->validator,
            new Json()
        );
    }

    /**
     * A real Instance model without the framework constructor; with an ID it stands for a stored instance.
     *
     * @param int|null $instanceId
     * @return Instance
     */
    private function instanceModel(?int $instanceId = null): Instance
    {
        $model = new class extends Instance {
            public function __construct()
            {
                $this->_idFieldName = 'instance_id';
            }
        };
        if ($instanceId !== null) {
            $model->setData('instance_id', $instanceId);
            $model->setType(self::WIDGET_TYPE);
            $model->setTitle('Stored title');
        }
        return $model;
    }

    private function widget(): WidgetInstance
    {
        $widget = new WidgetInstance();
        $widget->setInstanceType(self::WIDGET_TYPE)
            ->setThemeId(3)
            ->setTitle('Home banner')
            ->setStoreIds([0, 1])
            ->setSortOrder(2)
            ->setWidgetParameters(['block_id' => 'home']);
        return $widget;
    }

    private function pageGroup(
        string $name,
        string $layoutHandle,
        string $blockReference,
        string $pageFor,
        string $entities,
        string $template
    ): PageGroup {
        $group = new PageGroup();
        $group->setPageGroup($name)
            ->setLayoutHandle($layoutHandle)
            ->setBlockReference($blockReference)
            ->setPageFor($pageFor)
            ->setEntities($entities)
            ->setPageTemplate($template);
        return $group;
    }
}
