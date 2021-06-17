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

use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Carbon;
use Jiannei\Schedule\Laravel\Support\Helpers;

class JobProcessingListener
{
    use Helpers;

    public function handle(JobProcessing $event)
    {
        if (! $this->schedulable($event->job->resolveName())) {
            return;
        }

        $this->jobLogModel()::query()->create([
            'uuid' => $event->job->uuid(),
            'job' => $event->job->resolveName(),
            'connection' => $event->job->getConnectionName(),
            'queue' => $event->job->getQueue(),
            'payload' => $event->job->getRawBody(),
            'status' => 'starting',
            'start' => microtime(true),
            'processing_at' => Carbon::now()->toDateTimeString(),
        ]);
    }
}
