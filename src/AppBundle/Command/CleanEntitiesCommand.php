<?php

namespace AppBundle\Command;

use AppBundle\Entity\AppPreference;
use AppBundle\Entity\MilitaryUnit;
use AppBundle\Entity\Testator;
use AppBundle\Repository\AppPreferenceRepository;
use AppBundle\Services\Place;
use Doctrine\ORM\EntityManager;
use FOS\OAuthServerBundle\Entity\ClientManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CleanEntitiesCommand extends ContainerAwareCommand
{
    private $em;
    private $militaryUnit;
    private $place;

    public function __construct(EntityManager $em, \AppBundle\Services\MilitaryUnit $militaryUnit, Place $place)
    {
        $name = "clean:entities";
        $this->em = $em;
        $this->militaryUnit = $militaryUnit;
        $this->place = $place;

        // you *must* call the parent constructor
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('clean:entities')
            ->setDescription('Remove every orphan entity. Use carefully')
            ->setHelp('Remove every orphan entity')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*$helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Continue with this action?', false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }*/

        // --- RUN
        $output->writeln([
            'Cleaning...',
            '============',
        ]);
        $progressBar = new ProgressBar($output, 50);
        $progressBar->start();

        $i = 0;
        while ($i++ < 50) {

            // Entities to clean : will, resource, transcript, place, testator, militaryUnit
            /** @var $testators Testator */
            $testators = $this->em->getRepository('AppBundle:Testator')->findAll();
            foreach ($testators as $testator) {
                if(count($testators->getWills()) == 0) {
                    $this->em->remove($testator);
                }
            }

            /** @var $militaryUnits MilitaryUnit */
            $militaryUnits = $this->em->getRepository('AppBundle:MilitaryUnit')->findAll();
            foreach ($militaryUnits as $militaryUnit) {
                if(count($this->militaryUnit->getTestators($militaryUnit)) == 0) {
                    $this->em->remove($militaryUnit);
                }
            }

            /** @var $militaryUnits MilitaryUnit */
            $places = $this->em->getRepository('AppBundle:Place')->findAll();
            foreach ($places as $place) {
                if(count($this->place->getTestators($place)) == 0 and count($this->place->getWills($place)) == 0) {
                    $this->em->remove($place);
                }
            }

            $this->em->flush();

            $progressBar->advance();
        }

        $progressBar->finish();

        $output->writeln([
            '<info>It\'s done!</info>'
        ]);
    }
}