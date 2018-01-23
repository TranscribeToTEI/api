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

class TestEmailCommand extends ContainerAwareCommand
{
    private $sender;

    public function __construct($mailer_user)
    {
        $this->sender = $mailer_user;

        // you *must* call the parent constructor
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('test:email')
            ->setDescription('It tests email sending.')
            ->setHelp('It tests email sending.')
            ->addArgument('recipient', InputArgument::REQUIRED, 'The email address where to send an email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mailer = $this->getContainer()->get('mailer');
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom($this->sender)
            ->setTo($input->getArgument('recipient'))
            ->setBody("If you receive this email, then the system is fully functional.")
        ;
        $mailer->send($message);

        $output->writeln(['<info>Email sent to '.$input->getArgument('recipient').'</info>']);
    }
}