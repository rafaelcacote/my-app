<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shared.permissions';
}
