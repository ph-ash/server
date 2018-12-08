<?php

declare(strict_types=1);

namespace App\Command;

use App\Dto\MonitoringData;
use App\Service\IncomingMonitoringDataDispatcher;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApiPostTestCommand extends Command
{
    private $monitoringDataDispatcher;

    public function __construct(IncomingMonitoringDataDispatcher $monitoringDataDispatcher)
    {
        parent::__construct();
        $this->monitoringDataDispatcher = $monitoringDataDispatcher;
    }

    /**
     * @throws Exception
     */
    protected function configure(): void
    {
        $this->setName('phash:post-test:start')
            ->addArgument('status', InputArgument::OPTIONAL, '', 'ok')
            ->addArgument('count', InputArgument::OPTIONAL, '', 25);
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        for ($i = 0; $i < $input->getArgument('count'); $i++) {
            $monitoringData = new MonitoringData(
                'monitoring id '. $i,
                $input->getArgument('status'),
                'some payload from' . $i,
                \random_int(1, 25),
                60,
                new DateTimeImmutable()
            );
            $this->monitoringDataDispatcher->invoke($monitoringData);
        }
    }
}
