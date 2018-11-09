<?php

namespace MyQ\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleaningRobotCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('cleaning_robot')
            ->setDescription('MyQ cleaning robot.')
            ->setHelp('Cleans all surface in a room without manual intervention.')
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'Path to the source file.'
            )
            ->addArgument(
                'result',
                InputArgument::REQUIRED,
                'Path to the result file.'
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '<info>Cleaning Robot in Action</info>',
            '<info>========================</info>',
            '',
        ]);

        $source = $input->getArgument('source');
        $result = $input->getArgument('result');

        // @TODO
    }
}
