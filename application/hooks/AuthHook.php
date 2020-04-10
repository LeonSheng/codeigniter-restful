<?php /** @noinspection PhpIncludeInspection */
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'config/errors.php';

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

class AuthHook
{
    private $ci;

    public function __construct()
    {
        $this->ci = &get_instance();
    }

    /**
     * Token Authentication
     */
    public function index()
    {
        if (!config_item('authc_enable')) {
            return;
        }
        $requestUri = $_SERVER['REQUEST_URI'];
        if ($this->skip($requestUri))
            return;

        $headers = $this->ci->input->request_headers();
        $result = $this->verifyAccessToken($headers);
        if ($result['errcode'] !== 0)
            show_json_error($result, HTTP_UNAUTHORIZED, false);
    }

    private function skip(string $requestUri): bool
    {
        $authcExcludeUris = config_item('authc_exclude_uris');
        return array_search($requestUri, $authcExcludeUris) !== false;
    }

    private function verifyAccessToken(array $headers): array
    {
        $accessToken = null;
        if (array_key_exists('Authorization', $headers)) {
            $accessToken = $headers['Authorization'];
        } else if (array_key_exists('authorization', $headers)) {
            $accessToken = $headers['authorization'];
        }
        if ($accessToken === null) {
            return ERROR_TOKEN_INVALID;
        }
        if (empty($accessToken) || strpos($accessToken, 'Bearer ') !== 0) {
            return ERROR_TOKEN_INVALID;
        }
        list($accessToken) = sscanf($accessToken, 'Bearer %s');
        try {
            $decoded_array = (array)JWT::decode($accessToken, config_item('jwt_key'), array('HS256'));
            if (!array_key_exists('user_id', $decoded_array)) {
                return ERROR_TOKEN_INVALID;
            }
        } catch (ExpiredException $e) {
            return ERROR_TOKEN_EXPIRED;
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return ERROR_TOKEN_INVALID;
        }
        return ERROR_SUCCESS;
    }
}
