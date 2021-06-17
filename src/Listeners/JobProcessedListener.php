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

use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Carbon;
use Jiannei\Schedule\Laravel\Support\Helpers;

class JobProcessedListener
{
    use Helpers;

    public function handle(JobProcessed $event)
    {
        if (! $this->schedulable($event->job)) {
            return;
        }

        $jobLog = $this->jobLogModel()::query()->select('start')->where('id', $event->job->getJobId())->first();
        if (! $jobLog) {
            return;
        }

        $end = microtime(true);
        $duration = format_duration($end - $jobLog->start);

        $jobLog->update([
            'status' => 'success',
            'end' => $end,
            'processed_at' => Carbon::now()->toDateTimeString(),
            'duration' => $duration,
        ]);
    }
}
