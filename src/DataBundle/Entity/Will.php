<?php

namespace DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Will
 *
 * @ORM\Table(name="will")
 * @ORM\Entity(repositoryClass="DataBundle\Repository\WillRepository")
 */
class Will
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="minuteDate", type="datetime")
     */
    private $minuteDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="willWritingDate", type="datetime")
     */
    private $willWritingDate;

    /**
     * @var string
     *
     * @ORM\Column(name="willWritingPlace", type="string", length=255)
     */
    private $willWritingPlace;

    /**
     * @var string
     *
     * @ORM\Column(name="testator", type="string", length=255)
     */
    private $testator;


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
     * Set title
     *
     * @param string $title
     *
     * @return Will
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set minuteDate
     *
     * @param \DateTime $minuteDate
     *
     * @return Will
     */
    public function setMinuteDate($minuteDate)
    {
        $this->minuteDate = $minuteDate;

        return $this;
    }

    /**
     * Get minuteDate
     *
     * @return \DateTime
     */
    public function getMinuteDate()
    {
        return $this->minuteDate;
    }

    /**
     * Set willWritingDate
     *
     * @param \DateTime $willWritingDate
     *
     * @return Will
     */
    public function setWillWritingDate($willWritingDate)
    {
        $this->willWritingDate = $willWritingDate;

        return $this;
    }

    /**
     * Get willWritingDate
     *
     * @return \DateTime
     */
    public function getWillWritingDate()
    {
        return $this->willWritingDate;
    }

    /**
     * Set willWritingPlace
     *
     * @param string $willWritingPlace
     *
     * @return Will
     */
    public function setWillWritingPlace($willWritingPlace)
    {
        $this->willWritingPlace = $willWritingPlace;

        return $this;
    }

    /**
     * Get willWritingPlace
     *
     * @return string
     */
    public function getWillWritingPlace()
    {
        return $this->willWritingPlace;
    }

    /**
     * Set testator
     *
     * @param string $testator
     *
     * @return Will
     */
    public function setTestator($testator)
    {
        $this->testator = $testator;

        return $this;
    }

    /**
     * Get testator
     *
     * @return string
     */
    public function getTestator()
    {
        return $this->testator;
    }
}

