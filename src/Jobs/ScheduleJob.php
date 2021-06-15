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

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Jiannei\Schedule\Laravel\Contracts\ScheduleContract;

class ScheduleJob implements ScheduleContract, ShouldQueue
{
    /*
       |--------------------------------------------------------------------------
       | Queueable Jobs
       |--------------------------------------------------------------------------
       |
       | This job base class provides a central location to place any logic that
       | is shared across all of your jobs. The trait included with the class
       | provides access to the "queueOn" and "delay" queue helper methods.
       |
       */

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
}
