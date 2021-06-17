<?php

/*
 * This file is part of the jiannei/laravel-schedule.
 *
 * (c) jiannei <longjian.huang@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Jiannei\Schedule\Laravel\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleJobLog extends Model
{
    protected $fillable = [
        'uuid','connection','queue','status','payload','duration','start','end','processing_at','processed_at'
    ];
}
