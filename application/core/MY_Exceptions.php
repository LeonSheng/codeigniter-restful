<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions
{
    public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
    {
        ob_clean();
        $message = is_array($message) ? implode(',', $message) : $message;
        if (!IS_DEV_MODE && key_exists($status_code, STATUS_TEXT)) {
            $message = STATUS_TEXT[$status_code];
        }
        header('Cache-Control: no-cache');
        header('Content-type: application/json');
        set_status_header($status_code);
        return json_encode([
            'status' => $status_code,
            'error' => $message,
        ]);
    }

    public function show_404($page = '', $log_error = TRUE)
    {
        $message = 'Not Found';
        if ($log_error) {
            log_message('error', $message.': '.$page);
        }
        echo $this->show_error('', $message, 'error_404', 404);
    }

    /**
     * A wrapper of show_error function
     *
     * @param $message
     * @param int $status_code
     * @param bool $log_error
     * @param array $append_fields
     */
    public function show_json_error($message, $status_code = 500, $log_error = TRUE, $append_fields = array())
    {
        if (is_array($message)) {
            if (is_array($append_fields) && key_exists('errcode', $message)) {
                $append_fields['errcode'] = $message['errcode'];
            }
            $message = key_exists('error', $message) ? (string)$message['error'] : implode(',', $message);
        }

        if ($log_error) {
            log_message('error', $message);
        }
        $error = $this->show_error('', $message, '', $status_code);
        if (is_array($append_fields) && count($append_fields) > 0) {
            $error = array_merge(json_decode($error, true), $append_fields);
            echo json_encode($error);
        } else {
            echo $error;
        }
    }

    /**
     * @param $exception Exception
     */
    public function show_exception($exception)
    {
        echo $this->show_error('', $exception->getMessage());
    }

    public function show_php_error($severity, $message, $filepath, $line)
    {
        echo $this->show_error('', $message);
    }
}
