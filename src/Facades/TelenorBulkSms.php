<?php

namespace SolveCase\TelenorBulkSms\Facades;

use Illuminate\Support\Facades\Facade;

class TelenorBulkSms extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'telenorbulksms';
    }
}
