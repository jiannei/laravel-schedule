<?php


namespace Jiannei\Schedule\Laravel\Jobs;


use Illuminate\Queue\Jobs\Job;
use Jiannei\Schedule\Laravel\Contracts\ScheduleContract;

abstract class ScheduleJob extends Job implements ScheduleContract
{

}
