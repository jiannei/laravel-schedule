<?php

namespace Jiannei\Schedule\Laravel\Repositories\Models;


use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'description', 'command', 'parameters', 'expression', 'timezone',
        'active', 'without_overlap', 'run_on_one_server', 'run_in_background', 'run_in_maintenance_mode',
        'notification_email', 'once'
    ];

    protected $casts = [
        'active' => 'boolean',
        'once' => 'boolean',
        'without_overlap' => 'boolean',
        'run_on_one_server' => 'boolean',
        'run_in_background' => 'boolean',
        'run_in_maintenance_mode' => 'boolean',
    ];

    /**
     * Scope a query to only include active schedule.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
