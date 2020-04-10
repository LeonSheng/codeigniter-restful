<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass="LoginTraceRepository")
 * @ORM\Table(name="login_trace",
 *     uniqueConstraints={@UniqueConstraint(name="ip",columns={"ip"})},
 *     options={"charset":"utf8mb4", "row_format":"DYNAMIC", "comment":"Login Trace Table"})
 */
class LoginTrace extends BaseEntity
{
    /**
     * @ORM\Column(type="string", length=20, options={"default": "", "comment":"IP address"})
     * @var string|null
     */
    private $ip;

    /**
     * @ORM\Column(type="integer", options={"default": 0, "comment":"Login error count"})
     * @var int|null
     */
    private $error_count;

    /**
     * @ORM\Column(type="boolean", options={"default": false, "comment":"Whether IP address is Locked"})
     * @var bool|null
     */
    private $is_locked;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"comment":"IP address locking time"})
     * @var DateTime|null
     */
    private $lock_time;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"comment":"Last login time"})
     * @var DateTime|null
     */
    private $last_login_time;

    /**
     * @ORM\Column(type="string", length=60, options={"default": "", "comment":"Last login username"})
     * @var string|null
     */
    private $last_login_username;

    /**
     * @ORM\Column(type="string", length=120, options={"default": "", "comment":"Captcha result"})
     * @var string|null
     */
    private $captcha_result;

    /**
     * @ORM\Column(type="integer", options={"default": 0, "comment":"Captcha refresh count"})
     * @var int|null
     */
    private $captcha_refresh_count;

    /**
     * @return string|null
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * @param string|null $ip
     * @return LoginTrace
     */
    public function setIp(?string $ip): LoginTrace
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getErrorCount(): ?int
    {
        return $this->error_count;
    }

    /**
     * @param int|null $error_count
     * @return LoginTrace
     */
    public function setErrorCount(?int $error_count): LoginTrace
    {
        $this->error_count = $error_count;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsLocked(): ?bool
    {
        return $this->is_locked;
    }

    /**
     * @param bool|null $is_locked
     * @return LoginTrace
     */
    public function setIsLocked(?bool $is_locked): LoginTrace
    {
        $this->is_locked = $is_locked;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getLockTime(): ?DateTime
    {
        return $this->lock_time;
    }

    /**
     * @param DateTime|null $lock_time
     * @return LoginTrace
     */
    public function setLockTime(?DateTime $lock_time): LoginTrace
    {
        $this->lock_time = $lock_time;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getLastLoginTime(): ?DateTime
    {
        return $this->last_login_time;
    }

    /**
     * @param DateTime|null $last_login_time
     * @return LoginTrace
     */
    public function setLastLoginTime(?DateTime $last_login_time): LoginTrace
    {
        $this->last_login_time = $last_login_time;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastLoginUsername(): ?string
    {
        return $this->last_login_username;
    }

    /**
     * @param string|null $last_login_username
     * @return LoginTrace
     */
    public function setLastLoginUsername(?string $last_login_username): LoginTrace
    {
        $this->last_login_username = $last_login_username;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCaptchaResult(): ?string
    {
        return $this->captcha_result;
    }

    /**
     * @param string|null $captcha_result
     * @return LoginTrace
     */
    public function setCaptchaResult(?string $captcha_result): LoginTrace
    {
        $this->captcha_result = $captcha_result;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCaptchaRefreshCount(): ?int
    {
        return $this->captcha_refresh_count;
    }

    /**
     * @param int|null $captcha_refresh_count
     * @return LoginTrace
     */
    public function setCaptchaRefreshCount(?int $captcha_refresh_count): LoginTrace
    {
        $this->captcha_refresh_count = $captcha_refresh_count;
        return $this;
    }

}
