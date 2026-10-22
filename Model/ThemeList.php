<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Model;

use Magendoo\WidgetInstanceApi\Api\Data\ThemeInterfaceFactory;
use Magendoo\WidgetInstanceApi\Api\ThemeListInterface;
use Magento\Framework\App\Area;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory;

class ThemeList implements ThemeListInterface
{
    public function __construct(
        private readonly CollectionFactory $themeCollectionFactory,
        private readonly ThemeInterfaceFactory $themeFactory
    ) {
    }

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
