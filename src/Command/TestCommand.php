<?
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
        // $parser = new SaveParser();
        // $save = $parser->loadSave("C:\Users\Virgil\Documents\Projets\StsAnalytics\testData\THE_SILENT\1626110219.run");

        // $output->writeln($save);
        
        return Command::SUCCESS;
    }
}