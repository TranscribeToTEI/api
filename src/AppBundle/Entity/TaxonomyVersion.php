<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * TaxonomyVersion
 *
 * @ORM\Table(name="taxonomy_version")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaxonomyVersionRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 * @Gedmo\Loggable
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_taxonomy_version",
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
 *          "update_taxonomy_version",
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
 *          "patch_taxonomy_version",
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
 *          "remove_taxonomy_version",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          groups={"full", "links"}
 *     )
 * )
 */
class TaxonomyVersion
{
    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "id"})
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The user who reviewed the version. If null, meaning nobody reviewed it
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     * @Gedmo\Versioned
     * @Serializer\MaxDepth(1)
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $reviewBy;

    /**
     * Version of the entity reviewed
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @var int
     *
     * @ORM\Column(name="versionId", type="integer")
     */
    private $versionId;

    /**
     * Type of the entity reviewed
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     *
     * @var string
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="taxonomyType", type="string", length=255)
     */
    private $taxonomyType;

    /**
     * Related testator if relevant
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Serializer\MaxDepth(1)
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Testator")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $testator;

    /**
     * Related place if relevant
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Serializer\MaxDepth(1)
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $place;

    /**
     * Related military unit if relevant
     *
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "content"})
     * @Serializer\MaxDepth(1)
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MilitaryUnit")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $militaryUnit;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     * @Serializer\MaxDepth(1)
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $createUser;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     *
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createDate", type="datetime", nullable=false)
     */
    protected $createDate;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     * @Gedmo\Versioned
     * @Serializer\MaxDepth(1)
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $updateUser;

    /**
     * @Serializer\Since("0.1")
     * @Serializer\Expose
     * @Serializer\Groups({"full", "metadata"})
     * @Gedmo\Versioned
     *
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updateDate", type="datetime", nullable=false)
     */
    protected $updateDate;


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
     * Set taxonomyType
     *
     * @param string $taxonomyType
     *
     * @return TaxonomyVersion
     */
    public function setTaxonomyType($taxonomyType)
    {
        $this->taxonomyType = $taxonomyType;

        return $this;
    }

    /**
     * Get taxonomyType
     *
     * @return string
     */
    public function getTaxonomyType()
    {
        return $this->taxonomyType;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return TaxonomyVersion
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     *
     * @return TaxonomyVersion
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Set reviewBy
     *
     * @param \UserBundle\Entity\User $reviewBy
     *
     * @return TaxonomyVersion
     */
    public function setReviewBy(\UserBundle\Entity\User $reviewBy = null)
    {
        $this->reviewBy = $reviewBy;

        return $this;
    }

    /**
     * Get reviewBy
     *
     * @return \UserBundle\Entity\User
     */
    public function getReviewBy()
    {
        return $this->reviewBy;
    }

    /**
     * Set testator
     *
     * @param \AppBundle\Entity\Testator $testator
     *
     * @return TaxonomyVersion
     */
    public function setTestator(\AppBundle\Entity\Testator $testator = null)
    {
        $this->testator = $testator;

        return $this;
    }

    /**
     * Get testator
     *
     * @return \AppBundle\Entity\Testator
     */
    public function getTestator()
    {
        return $this->testator;
    }

    /**
     * Set place
     *
     * @param \AppBundle\Entity\Place $place
     *
     * @return TaxonomyVersion
     */
    public function setPlace(\AppBundle\Entity\Place $place = null)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return \AppBundle\Entity\Place
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set militaryUnit
     *
     * @param \AppBundle\Entity\MilitaryUnit $militaryUnit
     *
     * @return TaxonomyVersion
     */
    public function setMilitaryUnit(\AppBundle\Entity\MilitaryUnit $militaryUnit = null)
    {
        $this->militaryUnit = $militaryUnit;

        return $this;
    }

    /**
     * Get militaryUnit
     *
     * @return \AppBundle\Entity\MilitaryUnit
     */
    public function getMilitaryUnit()
    {
        return $this->militaryUnit;
    }

    /**
     * Set createUser
     *
     * @param \UserBundle\Entity\User $createUser
     *
     * @return TaxonomyVersion
     */
    public function setCreateUser(\UserBundle\Entity\User $createUser = null)
    {
        $this->createUser = $createUser;

        return $this;
    }

    /**
     * Get createUser
     *
     * @return \UserBundle\Entity\User
     */
    public function getCreateUser()
    {
        return $this->createUser;
    }

    /**
     * Set updateUser
     *
     * @param \UserBundle\Entity\User $updateUser
     *
     * @return TaxonomyVersion
     */
    public function setUpdateUser(\UserBundle\Entity\User $updateUser = null)
    {
        $this->updateUser = $updateUser;

        return $this;
    }

    /**
     * Get updateUser
     *
     * @return \UserBundle\Entity\User
     */
    public function getUpdateUser()
    {
        return $this->updateUser;
    }

    /**
     * Set versionId
     *
     * @param integer $versionId
     *
     * @return TaxonomyVersion
     */
    public function setVersionId($versionId)
    {
        $this->versionId = $versionId;

        return $this;
    }

    /**
     * Get versionId
     *
     * @return integer
     */
    public function getVersionId()
    {
        return $this->versionId;
    }
}
