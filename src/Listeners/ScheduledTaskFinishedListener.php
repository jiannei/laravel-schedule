<?php

/*
 * This file is part of the jiannei/laravel-schedule.
 *
 * (c) jiannei <longjian.huang@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Jiannei\Schedule\Laravel\Listeners;

use Illuminate\Console\Events\ScheduledTaskFinished;

class ScheduledTaskFinishedListener
{
    public function handle(ScheduledTaskFinished $event) {}
}
