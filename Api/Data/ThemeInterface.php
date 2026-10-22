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
     * @return int
     */
    public function getThemeId();

    /**
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
     * @param string $code
     * @return $this
     */
    public function setCode($code);
}
