<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Model\Data;

use Magendoo\WidgetInstanceApi\Api\Data\ThemeInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class Theme extends AbstractSimpleObject implements ThemeInterface
{
    public function getThemeId()
    {
        return (int)$this->_get(self::THEME_ID);
    }

    public function setThemeId($themeId)
    {
        return $this->setData(self::THEME_ID, (int)$themeId);
    }

    public function getCode()
    {
        return (string)$this->_get(self::CODE);
    }

    public function setCode($code)
    {
        return $this->setData(self::CODE, (string)$code);
    }
}
