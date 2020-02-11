<!DOCTYPE html>
<?php
$mainMenuFormat = function ($link, $title, $icon, $linkAttributes, $isActive, $subMenus) {
    $activeClass = $isActive ? 'kt-menu__item--active' : '';
    if ($isActive && $subMenus) {
        $activeClass .= ' kt-menu__item--open';
    }
    $linkClass = $subMenus ? 'kt-menu__toggle' : '';
    $arrow = $subMenus ? '<i class="kt-menu__ver-arrow la la-angle-right"></i>' : '';
    $subMenusBlock = !$subMenus ? '' : <<<HTML
        <div class="kt-menu__submenu ">
            <span class="kt-menu__arrow"></span>
            <ul class="kt-menu__subnav">
                {$subMenus}
            </ul>
        </div>
HTML;
    $iconHtml = $icon ? '<i class="kt-menu__link-icon '.$icon.'"></i>' : '<i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>';
    return <<<HTML
        <li class="kt-menu__item {$activeClass}" aria-haspopup="true">
            <a href="{$link}" class="kt-menu__link {$linkClass}" {$linkAttributes} >
                {$iconHtml}
                <span class="kt-menu__link-text">{$title}</span>
                {$arrow}
            </a>
            {$subMenusBlock}
        </li>
HTML;
}
?>

<!--
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 4 & Angular 7
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
Renew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<html lang="en">

    <!-- begin::Head -->
    <head>

        <!--begin::Base Path (base relative path for assets of this page) -->
        <!--end::Base Path -->
        <meta charset="utf-8" />
        <title>{!! $title ?? title_from_breadcrumb($breadcrumb ?? null) !!} | {{config('app.name')}}</title>
        <meta name="description" content="Page with empty content">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!--begin::Fonts -->
        <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
        <script>
            WebFont.load({
                google: {
                    "families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]
                },
                active: function() {
                    sessionStorage.fonts = true;
                }
            });
        </script>

        <!--end::Fonts -->

        <!--begin:: Global Mandatory Vendors -->
        <link href="{{ cms_asset() }}vendors/general/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" type="text/css" />

        <!--end:: Global Mandatory Vendors -->

