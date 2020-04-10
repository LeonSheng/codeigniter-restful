<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\Table(name="user",
 *     uniqueConstraints={@UniqueConstraint(name="username",columns={"username"})},
 *     options={"charset":"utf8mb4", "row_format":"DYNAMIC", "comment":"User Table"})
 */
class User extends BaseEntity
{
    /**
     * @ORM\Column(type="string", length=60, options={"default": "", "comment":"Username"})
     * @var string|null
     */
    private $username;

    /**
     * JsonIgnore
     * @ORM\Column(type="string", length=64, options={"default": "", "fixed":true, "comment":"Password"})
     * @var string|null
     */
    private $password;

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     * @return User
     */
    public function setUsername(?string $username): User
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return User
     */
    public function setPassword(?string $password): User
    {
        $this->password = $password;
        return $this;
    }

}
