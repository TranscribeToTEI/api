<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Access
 *
 * @ORM\Table(name="access")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\AccessRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_preference",
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
 *          "update_preference",
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
 *          "patch_preference",
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
 *          "remove_preference",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 */
class Access
{
    /**
     * @Serializer\Since("0.1")
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
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "accessProperties"})
     *
     * @var bool
     *
     * @ORM\Column(name="isTaxonomyAccess", type="boolean")
     */
    private $isTaxonomyAccess;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "accessProperties"})
     *
     * @var string
     *
     * @ORM\Column(name="taxonomyRequest", type="string", length=255, nullable=true)
     */
    private $taxonomyRequest;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content", "accessProperties"})
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\MaxDepth(2)
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
     * Set isTaxonomyAccess
     *
     * @param boolean $isTaxonomyAccess
     *
     * @return Access
     */
    public function setIsTaxonomyAccess($isTaxonomyAccess)
    {
        $this->isTaxonomyAccess = $isTaxonomyAccess;

        return $this;
    }

    /**
     * Get isTaxonomyAccess
     *
     * @return bool
     */
    public function getIsTaxonomyAccess()
    {
        return $this->isTaxonomyAccess;
    }

    /**
     * Set taxonomyRequest
     *
     * @param string $taxonomyRequest
     *
     * @return Access
     */
    public function setTaxonomyRequest($taxonomyRequest)
    {
        $this->taxonomyRequest = $taxonomyRequest;

        return $this;
    }

    /**
     * Get taxonomyRequest
     *
     * @return string
     */
    public function getTaxonomyRequest()
    {
        return $this->taxonomyRequest;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return Access
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
