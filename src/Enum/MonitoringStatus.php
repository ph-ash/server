<?php

declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;

class MonitoringStatus extends Enum
{
    private const OK = 'ok';
    private const ERROR = 'error';

    public static function OK(): MonitoringStatus
    {
        return new MonitoringStatus(self::OK);
    }

    public static function ERROR(): MonitoringStatus
    {
        return new MonitoringStatus(self::ERROR);
    }
}
