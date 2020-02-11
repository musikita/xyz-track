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
            $link = $menu['url'] ?? null;
            if (isset($menu['route'])) {
                $link = route($menu['route']);
            }
            $icon = $menu['icon'] ?? null;
            $linkAttributes = $menu['link_attributes'] ?? '';
            list($subMenus, $activedSubMenu) = $this->process($format, $menu['submenus'] ?? []);
            $url = url()->current();
            $isActive = ($link !== null && $url == ($link = url($link))) || $activedSubMenu;
            if (($menu['descend'] ?? true) && $link !== null & strpos($url, $link) === 0) {
                $isActive = true;
            }
            if ($isActive) {
                $hasActive = true;
            }
            $result .= $format($link ?? 'javascript:void(0)', $title, $icon, $linkAttributes, $isActive, $subMenus)."\n";
        }

        return [$result, $hasActive];
    }

    public function output($name, $format = null)
    {
        $menus = $this->config[$name];
        $format = $format ?? $menus['format'];
        list($output) = $this->process($format, $menus['items']);

        return $output;
    }
}
