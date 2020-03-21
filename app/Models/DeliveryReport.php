<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryReport extends Model
{
    use SoftDeletes;

    const DELIVERY_REPORT_TYPE = ['TYPE_ONE' => 'TYPE_ONE', 'TYPE_TWO' => 'TYPE_TWO'];
}
