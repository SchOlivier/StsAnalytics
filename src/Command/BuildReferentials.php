<?php

namespace App\Command;

use App\Service\SaveParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:build')]
class BuildReferentials extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $parser = new SaveParser();

        $filePath = $_ENV['PROJECT_DIR'] . 'testData/runs/';
        $filename = "1626104263.run";
        $folders = ['DEFECT/', 'THE_SILENT/', 'IRONCLAD/', 'WATCHER/'];
        // Lister les fichiers dans chaque folder
        // Pour chacun d'eux :
        /**
         * charger le json
         * lister les cartes, events, reliques, potions, encounters (combats)
         *      pour chacune de ces liste, si on ne trouve pas l'element dans nos fichiers json, l'ajouter.
         */

        $cardList = $eventList  = $relicList = $potionList = $encounterList = [];

        $files = [];

        //todo : gestion d'exception quand on ne trouve pas une carte : créer une carte bidon et ne pas renvoyer null

        foreach ($folders as $folder) {
            $path = $filePath . $folder;
            $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
            
            $folderFiles = array_diff(scandir($path), array('..', '.'));
            $folderFiles = array_map(function ($file) use ($path) {
                return $path . $file;
            }, $folderFiles);

            $files = array_merge($files,$folderFiles);
        }

        $progressBar = new ProgressBar($output, count($files));
        $progressBar->start();

        foreach ($files as $i => $file) {
            $json = json_decode(file_get_contents($file));
            $cards = $this->getCards($json);
            $cardList = array_unique(array_merge($cardList, $cards));

            // $events = $this->getEvents($json);
            // $eventList = array_unique(array_merge($eventList, $events));

            // $relics = $this->getRelics($json);
            // $relicList = array_unique(array_merge($relicList, $relics));

            // $potions = $this->getPotions($json);
            // $potionList = array_unique(array_merge($potionList, $potions));

            // $encounters = $this->getEncounters($json);
            // $encounterList = array_unique(array_merge($encounterList, $encounters));
            $progressBar->advance();

        }
        $progressBar->finish();

        $refCardPath = $_ENV['PROJECT_DIR'] . 'public/assets/Cards.json';
        $refCardPath = str_replace('/', DIRECTORY_SEPARATOR, $refCardPath);
        $refCards = json_decode(file_get_contents($refCardPath), true);

        $refCards = array_keys($refCards);

        $missingCards = array_diff($cardList, $refCards);
        $output->writeln($cardList);

        return Command::SUCCESS;
    }

    private function getCards($json)
    {
        $deck = $json->master_deck;
        $cards = [];
        foreach ($deck as $card) {
            $split = explode("+", $card);
            $cards[] = $split[0];
        }
        return $cards;
    }
}
