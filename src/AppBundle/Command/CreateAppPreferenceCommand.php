<?php

namespace AppBundle\Command;

use AppBundle\Entity\AppPreference;
use AppBundle\Repository\AppPreferenceRepository;
use Doctrine\ORM\EntityManager;
use FOS\OAuthServerBundle\Entity\ClientManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CreateAppPreferenceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
                ->setName('app:preferences')

            // the short description shown while running "php bin/console list"
            ->setDescription('Setting up the app preferences')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to personalize the preferences of the app')
            ->addArgument('title', InputArgument::REQUIRED, 'The title of the project.')
            ->addArgument('systemEmail', InputArgument::REQUIRED, 'The email to use in the system.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'App Preferences',
            '============',
        ]);

        /** @var $em EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var $appReferenceRepository AppPreferenceRepository */
        $appReferenceRepository = $em->getRepository('AppBundle:AppPreference');
        if(count($appReferenceRepository->findAll()) == 0) {
            $appPreference = new AppPreference();
        } else {
            $appPreference = $appReferenceRepository->findAll()[0];
        }
        $appPreference->setProjectTitle($input->getArgument('title'));
        $appPreference->setSystemEmail($input->getArgument('systemEmail'));
        $appPreference->setTaxonomyEditAccess("forbidden");
        $em->persist($appPreference);
        $em->flush();

        $output->writeln([
            '<info>It\'s done!</info>'
        ]);
    }
}