@include('suitcorecms::partials.vendors-css')

    <!--begin::Global Theme Styles(used by all pages) -->
        <link href="{{ cms_asset() }}css/demo1/style.bundle.css" rel="stylesheet" type="text/css" />

        <!--end::Global Theme Styles -->

        <!--begin::Layout Skins(used by all pages) -->
        <link href="{{ cms_asset() }}css/demo1/skins/header/base/light.css" rel="stylesheet" type="text/css" />
        <link href="{{ cms_asset() }}css/demo1/skins/header/menu/light.css" rel="stylesheet" type="text/css" />
        <link href="{{ cms_asset() }}css/demo1/skins/brand/dark.css" rel="stylesheet" type="text/css" />
        <link href="{{ cms_asset() }}css/demo1/skins/aside/dark.css" rel="stylesheet" type="text/css" />

        <!--end::Layout Skins -->
        <link rel="shortcut icon" href="{{ cms_asset() }}media/logos/favicon.ico" />
    </head>

    <!-- end::Head -->

    <!-- begin::Body -->
    <body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">

        <!-- begin:: Page -->

        <!-- begin:: Header Mobile -->
        <div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
            <div class="kt-header-mobile__logo">
                <a href="{{cms_url()}}">
                    <h4 class="title text-white text-uppercase">{{config('app.name')}}</h4>
                    <!-- <img alt="Logo" src="{{ cms_asset() }}media/logos/logo-light.png" /> -->
                </a>
            </div>
            <div class="kt-header-mobile__toolbar">
                <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler"><span></span></button>
               <!--  <button class="kt-header-mobile__toggler" id="kt_header_mobile_toggler"><span></span></button> -->
                <button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>
            </div>
        </div>

        <!-- end:: Header Mobile -->
        <div class="kt-grid kt-grid--hor kt-grid--root">
            <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">

                <!-- begin:: Aside -->
                <button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>
                <div class="kt-aside  kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop" id="kt_aside">

                    <!-- begin:: Aside -->
                    <div class="kt-aside__brand kt-grid__item " id="kt_aside_brand">
                        <div class="kt-aside__brand-logo">
                            <a href="{{cms_url()}}">
                                <h4 class="title text-white text-uppercase">{{config('app.name')}}</h4>
                                <!-- <img alt="Logo" src="{{ cms_asset() }}media/logos/logo-light.png" /> -->
                            </a>
                        </div>
                        <div class="kt-aside__brand-tools">
                            <button class="kt-aside__brand-aside-toggler" id="kt_aside_toggler">
                                <span><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon id="Shape" points="0 0 24 0 24 24 0 24" />
                                            <path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" id="Path-94" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999) " />
                                            <path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999) " />
                                        </g>
                                    </svg></span>
                                <span><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon id="Shape" points="0 0 24 0 24 24 0 24" />
                                            <path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" id="Path-94" fill="#000000" fill-rule="nonzero" />
                                            <path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999) " />
                                        </g>
                                    </svg></span>
                            </button>

                            <!--
            <button class="kt-aside__brand-aside-toggler kt-aside__brand-aside-toggler--left" id="kt_aside_toggler"><span></span></button>
            -->
                        </div>
                    </div>

                    <!-- end:: Aside -->

                    <!-- begin:: Aside Menu -->
                    <div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
                        <div id="kt_aside_menu" class="kt-aside-menu " data-ktmenu-vertical="1" data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">
                            <ul class="kt-menu__nav ">
                                @if ($cms_custom_menus1 ?? false)
                                {!! cms_menu($cms_custom_menus1, $mainMenuFormat) !!}
                                @endif
                                {!! cms_menu('Main Menu', $mainMenuFormat) !!}
                                @if ($cms_custom_menus2 ?? false)
                                {!! cms_menu($cms_custom_menus2, $mainMenuFormat) !!}
                                @endif
                            </ul>
                        </div>
                    </div>

                    <!-- end:: Aside Menu -->
                </div>

                <!-- end:: Aside -->
                <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">

                    <!-- begin:: Header -->
                    <div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed ">

                        <!-- begin:: Header Menu -->
                        <button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
                        <div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">
                            <div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile  kt-header-menu--layout-default ">
@section('header_menu')
                                <ul class="kt-menu__nav ">
                                </ul>
@endsection
@yield('header_menu')
                            </div>
                        </div>

                        <!-- end:: Header Menu -->

                        <!-- begin:: Header Topbar -->
                        <div class="kt-header__topbar">

@include('suitcorecms::partials.header-topbar')

                        <!-- end:: Header Topbar -->
                    </div>

                    <!-- end:: Header -->
                    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

                        <!-- begin:: Subheader -->
                        <div class="kt-subheader kt-grid__item" id="kt_subheader">
                            <div class="kt-subheader__main">
                                <!-- <h3 class="kt-subheader__title">
                                    Empty Page </h3> -->
<!--                                 <h3 class="kt-subheader__title"> Profile 1 </h3>
                                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
 -->
                                <div class="kt-subheader__breadcrumbs">
                                    <a href="{{cms_url()}}" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
                                    <span class="kt-subheader__breadcrumbs-separator"></span>
                                    @if ($breadcrumb ?? false)
                                    {!! $breadcrumb->output(function ($link, $title, $active) {
                                        if ($active) {
                                            return '<span class="kt-subheader__breadcrumbs-link kt-subheader__breadcrumbs-link--active">'.$title.'</span>';
                                        }
                                        return '<a href="'.$link.'" class="kt-subheader__breadcrumbs-link">'.$title.'</a>';
                                    }) !!}
                                    @endif
                                </div>
                            </div>
