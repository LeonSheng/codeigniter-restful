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
     * @var string|null
     */
    private $id;

    /**
     * @ORM\Column(name="create_time", type="datetime", nullable=true, options={"comment":"create time"})
     * @var DateTime|null
     */
    private $createTime;

    /**
     * @ORM\Column(name="update_time", type="datetime", nullable=true, options={"comment":"update time"})
     * @var DateTime|null
     */
    private $updateTime;

    /**
     * @ORM\Column(name="is_deleted", type="boolean", options={"default":false, "comment":"Deleted Flag"})
     * @var bool|null
     */
    private $isDeleted;

    /**
     * JsonIgnore
     * @ORM\Version
     * @ORM\Column(name="version", type="integer", options={"default":0, "comment":"Version Number"})
     * @var int|null
     */
    private $version;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return BaseEntity
     */
    public function setId(?string $id): BaseEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getCreateTime(): ?DateTime
    {
        return $this->createTime;
    }

    /**
     * @param DateTime|null $createTime
     * @return BaseEntity
     */
    public function setCreateTime(?DateTime $createTime): BaseEntity
    {
        $this->createTime = $createTime;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdateTime(): ?DateTime
    {
        return $this->updateTime;
    }

    /**
     * @param DateTime|null $updateTime
     * @return BaseEntity
     */
    public function setUpdateTime(?DateTime $updateTime): BaseEntity
    {
        $this->updateTime = $updateTime;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool|null $isDeleted
     * @return BaseEntity
     */
    public function setIsDeleted(?bool $isDeleted): BaseEntity
    {
        $this->isDeleted = $isDeleted;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getVersion(): ?int
    {
        return $this->version;
    }

    /**
     * @param int|null $version
     * @return BaseEntity
     */
    public function setVersion(?int $version): BaseEntity
    {
        $this->version = $version;
        return $this;
    }

}
