<?php

use Illuminate\Support\Facades\Route;
use Suitcorecms\Datatables\Datatables;

if (!function_exists('cms_url')) {
    function cms_url($uri = '')
    {
        return rtrim($baseUrl = config('suitcorecms.base_domain').'/'.config('suitcorecms.prefix_url'), '/').'/'.ltrim(str_replace($baseUrl, '', $uri), '/');
    }
}

if (!function_exists('cms_asset')) {
    function cms_asset($uri = '')
    {
        return rtrim($baseUrl = config('suitcorecms.asset_url'), '/').'/'.ltrim(str_replace($baseUrl, '', $uri), '/');
    }
}

if (!function_exists('cms_menu')) {
    function cms_menu($name, $format = null)
    {
        $menuList = null;
        if (is_array($name)) {
            $menuList = ['menu' => $name];
            $name = 'menu';
        }
        $menu = new \Suitcorecms\Menus\Menu($menuList);

        return $menu->output($name, $format);
    }
}

if (!function_exists('cms_base_route')) {
    function cms_base_route()
    {
        $route = request()->route()->getName();

        return substr($route, 0, strrpos($route, '.'));
    }
}

if (!function_exists('cms_route_exist')) {
    function cms_route_exist($name)
    {
        $route = cms_base_route().'.'.$name;

        return Route::has($route);
    }
}

if (!function_exists('title_from_breadcrumb')) {
    /**
     * @param $route
     *
     * @return bool
     */
    function title_from_breadcrumb($breadcrumb, $separator = '&raquo;')
    {
        if ($breadcrumb) {
            return $breadcrumb->process(function ($link, $caption) {
                return $caption;
            }, " {$separator} ");
        }

        return null;
    }
}

if (!function_exists('cms_route')) {
    function cms_route($name, array $parameters = [], $reset = false)
    {
        if (!$reset) {
            $parameters = array_merge(request()->query(), $parameters);
        }

        return route(cms_base_route().'.'.$name, $parameters);
    }
}

if (!function_exists('cms_datatables')) {
    function cms_datatables($name = null)
    {
        return Datatables::get($name);
    }
}

if (!function_exists('notification')) {
    function notification($name)
    {
        return (new \Suitcorecms\Notifications\Notification())->{$name}();
    }
}

if (!function_exists('show_cms_flash_message')) {
    function show_cms_flash_message($message)
    {
        $content = $message->message;
        $content = is_callable($content) ? $content() : $content;
        $type = $message->type;
        $type = $type == 'danger' ? 'error' : $type;

        return <<<HTML
            Swal.fire('{$message->title}', '{$content}', '{$type}');
HTML;
    }
}

if (!function_exists('cms_inline_flash_html')) {
    function cms_inline_flash_html()
    {
        return <<<'HTML'
            <div id="cms-alert"></div>
HTML;
    }
}

if (!function_exists('cms_inline_flash_javascript')) {
    function cms_inline_flash_javascript()
    {
        return <<<'HTML'
            var cmsAlert = function (content, type) {
                var className = 'alert-'+type;
                var icon = type == 'warning' || type == 'danger' ? 'flaticon-warning' : 'flaticon-alert';
                var alert = $(
                '<div class="alert ' 
                + className 
                + ' fade show" role="alert">'
                + '<div class="alert-icon"><i class="'+icon+'"></i></div>'
                + '<div class="alert-text">'
                + content
                + '</div>'
                + '<div class="alert-close">'
                + '    <button type="button" class="close" data-dismiss="alert" aria-label="Close">'
                + '        <span aria-hidden="true"><i class="la la-close"></i></span>'
                + '    </button>'
                + '</div>'
                + '</div>'
                );
                alert.alert();
                $('#cms-alert').append(alert);
            }
HTML;
    }
}

if (!function_exists('cms_inline_notify_javascript')) {
    function cms_inline_notify_javascript($message)
    {
        $content = $message->message;
        $content = is_callable($content) ? $content() : $content;
        $type = $message->type;
        $type = $type == 'error' ? 'danger' : $type;
        $icon = $type == ('warning' || $type == 'danger') ? 'flaticon-warning' : 'flaticon-alert';
        $align = $type == ('warning' || $type == 'danger') ? 'center' : 'left';

        return <<<HTML
            $.notify({icon: '{$icon}', message: '{$content}'}, {allow_dismiss: true, type: '{$type}', delay: 10000, placement: {from: 'top', align: '{$align}'}});
HTML;
    }
}

