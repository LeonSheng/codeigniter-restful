<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
class BaseEntity
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=24 , options={"fixed":true, "comment":"id"})
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"comment":"create time"})
     * @var DateTime
     */
    private $create_time;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"comment":"update time"})
     * @var DateTime
     */
    private $update_time;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return BaseEntity
     */
    public function setId(string $id): BaseEntity
    {
        $this->id = $id;
        return $this;
    }

    public function setIdNull()
    {
        $this->id = null;
    }

    /**
     * @return DateTime
     */
    public function getCreateTime(): DateTime
    {
        return $this->create_time;
    }

    /**
     * @param DateTime $create_time
     * @return BaseEntity
     */
    public function setCreateTime(DateTime $create_time): BaseEntity
    {
        $this->create_time = $create_time;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdateTime(): DateTime
    {
        return $this->update_time;
    }

    /**
     * @param DateTime $update_time
     * @return BaseEntity
     */
    public function setUpdateTime(DateTime $update_time): BaseEntity
    {
        $this->update_time = $update_time;
        return $this;
    }

}
