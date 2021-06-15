<?php

/*
 * This file is part of the jiannei/laravel-schedule.
 *
 * (c) jiannei <longjian.huang@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Jiannei\Schedule\Laravel\Repositories\Enums;

use Illuminate\Support\Str;
use Jiannei\Enum\Laravel\Contracts\LocalizedEnumContract;
use Jiannei\Enum\Laravel\Enum;

class CommandEnum extends Enum implements LocalizedEnumContract
{
    public function hasTruthConstraint(string $key)
    {
        // skip 、when
        return $this->hasCallback($key);
    }

    private function hasCallback(string $key)
    {
        $method = Str::camel(Str::lower($this->key)).ucfirst($key);

        return method_exists($this, $method) ? $method : false;
    }

    public function hasHook(string $key): bool
    {
        // before、after、success、failure
        return $this->hasCallback($key);
    }
}
