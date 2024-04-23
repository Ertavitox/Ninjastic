<?php

namespace App\Command;

use App\Service\XmlProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessDirtyWordsXmlCommand extends Command
{
    private $xmlProcessor;

    public function __construct(XmlProcessor $xmlProcessor)
    {
        parent::__construct();
        $this->xmlProcessor = $xmlProcessor;
    }

    protected function configure()
    {
        $this->setName('app:process-dirty-words-xml')
            ->setDescription('Processes an XML file containing dirty words.')
            ->setHelp('This command allows you to process a dirty words XML file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $xmlPath = getcwd() . '/public/dirtywords.xml';
        $output->writeln('Processing XML file...: ' . $xmlPath);
        $this->xmlProcessor->process($xmlPath);
        $output->writeln('XML processing complete.');

        return Command::SUCCESS;
    }
}
