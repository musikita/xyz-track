<?php

// routes/cms.php

use Suitcorecms\Cms\Route as CmsRoute;

Route::get('/', 'DashboardController')->name('cms.index');

CmsRoute::resource('city', 'CityController');
CmsRoute::resource('division', 'DivisionController');
CmsRoute::resource('project/task', 'ProjectTaskController', ['as' => 'project']);
CmsRoute::resource('project', 'ProjectController');
CmsRoute::resource('province', 'ProvinceController');
CmsRoute::resource('role', 'RoleController');
CmsRoute::resource('task', 'TaskController');


Suitcorecms\Excel\Route::routes();
