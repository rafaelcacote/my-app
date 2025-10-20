<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shared.roles';
}
