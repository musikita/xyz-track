<?php

namespace Suitcorecms\RolePermissions;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Suitcorecms\Cms\Controller;
use Suitcorecms\Cms\Route as CmsRoute;

class RoleController extends Controller
{
    public static function cmsRoutes($uri = 'roles')
    {
        CmsRoute::resource($uri, static::class);
    }

    protected function baseResourceable()
    {
        return new Role();
    }

    protected function registerObserver()
    {
        Role::observe($this);
    }

    public function formOutput($model, $name, $form)
    {
        return $model->permissions->map(function ($item) {
            $item = $item->toArray();
            $item['id'] = $item['name'];

            return $item;
        });
    }

    public function fields()
    {
        $routes = Route::getRoutes();

        $routeFields = [];
        foreach ($routes as $route) {
            $routeName = explode('.', $route->getName());
            $action = basename(str_replace('\\', '/', $route->getAction()['controller']));
            [$controller, $method] = explode('Controller@', $action);
            $fieldLabel = Str::title(Str::snake($controller, ' '));

            if (count($routeName) >= 3 && array_shift($routeName) === 'cms') {
                array_shift($routeName);

                if (!isset($routeFields[$fieldLabel])) {
                    $routeFields[$fieldLabel] = [
                        'on_index' => false,
                        'output'   => function ($model, $value, $field) use ($route) {
                            $options = array_keys($field['options']);
                            if ($value) {
                                return $value
                                    ->filter(function ($item) use ($options) {
                                        return in_array($item->name, $options);
                                    })
                                    ->map(function ($item) use ($field) {
                                        return '<span class="kt-badge kt-badge--xl kt-badge--unified-dark kt-badge--inline">'.($field['options'][$item->name] ?? null).'</span>';
                                    })
                                    ->implode(' ');
                            }

                            return '-';
                        },
                        'type'     => 'checkbox',
                        'name'     => 'permissions',
                        'relation' => 'permissions',
                        'options'  => [],
                        'on_form'  => [
                            'output' => [$this, 'formOutput'],
                        ],
                        'attributes' => [
                            'autocomplete' => false,
                        ],
                    ];
                }
                $routeName = str_replace(['_', '-'], ' ', implode(' ', $routeName));
                $routeFields[$fieldLabel]['options'][$route->getName()] = Str::title($routeName);
            }
        }

        return array_merge([
            'ID' => [
                'name'    => 'id',
                'type'    => 'text',
                'on_form' => false,
            ],
            'Name' => [
                'type'  => 'text',
                'rules' => 'required',
            ],
        ], $routeFields);
    }

    public function saving($model)
    {
        $model->guard_name = 'cms';
    }

    public function saved($model)
    {
        $permissions = [];

        foreach (request()->permissions as $permission) {
            if (Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'cms'])) {
                $permissions[] = $permission;
            }
        }

        $model->syncPermissions($permissions);
    }
}
