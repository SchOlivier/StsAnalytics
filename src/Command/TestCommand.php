<?php
namespace App\Command;

use App\Service\SaveParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:test')]
class TestCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $parser = new SaveParser();

        $filePath = __DIR__ . '/../../testData/runs/IRONCLAD/' ;
        $filename = "1625301161.run";

        $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);

        $save = $parser->loadSave($filePath . $filename);

        $output->writeln($save);
        
        return Command::SUCCESS;
    }
}