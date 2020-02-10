<?php

return [
    'base_domain' => env('CMS_BASEDOMAIN'),
    'prefix_url'  => env('CMS_PREFIXURL', '/'),
    'theme'       => env('CMS_THEME', 'theme1'),
    'asset_url'   => env('CMS_ASSET', '/backend_assets'),

    // important
    'namespace' => 'App\Http\Controllers\Cms',

    'authenticated' => $config_authenticated = env('CMS_AUTH', false),

    'guard' => $guard_name = 'cms',

    'middlewares' => array_filter([
        'web', $config_authenticated ? 'auth:'.$guard_name : null, $config_authenticated ? 'auth:'.$guard_name : null,
    ]),

    'routes' => base_path('routes/cms.php'),

    /**
     * if you want to define your route to access your user profile,
     * uncomment this code, and define your route name.
     */
    'users' => [
        // 'profile' => 'cms.me.one',
        // 'notification'  => 'cms.me.notification',
        // 'logout'  => 'cms.logout',
    ],

    'menus' => [
        'Links' => [
            // 'format' => function () {
            //     return '';
            // },
            'items' => [
                'Google' => [
                    'url'             => 'https://www.google.co.id',
                    'link_attributes' => 'target="_blank"',
                    'icon'            => 'fa-globe',
                ],
            ],
        ],
        'Main Menu' => [
            'items' => [
                'Dashboard' => [
                    'route'   => 'cms.index',
                    'icon'    => 'flaticon-home',
                    'descend' => false,
                ],
                'Proyek' => [
                    'route'   => 'cms.project.index',
                    'icon'    => 'la la-wrench',
                ],
                'Data Master' => [
                    'icon' => 'la la-server',
                    'submenus' => [
                        'Propinsi' => [
                            'route' => 'cms.province.index',
                        ],
                        'Kota' => [
                            'route' => 'cms.city.index',
                        ],
                        'Divisi' => [
                            'route' => 'cms.division.index',
                        ],
                        'Pekerjaan' => [
                            'route' => 'cms.task.index',
                        ],
                        'Role' => [
                            'route' => 'cms.role.index',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'breadcrumbs' => [
        'separator' => '<span class="kt-subheader__breadcrumbs-separator"></span>',
        // 'item' => function ($link, $title, $active) {
        //     if ($active) {
        //         return '<span class="kt-subheader__breadcrumbs-link kt-subheader__breadcrumbs-link--active">'.$title.'</span>';
        //     }
        //     return '<a href="'.$link.'" class="kt-subheader__breadcrumbs-link">'.$title.'</a>';
        // },
    ],

    'datatables' => [
        'checkbox'   => [\Suitcorecms\Datatables\Datatables::class, 'defaultCheckbox'],
        'action'     => [\Suitcorecms\Datatables\Datatables::class, 'defaultAction'],
        'parameters' => [
            'responsive' => true,
            'columnDefs' => [
                ['responsivePriority' => 1, 'targets' => -1],
            ],
            // Order settings
            'order'          => [[1, 'desc']],
            'headerCallback' => 'function(thead, data, start, end, display) {
                thead.getElementsByTagName(\'th\')[0].innerHTML = `
                    <label class="kt-checkbox kt-checkbox--single kt-checkbox--solid">
                        <input type="checkbox" value="1" class="m-group-checkable" onchange="$.BulkSelectAll && $.BulkSelectAll(this)">
                        <span></span>
                    </label>`;
            }',
            'drawCallback' => 'function( settings ) {
                $.DatatablesSelect = function (obj) {
                    if ($.BulkSelect) {
                        $.BulkSelect(obj);
                    }
                    if (obj.checked == true){
                        $(obj).parents(\'tr\').addClass(\'selected\')
                    } else {
                        $(obj).parents(\'tr\').removeClass(\'selected\')
                    }
                }
            }',
        ],
    ],

    'forms' => [
        'template' => 'suitcorecms::partials.form',
        'selector' => 'form.kt-form',
        'inputs'   => [
            'attributes' => [
                'default' => [
                    'class' => 'form-control',
                ],
            ],
        ],
    ],

    'templates' => [
        'form_group' => '
                    <div class="form-group mb-4 mt-2">
                        <label class="kt-font-dark">{title}</label>
                        {control}
                        <span class="form-text text-muted"></span>
                    </div>',
        'show_wrapper' => '<form class="kt-form kt-form--label-right">
                            {html}
                        </form>',
        'show_group' => '<div class="row">
                        <label class="col-md-3 col-form-label text-dark">{title}</label>
                        <div class="col-md-8"><p class="form-control-plaintext">{value}</p> </div>
                    </div>
                    <div class="kt-separator kt-separator--border-dashed m-1"></div>',
        'no_value_show' => '<i>(no value)</i>',
    ],

    'fields' => [
        'richtext' => [
            'index' => [
                'length' => 20,
            ],
            'form' => [
                'javascript' => '
                    tinymce.init({
                        selector: `[richtext]`,
                        plugins : `autoresize advlist autolink link image lists charmap print preview lists code`,
                        toolbar: `undo redo | styleselect | bold italic underline | numlist bullist | link image code`,
                        autoresize_on_init: false,
                        min_height: 350,
                        max_height: 500,
                        paste_data_images: true,
                        image_advtab: true,
                        file_picker_callback: function(callback, value, meta) {
                          if (meta.filetype == `image`) {
                            $(`#tiny_mce_upload_image`).trigger(`click`);
                            $(`#tiny_mce_upload_image`).on(`change`, function() {
                              var file = this.files[0];
                              var reader = new FileReader();
                              reader.onload = function(e) {
                                callback(e.target.result, {
                                  alt: ``
                                });
                              };
                              reader.readAsDataURL(file);
                            });
                          }
                        },
                    })
                ',
            ],
        ],
        'image' => [
            'delete_name'       => 'delete_image',
            'delete_identifier' => 'name',
        ],
        'map' => [
            'form' => [\Suitcorecms\Fields\FieldTypes\Map::class, 'formMapBox'],
            'show' => [\Suitcorecms\Fields\FieldTypes\Map::class, 'showMapBox'],
        ],
    ],

    'medialibrary' => [
        'cms_scope' => 'cms',
        'thumbnail' => [
            'width'  => 100,
            'height' => 100,
        ],
    ],

    'seo' => [
        'meta' => [
            'title'       => env('SEO_TITLE', 'Default Title'),
            'description' => env('SEO_DESCRIPTION', 'Default Description'),
            'og'          => [
                'url'       => [Illuminate\Support\Facades\URL::class, 'current'],
                'site_name' => env('APP_NAME', 'All In Dive'),
                'type'      => 'website',
                'fb:app_id' => env('FB_APPID', null),
            ],
            'twitter' => [
                'site' => '@allindive',
                // 'creator' => '',
            ],
        ],
        'duplication' => [
            'og:title'              => 'title',
            'og:description'        => 'description',
            'og:image'              => 'image',
            'twitter:title'         => 'title',
            'twitter:description'   => 'description',
            'twitter:image'         => 'image',
        ],
        'basename'              => 'Seo Tools',
        'seo_table'             => 'seo',
        'seo_model'             => \Suitcorecms\Seo\Model::class,
        'seo_model_observer'    => \Suitcorecms\Seo\ModelObserver::class,
        'form'                  => [
            'field_name'    => 'seo_field',
            'general'       => null,
            'open_graph'    => null,
            'twitter_card'  => null,
        ],
    ],

    'notifications' => [
        'messengers' => [
            'flash'       => \Suitcorecms\Notifications\FlashMessage\Message::class,
            'flashinline' => \Suitcorecms\Notifications\FlashInlineMessage\Message::class,
            'error'       => \Suitcorecms\Notifications\ErrorMessage\Message::class,
        ],
    ],

    'services' => [
        'mapbox' => [
            'apikey' => env('MAPBOX_APIKEY', null),
        ],
    ],
];
