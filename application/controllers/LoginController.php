<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Firebase\JWT\JWT;

class LoginController extends RestController
{
    /** @var UserRepository */
    private $userRepository;
    /** @var LoginTraceRepository */
    private $loginTraceRepository;

    function __construct()
    {
        parent::__construct();
        $this->load->library('doctrine');
        $this->userRepository = $this->doctrine->em->getRepository(User::class);
        $this->loginTraceRepository = $this->doctrine->em->getRepository(LoginTrace::class);
    }

    /**
     * Login
     *
     * @throws
     */
	public function index_post()
	{
	    //Check if the ip address is locked
        $ip = $this->input->ip_address();
        /** @var LoginTrace $loginTrace */
        $loginTrace = $this->loginTraceRepository->findOneBy(['ip' => $ip]);
        $this->checkIpAddress($ip, $loginTrace);

        //Check username and password
        $body = $this->requestBody;
        /** @var User $user */
        $user = ObjectUtils::fromArray($body, User::class);
        $username = $user->getUsername();
        $password = $user->getPassword();
        if (StringUtils::isBlank($username)) {
            $this->fail(ERROR_LOGIN_U_P_REQUIRED, $loginTrace);
        }
        $user = $this->userRepository->findOneBy(['username' => $username], null, false);
        if ($user === null) {
            $this->fail(ERROR_LOGIN_U_P_INCORRECT, $loginTrace);
        }
        if ($password !== $user->getPassword()) {
            $this->fail(ERROR_LOGIN_U_P_INCORRECT, $loginTrace);
        }

        //Should check captcha if the current login requires it
        $captchaResult = $loginTrace->getCaptchaResult();
        if ($captchaResult !== '') {
            $captcha = array_key_exists('captcha', $body) ? (string)$body['captcha'] : '';
            if ($captcha !== $captchaResult) {
                $this->fail(ERROR_LOGIN_CAPTCHA_INCORRECT, $loginTrace);
            }
        }

        //Generate access token
        $rememberMe = array_key_exists('remember', $body) ? $body['remember'] : false;
        $payload = array(
            'user_id' => $user->getId(),
            'username' => $user->getUsername(),
            'password' => $user->getPassword(),
            'exp' => time() + ($rememberMe ? config_item('jwt_long_expire') : config_item('jwt_expire')),
        );
        $accessToken = JWT::encode($payload, config_item('jwt_key'));

        //Record the login trace
        $loginTrace->setIsLocked(false)
            ->setErrorCount(0)
            ->setLastLoginTime(new DateTime())
            ->setLastLoginUsername($username)
            ->setCaptchaResult('')
            ->setCaptchaRefreshCount(0);
        $this->doctrine->em->flush();
        $this->response([
            'errcode' => 0,
            'username' => $username,
            'accessToken' => $accessToken
        ], HTTP_OK);
	}

    /**
     * Refresh captcha
     *
     * @throws
     */
    public function captcha_get()
    {
        //Check if the ip address is locked
        $ip = $this->input->ip_address();
        /** @var LoginTrace $loginTrace */
        $loginTrace = $this->loginTraceRepository->findOneBy(['ip' => $ip]);
        $this->checkIpAddress($ip, $loginTrace);

        //Refresh a new captcha image to client
        $captchaRefreshCount = $loginTrace->getCaptchaRefreshCount();
        $captchaResult = $loginTrace->getCaptchaResult();
        $image = CaptchaUtils::generateCalculate($captchaResult);
        $loginTrace->setCaptchaResult($captchaResult)->setCaptchaRefreshCount(++$captchaRefreshCount);
        $this->doctrine->em->flush();
        $this->response([
            'errcode' => 0,
            'captcha' => $image
        ], HTTP_OK);
    }

    /**
     * Check if the ip address is locked. Unlock it after a certain time
     *
     * @param string $ip
     * @param LoginTrace|null $loginTrace
     * @throws
     */
    public function checkIpAddress(string $ip, &$loginTrace): void
    {
        if ($loginTrace === null) {
            $loginTrace = new LoginTrace();
            RepositoryUtils::initializeForCreate($loginTrace);
            $loginTrace->setIp($ip);
            $loginTrace->setLockTimeNull();
            $loginTrace->setLastLoginTimeNull();
            $this->doctrine->em->persist($loginTrace);
        }
        if ($loginTrace->getIsLocked()) {
            $lockTime = $loginTrace->getLockTime()->getTimestamp();
            $nowTime = time();
            if (($nowTime - $lockTime) < config_item('ip_lock_time')) {
                //The IP address is still locked
                $this->response(ERROR_LOGIN_IP_LOCKED, HTTP_OK);
            } else {
                //Unlock the IP address
                $loginTrace->setErrorCount(0)
                    ->setCaptchaRefreshCount(0)
                    ->setIsLocked(false);
            }
        }
        if ($loginTrace->getCaptchaRefreshCount() > config_item('captcha_refresh_threshold')) {
            $this->fail(ERROR_LOGIN_IP_LOCKED, $loginTrace);
        }
    }

    /**
     * Return failure information
     *
     * @param array $error
     * @param LoginTrace $loginTrace
     * @throws
     */
    private function fail(array $error, LoginTrace &$loginTrace)
    {
        $captchaRequestCount = $loginTrace->getCaptchaRefreshCount();
        $errorCount = $loginTrace->getErrorCount();
        $loginTrace->setErrorCount(++$errorCount);
        if ($errorCount > config_item('ip_lock_threshold') || $captchaRequestCount > config_item('captcha_refresh_threshold')) {
            //When error count of login exceeds a big threshold, lock the current IP address
            $loginTrace->setIsLocked(true)->setLockTime(new DateTime());
            $error = ERROR_LOGIN_IP_LOCKED;
        } elseif ($errorCount > config_item('captcha_show_threshold')) {
            //When error count of login exceeds a small threshold, add captcha image
            $captchaResult = $loginTrace->getCaptchaResult();
            $image = CaptchaUtils::generateCalculate($captchaResult);
            $loginTrace->setCaptchaResult($captchaResult)->setCaptchaRefreshCount(++$captchaRequestCount);
            $error['captcha'] = $image;
        }
        $this->doctrine->em->flush();
        $this->response($error, HTTP_OK);
    }
}
