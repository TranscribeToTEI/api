<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Preference
 *
 * @ORM\Table(name="preference")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\PreferenceRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_preference",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "modify",
 *      href = @Hateoas\Route(
 *          "update_preference",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "patch",
 *      href = @Hateoas\Route(
 *          "patch_preference",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "remove_preference",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 */
class Preference
{
    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     *
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     *
     * @Assert\Choice({"leftRead-centerHelp-rightImage", "leftRead-centerImage-rightHelp", "leftHelp-centerRead-rightImage", "leftHelp-centerImage-rightRead", "leftImage-centerHelp-rightRead", "leftImage-centerRead-rightHelp"})
     * @var string
     *
     * @ORM\Column(name="transcriptionDeskPosition", type="string", length=255)
     */
    private $transcriptionDeskPosition;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set transcriptionDeskPosition
     *
     * @param string $transcriptionDeskPosition
     *
     * @return Preference
     */
    public function setTranscriptionDeskPosition($transcriptionDeskPosition)
    {
        $this->transcriptionDeskPosition = $transcriptionDeskPosition;

        return $this;
    }

    /**
     * Get transcriptionDeskPosition
     *
     * @return string
     */
    public function getTranscriptionDeskPosition()
    {
        return $this->transcriptionDeskPosition;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return Preference
     */
    public function setUser(\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
