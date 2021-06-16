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
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Jiannei\Schedule\Laravel\Commands\ScheduleCommandsCommand;
use Jiannei\Schedule\Laravel\Contracts\ScheduleContract;

class ServiceProvider extends IlluminateServiceProvider
{
    public function boot(): void
    {
        $this->setupValidationRules();

        $this->setupConfig();

        $this->setupMigration();

        $this->setupCommands();

        if ($this->app->runningInConsole()) {
            $this->app->resolving(Schedule::class, function ($schedule) {
                $this->schedule($schedule);
            });

            $this->listenForEvents();
        }
    }

    protected function setupCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ScheduleCommandsCommand::class,
            ]);
        }
    }

    protected function setupValidationRules(): void
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
        if ($this->app->runningInConsole() && ! class_exists('CreateSchedulesTable')) {
            $this->publishes([
                __DIR__.'/../../database/migrations/create_schedules_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_schedules_table.php'),
            ], 'migrations');
        }
    }

    /**
     * Prepare schedule from tasks.
     *
     * @param  Schedule  $schedule
     */
    protected function schedule(Schedule $schedule): void
    {
        try {
            $schedules = app(Config::get('schedule.model'))->active()->get();
        } catch (QueryException $exception) {
            $schedules = collect();
        }

        $schedules->each(function ($item) use ($schedule) {
            //  todo 分片逻辑
            $event = $schedule->command($item->command.' '.$item->parameters);

            $event->cron($item->expression)
                ->name($item->description)
                ->timezone($item->timezone)
                ->runInBackground();

            if (class_exists($enum = Config::get('schedule.enum'))) {
                $commandEnum = $enum::fromValue($item->command);
                $callbacks = ['skip', 'when', 'onSuccess', 'onFailure']; // TODO：onSuccess、onFailure 对于 job 似乎没作用
                foreach ($callbacks as $callback) {
                    if ($method = $commandEnum->hasTruthConstraint($callback)) {
                        $event->$callback($commandEnum->$method($event, $item));
                    }
                }
            }

            if ($item->without_overlap) {
                $event->withoutOverlapping();
            }
            if ($item->run_in_maintenance_mode) {
                $event->evenInMaintenanceMode();
            }
            if ($item->run_on_one_server) {
                $event->onOneServer();
            }
        });
    }

    /**
     * Listen for the queue events in order to update the console output.
     *
     * @return void
     */
    protected function listenForEvents(): void
    {
        // todo 监听 schedule event
        $this->app['events']->listen(JobProcessing::class, function ($event) {
            $this->jobProcessing($event->job);
        });

        $this->app['events']->listen(JobProcessed::class, function ($event) {
            $this->jobProcessed($event->job);
        });
    }

    protected function jobProcessing(Job $job): void
    {
        if (! $this->schedulable($job)) {
            return;
        }

        $this->model()::query()->create([
            'id' => $job->getJobId(),
            'job' => $job->resolveName(),
            'connection' => $job->getConnectionName(),
            'queue' => $job->getQueue(),
            'payload' => $job->getRawBody(),
            'status' => 'starting',
            'start' => microtime(true),
            'end' => 0,
            'duration' => '',
            'processing_at' => Carbon::now()->toDateTimeString(),
            'processed_at' => '',
        ]);
    }

    protected function schedulable(Job $job): bool
    {
        if ($command = Arr::get($job->payload(), 'data.command')) {
            return unserialize($command) instanceof ScheduleContract;
        }

        return false;
    }

    protected function model(): Model
    {
        if (! class_exists($model = Config::get('schedule.result.model'))) {
            throw new \Exception('result model config error');
        }

        return app($model);
    }

    protected function jobProcessed(Job $job): void
    {
        if (! $this->schedulable($job)) {
            return;
        }

        $result = $this->model()::query()->where('id', $job->getJobId())->first();
        if (! $result) {
            return;
        }

        $end = microtime(true);
        $duration = format_duration($end - $result->start);

        $result->update([
            'status' => 'success',
            'end' => $end,
            'processed_at' => Carbon::now()->toDateTimeString(),
            'duration' => $duration,
        ]);
    }
}
