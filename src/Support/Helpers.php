<?php


namespace Jiannei\Schedule\Laravel\Support;


use Illuminate\Contracts\Queue\Job;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
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
        if ($command = Arr::get($job->payload(), 'data.command')) {
            return unserialize($command) instanceof ScheduleContract;
        }

        return false;
    }
}