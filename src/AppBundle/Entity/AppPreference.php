<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * AppPreference
 *
 * @ORM\Table(name="app_preference")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AppPreferenceRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_app_preference",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 * @Hateoas\Relation(
 *      "modify",
 *      href = @Hateoas\Route(
 *          "update_app_preference",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 * @Hateoas\Relation(
 *      "patch",
 *      href = @Hateoas\Route(
 *          "patch_app_preference",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "remove_app_preference",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 */
class AppPreference
{
    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "id"})
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
     * @Serializer\Groups({"full", "content"})
     *
     * @var int
     *
     * @ORM\Column(name="helpHomeContent", type="integer", nullable=true, unique=true)
     */
    private $helpHomeContent;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var int
     *
     * @ORM\Column(name="helpInsideHomeContent", type="integer", nullable=true, unique=true)
     */
    private $helpInsideHomeContent;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var int
     *
     * @ORM\Column(name="discoverHomeContent", type="integer", nullable=true, unique=true)
     */
    private $discoverHomeContent;

    /**
     * @Serializer\Since("1.0")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @var string
     *
     * @ORM\Column(name="projectTitle", type="string", length=255, unique=true)
     */
    private $projectTitle;


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
     * Set helpHomeContent
     *
     * @param integer $helpHomeContent
     *
     * @return AppPreference
     */
    public function setHelpHomeContent($helpHomeContent)
    {
        $this->helpHomeContent = $helpHomeContent;

        return $this;
    }

    /**
     * Get helpHomeContent
     *
     * @return int
     */
    public function getHelpHomeContent()
    {
        return $this->helpHomeContent;
    }

    /**
     * Set discoverHomeContent
     *
     * @param integer $discoverHomeContent
     *
     * @return AppPreference
     */
    public function setDiscoverHomeContent($discoverHomeContent)
    {
        $this->discoverHomeContent = $discoverHomeContent;

        return $this;
    }

    /**
     * Get discoverHomeContent
     *
     * @return int
     */
    public function getDiscoverHomeContent()
    {
        return $this->discoverHomeContent;
    }

    /**
     * Set projectTitle
     *
     * @param string $projectTitle
     *
     * @return AppPreference
     */
    public function setProjectTitle($projectTitle)
    {
        $this->projectTitle = $projectTitle;

        return $this;
    }

    /**
     * Get projectTitle
     *
     * @return string
     */
    public function getProjectTitle()
    {
        return $this->projectTitle;
    }

    /**
     * Set helpInsideHomeContent
     *
     * @param integer $helpInsideHomeContent
     *
     * @return AppPreference
     */
    public function setHelpInsideHomeContent($helpInsideHomeContent)
    {
        $this->helpInsideHomeContent = $helpInsideHomeContent;

        return $this;
    }

    /**
     * Get helpInsideHomeContent
     *
     * @return integer
     */
    public function getHelpInsideHomeContent()
    {
        return $this->helpInsideHomeContent;
    }
}
