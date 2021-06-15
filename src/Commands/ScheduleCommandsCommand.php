<?php

namespace Jiannei\Schedule\Laravel\Commands;

use Carbon\Carbon;
use Cron\CronExpression;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Str;

class ScheduleCommandsCommand extends Command
{
    protected $signature = 'schedule:commands';

    protected $description = 'List the scheduled commands';

    private Schedule $schedule;


    public function __construct(Schedule $schedule)
    {
        parent::__construct();

        $this->schedule = $schedule;
    }

    public function handle(): void
    {
        if (count($this->schedule->events()) > 0) {
            $events = collect($this->schedule->events())->map(function (Event $event) {
                $command = ltrim(strtok(Str::after(str_replace("'", '', $event->command), "artisan"), ' '));

                return [
                    'description' => $event->description ?: 'N/A',
                    'command' => $command,
                    'parameters' => trim(Str::after($event->command, $command)),
                    'schedule' => $event->expression,
                    'upcoming' => $this->upcoming($event),
                    'timezone' => $event->timezone ?: config('app.timezone'),
                    'overlaps' => $event->withoutOverlapping ? 'Yes' : 'No',
                    'maintenance' => $event->evenInMaintenanceMode ? 'Yes' : 'No',
                    'oneServer' => $event->onOneServer ? 'Yes' : 'No',
                ];
            });

            $this->table($this->headers(), $events);
        } else {
            $this->info('No Scheduled Commands Found');
        }
    }

    protected function upcoming(Event $event): string
    {
        $date = Carbon::now();

        if ($event->timezone) {
            $date->setTimezone($event->timezone);
        }

        return (new CronExpression($event->expression))->getNextRunDate($date->toDateTimeString())->format('Y-m-d H:i:s');
    }

    protected function headers(): array
    {
        return [
            'Description', 'Command', 'Parameters',
            'Schedule', 'Upcoming', 'Timezone',
            'Overlaps', 'In Maintenance', 'One Server',
        ];
    }
}
