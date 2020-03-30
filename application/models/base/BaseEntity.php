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
     * @ORM\Column(name="create_time", type="datetime", nullable=true, options={"comment":"create time"})
     * @var DateTime
     */
    private $createTime;

    /**
     * @ORM\Column(name="update_time", type="datetime", nullable=true, options={"comment":"update time"})
     * @var DateTime
     */
    private $updateTime;

    /**
     * @ORM\Column(name="is_deleted", type="boolean", options={"default":false, "comment":"Deleted Flag"})
     * @var bool
     */
    private $isDeleted;

    /**
     * JsonIgnore
     * @ORM\Version
     * @ORM\Column(name="version", type="integer", options={"default":0, "comment":"Version Number"})
     * @var int
     */
    private $version;

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
        return $this->createTime;
    }

    /**
     * @param DateTime $createTime
     * @return BaseEntity
     */
    public function setCreateTime(DateTime $createTime): BaseEntity
    {
        $this->createTime = $createTime;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdateTime(): DateTime
    {
        return $this->updateTime;
    }

    /**
     * @param DateTime $updateTime
     * @return BaseEntity
     */
    public function setUpdateTime(DateTime $updateTime): BaseEntity
    {
        $this->updateTime = $updateTime;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     * @return BaseEntity
     */
    public function setIsDeleted(bool $isDeleted): BaseEntity
    {
        $this->isDeleted = $isDeleted;
        return $this;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param int $version
     * @return BaseEntity
     */
    public function setVersion(int $version): BaseEntity
    {
        $this->version = $version;
        return $this;
    }

}
