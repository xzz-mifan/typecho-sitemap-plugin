<?php

define('__TYPECHO_PLUGIN_SITEMAP_VERSION__', '2.0.3');

/**
 * <strong style="color:#28B7FF;font-family: 楷体;">生成Sitemap符合Google和百度标准</strong>
 *
 * @package    Sitemap
 * @author     <strong style="color:#28B7FF;font-family: 楷体;">lmcc</strong>
 * @version    2.0.3
 * @dependence 17.10.30-*
 * @link       https://www.lmcc.top/
 */
class Sitemap_Plugin implements Typecho_Plugin_Interface
{
    private static $routes = [
        ['name' => 'action', 'url' => 'sitemap'],
        ['name' => 'tags', 'url' => 'tags'],
        ['name' => 'category', 'url' => 'category'],
    ];

    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     */
    public static function activate()
    {
        foreach (self::$routes as $route) {
            Helper::addRoute("sitemap_{$route['name']}_route", "/{$route['url']}.xml", 'Sitemap_Action', $route['name']);
        }
        // 更新提示
        Typecho_Plugin::factory('admin/menu.php')->navBar = array(__CLASS__, 'updateTip');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     */
    public static function deactivate()
    {
        foreach (self::$routes as $route) {
            Helper::removeRoute("sitemap_{$route['name']}_route");
        }
    }

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $updateTip = new Typecho_Widget_Helper_Form_Element_Radio('updateTip',
            array(
                '1' => '是',
                '0' => '否'
            ), '1', _t('接收更新提示'));
        $form->addInput($updateTip);
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    /**
     * 更新提示
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function updateTip()
    {
        $option = Helper::options()->plugin('Sitemap');
        if ($option->updateTip == 1) {
            $date = new Typecho_Date();
            $date = $date->timeStamp;
            $data = file_get_contents(__DIR__ . '/cache/version.json');
            if ($data) {
                $data = json_decode($data, true);
                if ($date - $data['time'] < 86400) {
                    if ($data['version'] != __TYPECHO_PLUGIN_SITEMAP_VERSION__) {
                        echo '<a href="https://github.com/xzz-mifan/typecho-sitemap-plugin/releases">Sitemap插件有更新</a>';
                        return;
                    } else {
                        return;
                    }
                }
            }
            //
            $tag  = self::getNewRelease();
            $data = json_encode(array(
                "version" => $tag,
                "time"    => $date
            ));
            file_put_contents(__DIR__ . '/cache/version.json', $data);
            if ($tag != __TYPECHO_PLUGIN_SITEMAP_VERSION__) {
                echo '<a href="https://github.com/xzz-mifan/typecho-sitemap-plugin/releases">Sitemap插件有更新</a>';
                return;
            }
            return;
        }
    }

    /**
     * 获取 Github 最新 Release Tag 版本
     *
     * @access private
     * @return string
     */
    private static function getNewRelease()
    {
        $ch = curl_init("https://api.github.com/repos/xzz-mifan/typecho-sitemap-plugin/releases/latest");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "typecho-sitemap-plugin");
        $res  = curl_exec($ch);
        $data = json_decode($res, JSON_UNESCAPED_UNICODE);
        return $data['tag_name'];
    }

}
