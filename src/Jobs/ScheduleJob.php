<?php

/*
 * This file is part of the jiannei/laravel-schedule.
 *
 * (c) jiannei <longjian.huang@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Jiannei\Schedule\Laravel\Jobs;

use Illuminate\Queue\Jobs\Job;
use Jiannei\Schedule\Laravel\Contracts\ScheduleContract;

abstract class ScheduleJob extends Job implements ScheduleContract
{
}
