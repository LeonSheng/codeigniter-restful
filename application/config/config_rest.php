<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Cors Config
|--------------------------------------------------------------------------
*/
//Access-Control-Allow-Origin. '*' means any origin is acceptable
$config['allowed_origins'] = IS_DEV_MODE ?
    //development
    array(
        '*'
    ) :
    //production
    array(
        'http://domainname',
    );

//Access-Control-Allow-Headers
$config['allowed_headers'] = array(
    'Origin',
    'X-Requested-With',
    'Authorization',
    'Cache-Control',
    'Content-Type',
    'Accept',
    'Access-Control-Request-Method',
);

//Access-Control-Allow-Methods
$config['allowed_methods'] = array(
    'OPTIONS',
    'HEAD',
    'GET',
    'POST',
    'PUT',
    'PATCH',
    'DELETE',
);

//Access-Control-Allow-Credentials. Whether to accept credentials, such as cookies from client.
$config['allow_credentials'] = true;

/*
|--------------------------------------------------------------------------
| HTTPS Support Only
|--------------------------------------------------------------------------
*/
$config['https_only'] = true;

/*
|--------------------------------------------------------------------------
| Authentication Config
|--------------------------------------------------------------------------
*/
$config['authc_enable'] = false;
$config['authc_exclude_uris'] = array(
    '/login'
);

/*
|--------------------------------------------------------------------------
| JWT (Json Web Token) Config
|--------------------------------------------------------------------------
*/
//The secret key to decode access token
$config['jwt_key'] = 'HelloWorld';

//token expire time, in seconds
$config['jwt_expire'] = 30*60;

//token expire time, in seconds, used in cases like 'Remember Me'
$config['jwt_long_expire'] = 30*24*60*60;

/*
|--------------------------------------------------------------------------
| Login Config
|--------------------------------------------------------------------------
*/
//Captcha shows up when login failure count exceeds threshold
$config['captcha_show_threshold'] = 2;

//Max captcha refresh count. Error response will return when exceeds threshold
$config['captcha_refresh_threshold'] = 20;

//IP address will be locked when login failure count exceeds threshold
$config['ip_lock_threshold'] = 10;

//IP address locking time, in seconds
$config['ip_lock_time'] = 30*60;
