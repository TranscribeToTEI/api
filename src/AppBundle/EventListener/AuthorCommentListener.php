<?php

namespace AppBundle\EventListener;

use FOS\CommentBundle\Events;
use FOS\CommentBundle\Event\CommentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Security;

/**
 * Listener responsible to send email notifications when a comment is persisted
 */
class AuthorCommentListener implements EventSubscriberInterface
{
    private $token_storage;

    /**
     * Constructor.
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->token_storage = $tokenStorage;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::COMMENT_PRE_PERSIST => 'onCommentPrePersistTest',
        );
    }

    public function onCommentPrePersist(CommentEvent $event)
    {
        $comment = $event->getComment();
        $comment->setAuthor($this->token_storage->getToken()->getUser());
    }
}