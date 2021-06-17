<?php

/*
 * This file is part of the jiannei/laravel-schedule.
 *
 * (c) jiannei <longjian.huang@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Jiannei\Schedule\Laravel\Support;

use Illuminate\Contracts\Queue\Job;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Jiannei\Schedule\Laravel\Contracts\ScheduleContract;

trait Helpers
{
    protected function resultModel(): Model
    {
        return app(Config::get('schedule.result.model'));
    }

    protected function schedulable(Job $job): bool
    {
        return is_subclass_of($job->resolveName(), ScheduleContract::class);
    }
}
