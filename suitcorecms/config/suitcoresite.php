<?php

return [
    'site_settings' => [
        'name'                  => env('APP_NAME', 'All In Dive'),
        'contactus_raw_address' => '',
        'contactus_phone'       => '',
        'contactus_email'       => '',
        'mailchimp_api_key'     => env('MAILCHIMP_API_KEY', ''),
        'mailchimp_list_id'     => env('MAILCHIMP_LIST_ID', ''),
        'facebook_url'          => '',
        'twitter_url'           => '',
        'instagram_url'         => '',
        'google_analytics_code' => '',
        'google_tag_manager'    => '',
        'facebook_pixel'        => '',
    ],

    'settings' => [
        'table'    => 'settings',
        'basename' => 'General Settings',
    ],

    'contacts' => [
        'table'    => 'contacts',
        'basename' => 'Contact Messages',
        'fields'   => ['name' => 'Name', 'email' => 'Email', 'message' => 'Message'],
        'rules'    => [
            'name'                 => 'required|max:50',
            'email'                => 'email|required|max:50',
            'message'              => 'required|max:255',
            'g-recaptcha-response' => 'recaptcha',
        ],
    ],

    'subscribers' => [
        'table'    => 'subscribers',
        'model'    => \Suitcorecms\Sites\Subscribers\Model::class,
        'observer' => \Suitcorecms\Sites\Subscribers\Observer::class,
        'basename' => 'Newsletter Subscribers',
        'fields'   => ['email' => 'Email'],
        'rules'    => ['email' => 'email|required|max:50'],
    ],

    'newsletters' => [
        'table'             => 'newsletters',
        'model'             => \Suitcorecms\Sites\Newsletters\NewsletterModel::class,
        'template_table'    => 'newsletter_templates',
        'template_model'    => \Suitcorecms\Sites\Newsletters\TemplateModel::class,
        'transport_table'   => 'newsletter_transports',
        'datas'             => [
            'base_url' => 'base_url',
        ],
        'recipients'        => [\Suitcorecms\Sites\Newsletters\Newsletter::class, 'newsletterRecipients'],
        'send_at'           => false,
    ],

    'seo' => [
        'table'    => 'seo_urls',
        'basename' => 'Url Based SEO',
    ],

    'banners' => [
        'table'    => 'banners',
        'basename' => 'Banners',
        'fields'   => ['title' => 'Title', 'description' => 'Description'],
        'rules'    => ['title' => 'required|max:100', 'description' => 'max:255'],
    ],
];
