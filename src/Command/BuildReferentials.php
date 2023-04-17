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

            $events = $this->getEvents($json);
            $eventList = array_unique(array_merge($eventList, $events));

            $relics = $this->getRelics($json);
            $relicList = array_unique(array_merge($relicList, $relics));

            $potions = $this->getPotions($json);
            $potionList = array_unique(array_merge($potionList, $potions));

            $encounters = $this->getEncounters($json);
            $encounterList = array_unique(array_merge($encounterList, $encounters));

            $progressBar->advance();

        }
        $progressBar->finish();

        $refCards = $this->processRef('Cards.json');
        $refEvents = $this->processRef('Events.json');
        $refRelics = $this->processRef('Relics.json');
        $refPotions = $this->processRef('Potions.json');
        $refEncounters = $this->processRef('Encounters.json');

        $this->displayResult($output, $cardList, $refCards, "Cards");
        $this->displayResult($output, $eventList, $refEvents, "Events");
        $this->displayResult($output, $relicList, $refRelics, "Relics");
        $this->displayResult($output, $potionList, $refPotions, "Potions");
        $this->displayResult($output, $encounterList, $refEncounters, "Encounters");

        return Command::SUCCESS;
    }

    private function displayResult(OutputInterface $output, array $list, array $refArr, string $tilte): void
    {
        $output->writeln("\n----- ". $tilte);
        $missingCards = array_diff($list, $refArr);
        $output->writeln($missingCards);
    }

    private function processRef(string $jsonRefFilename): array
    {
        $refPath = $_ENV['PROJECT_DIR'] . 'public/assets/' . $jsonRefFilename;
        $refPath = str_replace('/', DIRECTORY_SEPARATOR, $refPath);
        $refJSON = json_decode(file_get_contents($refPath), true);

        $refJSON = array_keys($refJSON);

        return $refJSON;
    }

    private function getCards(mixed $json): array
    {
        $deck = $json->master_deck;
        $cards = [];
        foreach ($deck as $card) {
            $split = explode("+", $card);
            $cards[] = $split[0];
        }
        return $cards;
    }

    private function getEvents(mixed $json): array
    {
        $arrEventsName = [];
        $events = $json->event_choices;
        foreach ($events as $event)
        {
            $eventName = $event->event_name;
            $arrEventsName[] = $eventName;
        }

        return $arrEventsName;
    }

    private function getRelics(mixed $json): array
    {
        $relicArr = [];
        $relics = $json->relics;
        foreach ($relics as $relic)
        {
            $relicArr[] = $relic;
        }

        return $relicArr;
    }

    private function getPotions(mixed $json): array
    {
        $potionsArr = [];
        $potions = $json->potions_obtained;
        foreach ($potions as $potion)
        {
            $eventName = $potion->key;
            $potionsArr[] = $eventName;
        }
        return $potionsArr;
    }

    private function getEncounters(mixed $json): array
    {
        $encounterArr = [];
        $encounters = $json->damage_taken;
        foreach ($encounters as $encounter)
        {
            $encounterName = $encounter->enemies;
            $potionsArr[] = $encounterName;
        }

        return $encounterArr;
    }
}
