<?php

declare(strict_types=1);

namespace App\Service\GrowTiles;

use App\Document\MonitoringData;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class PriorityGrowthService implements PriorityGrowth
{
    private const EVLUATION_CRITERIA = 'priority %s';

    private $expressionLanguage;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public function calculateNewPriority(MonitoringData $monitoringData): int
    {
        $growthExpression = $monitoringData->getTileExpansionGrowthExpression() ?? '+ 1';
        $invertedExpression = str_replace(['+', '*'], ['-', '/'], $growthExpression);

        $maximumCurrentPriority = $this->expressionLanguage->evaluate(
            sprintf(self::EVLUATION_CRITERIA, $invertedExpression),
            ['priority' => PHP_INT_MAX]
        );
        if ($monitoringData->getPriority() > $maximumCurrentPriority) {
            return PHP_INT_MAX;
        }

        $newPriority = $this->expressionLanguage->evaluate(
            sprintf(self::EVLUATION_CRITERIA, $growthExpression),
            ['priority' => $monitoringData->getPriority()]
        );

        return $newPriority;
    }
}
