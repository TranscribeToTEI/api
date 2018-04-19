<?php

namespace AppBundle\Entity\Comment;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Comment as BaseComment;
use FOS\CommentBundle\Model\SignedCommentInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 *
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 */
class Comment extends BaseComment implements SignedCommentInterface
{
    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "id"})
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * Thread of this comment
     *
     * @var Thread
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Comment\Thread")
     */
    protected $thread;

    /**
     * Author of the comment
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Serializer\MaxDepth(1)
     *
     * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     * @var \UserBundle\Entity\User
     */
    protected $author;

    /**
     * @param UserInterface $author
     */
    public function setAuthor(UserInterface $author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getAuthorName()
    {
        if (null === $this->getAuthor()) {
            return 'Anonymous';
        }

        return $this->getAuthor()->getName();
    }
}
