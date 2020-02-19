<?php

namespace Suitcorecms\Menus;

class Menu
{
    protected $config;

    public function __construct($config = null)
    {
        $this->config = $config ?? config('suitcorecms.menus');
    }

    protected function process($format, $menus = [])
    {
        $result = false;
        $hasActive = false;
        foreach ($menus as $title => $menu) {
            if (!is_array($menu)) {
                $menu = ['url' => $menu];
            }
            $link = $menu['url'] ?? null;
            if (isset($menu['route'])) {
                $link = route($menu['route']);
            }
            $icon = $menu['icon'] ?? null;
            $linkAttributes = $menu['link_attributes'] ?? '';
            list($subMenus, $activedSubMenu) = $this->process($format, $menu['submenus'] ?? []);
            $isActive = $activedSubMenu;
            if (!$isActive && $link) {
                $link = url($link);
                $isActive = $this->isActive($link, $menu['descend'] ?? true);
                if ($isActive) {
                    $hasActive = true;
                }
            }
            $result .= $format($link ?? 'javascript:void(0)', $title, $icon, $linkAttributes, $isActive, $subMenus)."\n";
        }

        return [$result, $hasActive];
    }

    protected function isActive($link, $restPath = false)
    {
        $url = url()->current();
        $isActive = $url == $link;
        if ($restPath) {
            $isActive = strpos($url, $link) === 0;
        }

        return $isActive;
    }

    public function output($name, $format = null)
    {
        $menus = $this->config[$name];
        $format = $format ?? $menus['format'];
        list($output) = $this->process($format, $menus['items']);

        return $output;
    }

    public static function factory($items, $format)
    {
        $instance = new static();
        list($output) = $instance->process($format, $items);

        return $output;
    }
}
