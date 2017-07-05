<?php

namespace UserBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Credentials
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    protected $login;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    protected $password;

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }
}