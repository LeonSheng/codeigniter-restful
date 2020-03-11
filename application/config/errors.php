<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** @var CI_Lang $langInstance */
$lang = get_instance()->lang;

define('ERROR_SUCCESS', ['errcode' => 0, 'error' => $lang->line('ERROR_SUCCESS')]);
define('ERROR_UNKNOWN', ['errcode' => 99999, 'error' => $lang->line('ERROR_UNKNOWN')]);

define('ERROR_TOKEN_INVALID', ['errcode' => 10001, 'error' => $lang->line('ERROR_TOKEN_INVALID')]);
define('ERROR_TOKEN_EXPIRED', ['errcode' => 10002, 'error' => $lang->line('ERROR_TOKEN_EXPIRED')]);

define('ERROR_LOGIN_U_P_REQUIRED', ['errcode' => 10101, 'error' => $lang->line('ERROR_LOGIN_U_P_REQUIRED')]);
define('ERROR_LOGIN_U_P_INCORRECT', ['errcode' => 10102, 'error' => $lang->line('ERROR_LOGIN_U_P_INCORRECT')]);
define('ERROR_LOGIN_CAPTCHA_INCORRECT', ['errcode' => 10103, 'error' => $lang->line('ERROR_LOGIN_CAPTCHA_INCORRECT')]);
define('ERROR_LOGIN_IP_LOCKED', ['errcode' => 10104, 'error' => $lang->line('ERROR_LOGIN_IP_LOCKED')]);

define('ERROR_USER_USERNAME_REQUIRED', ['errcode' => 10201, 'error' => $lang->line('ERROR_USER_USERNAME_REQUIRED')]);
define('ERROR_USER_PASSWORD_REQUIRED', ['errcode' => 10202, 'error' => $lang->line('ERROR_USER_PASSWORD_REQUIRED')]);
define('ERROR_USER_USERNAME_EXISTS', ['errcode' => 10203, 'error' => $lang->line('ERROR_USER_USERNAME_EXISTS')]);
