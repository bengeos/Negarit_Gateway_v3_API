<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NegaritClient extends Model
{
    use SoftDeletes;
    const PORT_TYPE = ['MOBILE_ORIGINATED' => 'MOBILE_ORIGINATED', 'MOBILE_TERMINATED' => 'MOBILE_TERMINATED'];
}
