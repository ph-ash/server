<?php

declare(strict_types=1);

namespace App\Service\GrowTiles;

use App\Document\MonitoringData;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class PriorityGrowthService implements PriorityGrowth
{
    private $expressionLanguage;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public function calculateNewPriority(MonitoringData $monitoringData): int
    {
        $growthExpression = $monitoringData->getTileExpansionGrowthExpression() ?? '+ 1';

        $newPriority = $this->expressionLanguage->evaluate(
            sprintf('priority %s', $growthExpression),
            ['priority' => $monitoringData->getPriority()]
        );

        return $newPriority;
    }
}
