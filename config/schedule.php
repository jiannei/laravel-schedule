<?php

/*
 * This file is part of the jiannei/laravel-schedule.
 *
 * (c) jiannei <longjian.huang@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

return [
    'table' => 'schedules',

    'model' => \Jiannei\Schedule\Laravel\Repositories\Models\Schedule::class,

    'output' => [
        'path' => storage_path('logs'),
    ],
];