@yield('top_pagemenus')
                        </div>

                        <!-- end:: Subheader -->

                        <!-- begin:: Content -->
                        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
{!! cms_inline_flash_html() !!}
@yield('content')
                        </div>

                        <!-- end:: Content -->
                    </div>

                    <!-- begin:: Footer -->
                    <div class="kt-footer kt-grid__item kt-grid kt-grid--desktop kt-grid--ver-desktop" id="kt_footer">
                        <div class="kt-footer__copyright">
                            2019 {{ date('Y') == '2019' ? '' : '- '.date('Y') }} &copy;&nbsp;<a href="{{config('app.url')}}" target="_blank" class="kt-link">{{config('app.name')}}</a>
                        </div>
                        <div class="kt-footer__menu">

                        </div>
                    </div>

                    <!-- end:: Footer -->
                </div>
            </div>
        </div>

        <!-- end:: Page -->

        <!-- begin::Quick Panel -->

        <!-- end::Quick Panel -->

        <!-- begin::Scrolltop -->
        <div id="kt_scrolltop" class="kt-scrolltop">
            <i class="fa fa-arrow-up"></i>
        </div>

        <!-- end::Scrolltop -->

        <!-- begin::Sticky Toolbar -->

        <!-- end::Sticky Toolbar -->


        <!-- begin::Global Config(global config for global JS sciprts) -->
        <script>
            var KTAppOptions = {
                "colors": {
                    "state": {
                        "brand": "#5d78ff",
                        "dark": "#282a3c",
                        "light": "#ffffff",
                        "primary": "#5867dd",
                        "success": "#34bfa3",
                        "info": "#36a3f7",
                        "warning": "#ffb822",
                        "danger": "#fd3995"
                    },
                    "base": {
                        "label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
                        "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
                    }
                }
            };
        </script>

        <!-- end::Global Config -->

        <!--begin:: Global Mandatory Vendors -->
        <script src="{{ cms_asset() }}vendors/general/jquery/dist/jquery.js" type="text/javascript"></script>
        <script src="{{ cms_asset() }}vendors/general/popper.js/dist/umd/popper.js" type="text/javascript"></script>
        <script src="{{ cms_asset() }}vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="{{ cms_asset() }}vendors/general/js-cookie/src/js.cookie.js" type="text/javascript"></script>
        <script src="{{ cms_asset() }}vendors/general/moment/min/moment.min.js" type="text/javascript"></script>
        <script src="{{ cms_asset() }}vendors/general/tooltip.js/dist/umd/tooltip.min.js" type="text/javascript"></script>
        <script src="{{ cms_asset() }}vendors/general/perfect-scrollbar/dist/perfect-scrollbar.js" type="text/javascript"></script>
        <script src="{{ cms_asset() }}vendors/general/sticky-js/dist/sticky.min.js" type="text/javascript"></script>
        <script src="{{ cms_asset() }}vendors/general/wnumb/wNumb.js" type="text/javascript"></script>
        <script src="{{ cms_asset() }}vendors/jsvalidation/js/jsvalidation.js" type="text/javascript"></script>

        <!--end:: Global Mandatory Vendors -->


        <!--begin::Global Theme Bundle(used by all pages) -->
        <script src="{{ cms_asset() }}js/demo1/scripts.bundle.js" type="text/javascript"></script>

        <!--end::Global Theme Bundle -->

@include('suitcorecms::partials.vendors-js')

@stack('scripts-footer')

        <script>
        {!! show_cms_notification_messages() !!}
        </script>

        <!--begin::Page Vendors(used by this page) -->
        <!--
        <script src="//maps.google.com/maps/api/js?key=AIzaSyBTGnKT7dt597vo9QgeQ7BFhvSRP4eiMSM" type="text/javascript"></script>
        <script src="{{ cms_asset() }}vendors/custom/gmaps/gmaps.js" type="text/javascript"></script>
        -->

        <!--end::Page Vendors -->

    </body>

    <!-- end::Body -->
</html>