if (!function_exists('show_cms_inline_flash_message')) {
    function show_cms_inline_flash_message($message)
    {
        $content = $message->message;
        $content = is_callable($content) ? $content() : $content;

        return <<<HTML
            cmsAlert('{$content}', '{$message->type}');
HTML;
    }
}

if (!function_exists('show_cms_notification_messages')) {
    function show_cms_notification_messages()
    {
        if ($message = notification('message')->pull('flash')) {
            return show_cms_flash_message($message);
        }

        if ($message = notification('message')->pull('flashinline')) {
            return cms_inline_notify_javascript($message);
        }
    }
}

if (!function_exists('cms_button_new')) {
    function cms_button_new($caption)
    {
        return new \Suitcorecms\Resources\Buttons\Button($caption);
    }
}

if (!function_exists('cms_button_group_item')) {
    function cms_button_group_item($button)
    {
        return $button->show([
            'class'   => 'kt-nav__link',
            'content' => function ($btn) {
                return "
                    <i class=\"kt-nav__link-icon {$btn->getIcon()}\"></i>
                    <span class=\"kt-nav__link-text\">{$btn->getCaption()}</span>
                ";
            },
        ]);
    }
}

if (!function_exists('cms_button_group')) {
    function cms_button_group(array $buttons, callable $buttonTemplate, callable $firstTemplate, $optionalFirst = null)
    {
        $first = $optionalFirst ?? array_shift($buttons);

        if ($first) {
            $btnFirst = call_user_func_array($firstTemplate, [$first]);
            $btnTmplt = '';
            if (count($buttons)) {
                $btnTmplt = '
                    <button type="button" class="btn btn-brand dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <ul class="kt-nav">';
                foreach ($buttons as $button) {
                    $btnTmplt .= '<li class="kt-nav__item">'.call_user_func_array($buttonTemplate, [$button]).'</li>';
                }

                $btnTmplt .= '
                        </ul>
                    </div>';
            }

            return <<<HTML
                <div class="btn-group">
                    {$btnFirst}
                    {$btnTmplt}
                </div>
HTML;
        }

        return '';
    }
}

if (!function_exists('crud_button_group')) {
    function crud_button_group($buttons, $optionalFirst = null)
    {
        return cms_button_group(
            $buttons,
            'cms_button_group_item',
            function ($button) {
                return $button->show([
                    'class' => 'btn btn-brand',
                    'text'  => function ($button) {
                        return "<span class=\"kt-hidden-mobile\">{$button->getCaption()}</span>";
                    },
                ]);
            },
            $optionalFirst
        );
    }
}

if (!function_exists('change_dot_to_bracket')) {
    function change_dot_to_bracket($name)
    {
        $names = explode('.', $name);
        $name = array_shift($names);
        foreach ($names as $n) {
            $name .= "[{$n}]";
        }

        return $name;
    }
}

if (!function_exists('change_bracket_to_dot')) {
    function change_bracket_to_dot($name)
    {
        return str_replace('[', '.', str_replace(']', '', $name));
    }
}

if (!function_exists('show_notification_item')) {
    function show_notification_item($item)
    {
        $url = route('cms.me.notifications.show', ['id' => $item->id]);
        $title = $item->read()
                    ? $item->notification()->caption()
                    : '<strong>'.$item->notification()->caption().'</strong>';
        $time = $item->created_at->diffForHumans();

        return <<<HTML
            <a href="{$url}" class="kt-notification__item">
                <div class="kt-notification__item-icon">
                    <i class="flaticon2-line-chart kt-font-success"></i>
                </div>
                <div class="kt-notification__item-details">
                    <div class="kt-notification__item-title">
                        {$title}
                    </div>
                    <div class="kt-notification__item-time">
                        {$time}
                    </div>
                </div>
            </a>
HTML;
    }
}
