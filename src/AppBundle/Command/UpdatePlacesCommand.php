<?php

namespace AppBundle\Command;

use AppBundle\Entity\AppPreference;
use AppBundle\Entity\Place;
use AppBundle\Repository\AppPreferenceRepository;
use Doctrine\ORM\EntityManager;
use FOS\OAuthServerBundle\Entity\ClientManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class UpdatePlacesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
                ->setName('app:updateplace')

            // the short description shown while running "php bin/console list"
            ->setDescription('Update places value')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Place update',
            '============',
        ]);


        /** @var $em EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        /** @var $places Place[] */
        $places = $em->getRepository('AppBundle:Place')->findAll();
        // creates a new progress bar (50 units)
        $progressBar = new ProgressBar($output, count($places));

        // starts and displays the progress bar
        $progressBar->start();
        foreach ($places as $place) {
            if($place->getIndexName() == null or $place->getIndexName() == "") {
                $output->writeln([
                    '<info>Enter</info>'
                ]);
                $suffixName = "";
                if($place->getCity() != null) {
                    $suffixName .= $place->getCity();
                }
                if($place->getFrenchDepartement() != null) {
                    if(!empty($suffixName)) { $suffixName .= ", "; }
                    $suffixName .= $place->getFrenchDepartement();
                }
                if($place->getFrenchRegion() != null) {
                    if(!empty($suffixName)) { $suffixName .= ", "; }
                    $suffixName .= $place->getFrenchRegion();
                }
                if($place->getCountry() != null) {
                    if(!empty($suffixName)) { $suffixName .= ", "; }
                    $suffixName .= $place->getCountry();
                }

                if(!empty($suffixName)) {
                    $place->setIndexName($place->getName()." (".$suffixName.")");
                } else {
                    $place->setIndexName($place->getName());
                }
            }
            $em->persist($place);
            $em->flush();
            $progressBar->advance();
        }

        $progressBar->finish();
        $output->writeln([
            '<info>It\'s done!</info>'
        ]);
    }
}