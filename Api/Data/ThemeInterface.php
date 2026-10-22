<?php
declare(strict_types=1);

namespace Magendoo\WidgetInstanceApi\Api\Data;

/**
 * A frontend theme a widget instance can be assigned to.
 *
 * @api
 */
interface ThemeInterface
{
    public const THEME_ID = 'theme_id';
    public const CODE = 'code';

    /**
     * Get the theme ID.
     *
     * @return int
     */
    public function getThemeId();

    /**
     * Set the theme ID.
     *
     * @param int $themeId
     * @return $this
     */
    public function setThemeId($themeId);

    /**
     * The theme path, for example Magento/luma: stable across installations where ids are not.
     *
     * @return string
     */
    public function getCode();

    /**
     * Set the theme path.
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code);
}
