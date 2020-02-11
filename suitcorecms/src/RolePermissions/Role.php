<?php

namespace Suitcorecms\RolePermissions;

use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role as BaseRole;
use Suitcorecms\Resources\Contracts\Resourceable;
use Suitcorecms\Resources\ResourceableTrait;

class Role extends BaseRole implements Resourceable
{
    use ResourceableTrait;

    protected $fillable = ['name', 'guard_name'];

    public function rules($method)
    {
        $rolesTable = config('permission.table_names.roles', 'roles');

        return [
            'name' => [
                $method == 'create' ? Rule::unique($rolesTable) : Rule::unique($rolesTable)->ignore($this->id),
            ],
        ];
    }
}
