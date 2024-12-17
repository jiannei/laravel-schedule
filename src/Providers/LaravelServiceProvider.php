<?php

/*
 * This file is part of the jiannei/laravel-schedule.
 *
 * (c) jiannei <longjian.huang@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Jiannei\Schedule\Laravel\Providers;

use Cron\CronExpression;
use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Jiannei\Schedule\Laravel\Listeners\ScheduledTaskFailedListener;
use Jiannei\Schedule\Laravel\Listeners\ScheduledTaskFinishedListener;
use Jiannei\Schedule\Laravel\Listeners\ScheduledTaskStartingListener;

class LaravelServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->extendValidationRules();

        $this->setupConfig();

        if ($this->app->runningInConsole()) {
            $this->setupMigration();

            $this->listenEvents();

            $this->app->resolving(Schedule::class, function ($schedule) {
                $this->schedule($schedule);
            });
        }
    }

    protected function listenEvents(): void
    {
        $this->app['events']->listen(ScheduledTaskStarting::class, ScheduledTaskStartingListener::class);
        $this->app['events']->listen(ScheduledTaskFinished::class, ScheduledTaskFinishedListener::class);
        $this->app['events']->listen(ScheduledTaskFailed::class, ScheduledTaskFailedListener::class);
    }

    protected function extendValidationRules(): void
    {
        Validator::extend('cron_expression', function ($attribute, $value, $parameters, $validator) {
            return CronExpression::isValidExpression($value);
        });
    }

    protected function setupConfig(): void
    {
        $configPath = dirname(__DIR__, 2).'/config/schedule.php';

        if ($this->app->runningInConsole()) {
            $this->publishes([$configPath => config_path('schedule.php')], 'schedule');
        }

        $this->mergeConfigFrom($configPath, 'schedule');
    }

    protected function setupMigration(): void
    {
        $this->publishes([
            __DIR__.'/../../database/migrations/create_schedules_table.php.stub' => database_path('migrations/'.date('Y_m_d_His').'_create_schedules_table.php'),
        ], 'migrations');
    }

    /**
     * Prepare schedule from tasks.
     */
    protected function schedule(Schedule $schedule): void
    {
        $commands = app(\Illuminate\Contracts\Console\Kernel::class)->all();

        try {
            $schedules = app(Config::get('schedule.model'))->active()->get();
        } catch (QueryException $exception) {
            $schedules = collect();
        }

        $schedules->each(function ($item) use ($schedule, $commands) {
            $event = $schedule->command($item->command.' '.$item->parameters);
            $event->cron($item->expression)
                ->name($item->description)
                ->timezone($item->timezone);

            $callbacks = ['skip', 'when', 'before', 'after', 'onSuccess', 'onFailure'];
            foreach ($callbacks as $callback) {
                if (isset($commands[$item->command]) && method_exists($commands[$item->command], $callback)) {
                    $event->$callback($commands[$item->command]->$callback($event, $item));
                }
            }

            if ($item->environments) {
                $event->environments($item->environments);
            }

            if ($item->without_overlapping) {
                $event->withoutOverlapping($item->without_overlapping);
            }

            if ($item->on_one_server) {
                $event->onOneServer();
            }

            if ($item->in_background) {
                $event->runInBackground();
            }

            if ($item->in_maintenance_mode) {
                $event->evenInMaintenanceMode();
            }

            if ($item->output_file_path) {
                if ($item->output_append) {
                    $event->appendOutputTo(Config::get('schedule.output.path').Str::start($item->output_file_path, DIRECTORY_SEPARATOR));
                } else {
                    $event->sendOutputTo(Config::get('schedule.output.path').Str::start($item->output_file_path, DIRECTORY_SEPARATOR));
                }
            }

            if ($item->output_email) {
                if ($item->output_email_on_failure) {
                    $event->emailOutputOnFailure($item->output_email);
                } else {
                    $event->emailOutputTo($item->output_email);
                }
            }
        });
    }
}
