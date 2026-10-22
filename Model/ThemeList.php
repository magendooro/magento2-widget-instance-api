<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Model;

use Magendoo\WidgetInstanceApi\Api\Data\ThemeInterfaceFactory;
use Magendoo\WidgetInstanceApi\Api\ThemeListInterface;
use Magento\Framework\App\Area;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory;

/**
 * Lists the frontend themes, ordered by ID, with the stable theme path as their code.
 */
class ThemeList implements ThemeListInterface
{
    /**
     * @param CollectionFactory $themeCollectionFactory
     * @param ThemeInterfaceFactory $themeFactory
     */
    public function __construct(
        private readonly CollectionFactory $themeCollectionFactory,
        private readonly ThemeInterfaceFactory $themeFactory
    ) {
    }

    /**
     * Get the frontend themes widget instances can be assigned to.
     *
     * @return \Magendoo\WidgetInstanceApi\Api\Data\ThemeInterface[]
     */
    public function getThemes()
    {
        $collection = $this->themeCollectionFactory->create();
        $collection->addAreaFilter(Area::AREA_FRONTEND)->setOrder('theme_id', 'ASC');
        $themes = [];
        foreach ($collection as $theme) {
            $themes[] = $this->themeFactory->create()
                ->setThemeId((int)$theme->getId())
                ->setCode((string)$theme->getThemePath());
        }
        return $themes;
    }
}
