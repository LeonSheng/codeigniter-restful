<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RestController extends CI_Controller
{
    /**
     * The parameters from a request. Url query, url route based, form based parameters will be
     * merged into requestParams variable together.
     *
     * @var array
     */
    protected $requestParams = [];

    /**
     * The body from a request
     *
     * @var string|array
     */
    protected $requestBody;

	public function __construct()
	{
		parent::__construct();
        $this->output->parse_exec_vars = FALSE;
		$this->addResponseCorsHeaders();
        if ($this->input->method() === 'options') {
            //stop here for options request
            $this->response(null, HTTP_OK);
        }
        $this->parseRequestParamsAndBody();
	}

    /**
     * remap to call real controller method
     *
     * @param string $method
     * @throws ReflectionException
     * @throws Exception
     */
    public function _remap($method)
    {
        //append http method suffix
        $method = $method . '_' . $this->input->method();
        if (!method_exists($this, $method)) {
            show_json_error(STATUS_TEXT[HTTP_METHOD_NOT_ALLOWED], HTTP_METHOD_NOT_ALLOWED, false);
        }

        //handle method parameters to pass in
        $params = [];
        $reflectionClass = new ReflectionClass($this);
        $reflectionMethod = $reflectionClass->getMethod($method);
        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            // try to map parameter name first
            $parameterName = $reflectionParameter->getName();
            $parameterType = $reflectionParameter->getType();
            $value = null;
            if (array_key_exists($parameterName, $this->requestParams)) {
                $value = $this->requestParams[$parameterName];
            }

            //Convert value if parameter type is declared
            if ($parameterType !== null) {
                $parameterTypeName = $parameterType->getName();
                if ($parameterType->isBuiltin()) {
                    //primitive type
                    switch ($parameterTypeName) {
                        case 'string':
                            $value = (string)$value;
                            break;
                        case 'int':
                            $value = (int)$value;
                            break;
                        case 'float':
                            $value = (float)$value;
                            break;
                        case 'bool':
                            $value = (bool)$value;
                            break;
                        case 'object':
                        case 'array':
                            if ($value === null && is_array($this->requestBody)) {
                                $value = $this->requestBody;
                            }
                            $value = (array)$value;
                            break;
                        default:
                            break;
                    }
                }
                else {
                    $reflectionTypeClass = new ReflectionClass($parameterTypeName);
                    if ($reflectionTypeClass->isInternal()) {
                        //internal class type. add more handling if need
                        switch ($parameterTypeName) {
                            case 'DateTime':
                                $datetime = new DateTime();
                                $datetime->setTimestamp($value);
                                $value = $datetime;
                                break;
                            default:
                                break;
                        }
                    } else {
                        //custom class type
                        $customClassValues = $this->requestParams;
                        if (is_array($this->requestBody)) {
                            $customClassValues = array_merge($customClassValues, $this->requestBody);
                        }
                        $value = ObjectUtils::fromArray($customClassValues, $parameterTypeName);
                    }
                }

                if ($value === null && !$parameterType->allowsNull()) {
                    $message = sprintf('Cannot parse %s::%s method parameter: %s',
                        $reflectionClass->getName(),
                        $reflectionMethod->getName(),
                        $parameterName);
                    show_json_error($message, HTTP_BAD_REQUEST, true);
                }
                array_push($params, $value);
            }
            // No parameter type declared, just assign value, it's safe.
            else {
                array_push($params, $value);
            }
        }
        call_user_func_array([$this, $method], $params);
    }

    /**
     * Get Request content type, ignoring charset string
     * @return string
     */
    protected function getRequestContentType(): string
    {
        $content_type = $this->input->server('CONTENT_TYPE');
        $content_type === null && $content_type = '';
        return current(explode(';', $content_type));
    }

    /**
     * @param array|string|null $data
     * @param int $http_code
     * @param string $content_type
     */
    protected function response($data, int $http_code, string $content_type = 'application/json')
    {
        ob_start();
        $data === null && $data = '';
        $http_code <= 0 && $http_code = HTTP_OK;

        //content type
        $this->output->set_content_type($content_type, $this->config->item('charset'));

        //body
        if ($content_type === 'application/json' && is_array($data)) {
            $this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
        } else {
            $this->output->set_output($data);
        }

        //status code
        $this->output->set_status_header($http_code);
        $this->output->_display();
        exit;
    }

    private function addResponseCorsHeaders()
    {
        $allowed_origins = $this->config->item('allowed_origins');
        $allowed_headers = $this->config->item('allowed_headers');
        $allowed_methods = $this->config->item('allowed_methods');
        $allow_credentials = $this->config->item('allow_credentials');

        if (in_array('*', $allowed_origins)) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: ' . implode(', ', $allowed_headers));
            header('Access-Control-Allow-Methods: ' . implode(', ', $allowed_methods));
        } else {
            $origin = $this->input->server('HTTP_ORIGIN');
            if (in_array($origin, $allowed_origins)) {
                header('Access-Control-Allow-Origin: ' . $origin);
                header('Access-Control-Allow-Headers: ' . implode(', ', $allowed_headers));
                header('Access-Control-Allow-Methods: ' . implode(', ', $allowed_methods));
            }
        }

        if ($allow_credentials) {
            header('Access-Control-Allow-Credentials: true');
        }
    }

    private function parseRequestParamsAndBody()
    {
        //get parameters from url
        $urlQueryParams = $this->input->get();
        $urlRouteParams = $this->uri->ruri_to_assoc();
        if (is_array($urlQueryParams) && count($urlQueryParams) > 0) {
            $this->requestParams = array_merge($this->requestParams, $urlQueryParams);
        }
        if (is_array($urlRouteParams) && count($urlRouteParams) > 0) {
            $this->requestParams = array_merge($this->requestParams, $urlRouteParams);
        }

        //get body
        $this->requestBody = $this->input->raw_input_stream;

        //handle content types
        $contentType = $this->getRequestContentType();
        switch ($contentType) {
            case 'application/x-www-form-urlencoded':
                $formBasedParams = $this->input->input_stream();
                if ($formBasedParams != null && is_array($formBasedParams)) {
                    $this->requestParams = array_merge($this->requestParams, $formBasedParams);
                }
                break;
            case 'application/json':
                $body = json_decode($this->requestBody, true); //to array for json string
                if ($body === null) {
                    $body = [];
                }
                $this->requestBody = $body;
                break;
            //Add more handling for different types
            default:
                break;
        }

        //xss filtering
        if ($this->config->item('global_xss_filtering') === true) {
            $this->requestParams = $this->security->xss_clean($this->requestParams);
            $this->requestBody = $this->security->xss_clean($this->requestBody);
        }
    }
}
