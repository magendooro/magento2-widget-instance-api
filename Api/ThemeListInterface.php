<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Api;

/**
 * @api
 */
interface ThemeListInterface
{
    /**
     * The frontend themes widget instances can be assigned to.
     *
     * @return \Magendoo\WidgetInstanceApi\Api\Data\ThemeInterface[]
     */
    public function getThemes();
}
