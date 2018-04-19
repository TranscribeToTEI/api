<?php

namespace AppBundle\Services;

use AppBundle\Entity\Comment\Comment;
use AppBundle\Entity\Comment\Thread;
use AppBundle\Entity\Resource;
use AppBundle\Entity\Testator;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Mailer\Mailer;
use Proxies\__CG__\Gedmo\Loggable\Entity\LogEntry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Twig\Environment;
use UserBundle\Entity\User;

class Transcript
{
    private $em;
    private $version;
    private $logger;
    private $user;
    private $mailer;
    private $twig;
    private $siteURL;

    public function __construct(EntityManager $em, Versioning $version, LoggerInterface $logger, \UserBundle\Services\User $user, \Swift_Mailer $mailer, EngineInterface $twig, $siteURL)
    {
        $this->em = $em;
        $this->version = $version;
        $this->logger = $logger;
        $this->user = $user;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->siteURL = $siteURL;
    }

    /**
     * @param $transcript \AppBundle\Entity\Transcript
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove($transcript)
    {
        /** @var $resource \AppBundle\Entity\Resource */
        $resource = $this->em->getRepository("AppBundle:Resource")->findOneBy(array("transcript" => $transcript));
        $resource->setTranscript(null);

        $this->em->remove($transcript);
        $this->em->flush();
    }

    /**
     * @param $transcript \AppBundle\Entity\Transcript
     * @return null|object \AppBundle\Entity\Resource
     */
    public function getResource($transcript)
    {
        return $this->em->getRepository('AppBundle:Resource')->findOneBy(array('transcript' => $transcript));
    }

    /**
     * @param $transcript \AppBundle\Entity\Transcript
     * @return boolean
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function isCurrentlyEdited($transcript)
    {
        /** @var $transcriptLog \AppBundle\Entity\TranscriptLog */
        $transcriptLogs = $this->em->getRepository("AppBundle:TranscriptLog")->findBy(array("transcript" => $transcript, 'isCurrentlyEdited' => true));
        $isCurrentlyEdit = false;

        foreach($transcriptLogs as $transcriptLog) {
            $datetimeLog = new \DateTime($transcriptLog->getUpdateDate()->format('Y-m-d H:i:s'));
            $datetimeNow = new \DateTime('now');
            $interval = $datetimeLog->diff($datetimeNow);

            if(intval($interval->format('%h')) > 1 or intval($interval->format('%d')) > 1 or intval($interval->format('%M')) > 1 or intval($interval->format('%y')) > 1) {
                $transcriptLog->setIsCurrentlyEdited(false);
            } else {
                $isCurrentlyEdit = true;
            }
        }
        $this->em->flush();

        return $isCurrentlyEdit;
    }

    /**
     * @param $transcript \AppBundle\Entity\Transcript
     * @return array
     */
    public function getLogs($transcript)
    {
        /** @var $resource \AppBundle\Entity\Resource */
        return $this->em->getRepository("AppBundle:TranscriptLog")->findBy(array("transcript" => $transcript));
    }

    /**
     * @param $transcript \AppBundle\Entity\Transcript
     * @return array
     */
    public function computeVersions($transcript)
    {
        $versions = $this->version->getVersions($transcript);
        $computedVersions = array();

        foreach ($versions as $version) {
            /** @var $version LogEntry */
            /** @var $user User */
            $user = $this->em->getRepository("UserBundle:User")->findOneBy(array("username" => $version->getUsername()));

            if($user != null) {
                $userContent = ["id" => $user->getId(), 'name' => $user->getName()];
            } else {
                $userContent = null;
            }


            $computedVersions[] =
                [
                    "user" => $userContent,
                    "data" => $version->getData(),
                    "loggedAt" => $version->getLoggedAt(),
                    "id" => $version->getId(),
                    "objectId" => $version->getObjectId(),
                    "objectClass" => $version->getObjectClass(),
                    "version" => $version->getVersion()
                ];
        }

        return $computedVersions;
    }

    /**
     * @param $transcript \AppBundle\Entity\Transcript
     * @param $form FormInterface
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function manageNotifications($transcript, $form) {
        if($form->get('sendNotification')->getData() == true) {
            $resource = $this->getResource($transcript);
            $entity = $resource->getEntity();

            if ($transcript->getSubmitUser() != null and $this->user->getPreference($transcript->getSubmitUser())->getNotificationTranscription() == true) {
                // If the user accepts to receive emails, we send it.
                $message = \Swift_Message::newInstance()
                    ->setSubject('Notification de traitement d\'une transcription - Testaments de Poilus')
                    ->setFrom('testaments-de-poilus@huma-num.fr')
                    ->setTo($transcript->getSubmitUser()->getEmail())
                    ->setBody($this->twig->render(
                        'AppBundle:Transcript:notification.html.twig',
                        array('transcript' => $transcript, 'entity' => $entity)), 'text/html');
                $this->mailer->send($message);
            } elseif($transcript->getSubmitUser() != null) {
                // Else, we send the notification through a comment in special thread
                $thread = $this->em->getRepository('AppBundle:Comment\Thread')->find('users-' . $transcript->getSubmitUser()->getId() . '-0');
                if ($thread == null) {
                    $thread = new Thread();
                    $thread->setCommentable(true);
                    $thread->setId('users-' . $transcript->getSubmitUser()->getId() . '-0');
                    $thread->setPermalink($this->siteURL.'/thread/users-' . $transcript->getSubmitUser()->getId() . '-0');
                    $this->em->persist($thread);
                    $this->em->flush();
                }

                $comment = new Comment();
                $comment->setAuthor($transcript->getUpdateUser());
                $comment->setBody("<p>Ce message vous est adressé par un administrateur ayant traité votre demande de validation de votre transcription du ".$entity->getWill()->getTitle().", ".$this->formatResourceType($resource->getType())." ".$resource->getOrderInWill()."</p>".$transcript->getValidationText());
                $comment->setThread($thread);
                $comment->setState(0);
                $this->em->persist($comment);
                $thread->setNumComments(intval($thread->getNumComments())+1);
                $thread->setLastCommentAt(new \DateTime());
                $this->em->persist($thread);
                $this->em->flush();
            }

            // Then, we repost the message in the thread of the transcript
            $thread = $this->em->getRepository('AppBundle:Comment\Thread')->find('transcript-' . $transcript->getId());
            if ($thread != null) {
                $comment = new Comment();
                $comment->setAuthor($transcript->getUpdateUser());
                $comment->setBody($transcript->getValidationText());
                $comment->setThread($thread);
                $comment->setState(0);
                $this->em->persist($comment);
                $thread->setNumComments(intval($thread->getNumComments())+1);
                $thread->setLastCommentAt(new \DateTime());
                $this->em->persist($thread);
                $this->em->flush();
            }
        }
    }

    private function formatResourceType($type) {
        switch ($type) {
            case 'page':
                return 'page';
                break;
            case 'envelope':
                return 'enveloppe';
                break;
            case 'codicil':
                return 'codicille';
                break;
        }
    }
}