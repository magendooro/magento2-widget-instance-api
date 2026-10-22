<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Model\Data;

use Magendoo\WidgetInstanceApi\Api\Data\ThemeInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Data object of a frontend theme: its ID and its path.
 */
class Theme extends AbstractSimpleObject implements ThemeInterface
{
    /**
     * Get the theme ID.
     *
     * @return int
     */
    public function getThemeId()
    {
        return (int)$this->_get(self::THEME_ID);
    }

    /**
     * Set the theme ID.
     *
     * @param int $themeId
     * @return $this
     */
    public function setThemeId($themeId)
    {
        return $this->setData(self::THEME_ID, (int)$themeId);
    }

    /**
     * Get the theme path, for example Magento/luma.
     *
     * @return string
     */
    public function getCode()
    {
        return (string)$this->_get(self::CODE);
    }

    /**
     * Set the theme path.
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, (string)$code);
    }
}
