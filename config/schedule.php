<?php


return [
    'table' => 'schedules',

    'enum' => \Jiannei\Schedule\Laravel\Repositories\Enums\CommandEnum::class,

    'model' => \Jiannei\Schedule\Laravel\Repositories\Models\Schedule::class,

    'result' => [
        'model' => \Jiannei\Schedule\Laravel\Repositories\Models\ScheduleResult::class
    ]
];
