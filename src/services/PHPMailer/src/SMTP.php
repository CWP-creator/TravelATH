<?php
namespace PHPMailer\PHPMailer;

/**
 * PHPMailer RFC821 SMTP email transport class.
 * Simplified version for demonstration purposes.
 */
class SMTP
{
    /**
     * The PHPMailer SMTP Version number.
     * @var string
     */
    const VERSION = '6.8.0';

    /**
     * SMTP line break constant.
     * @var string
     */
    const CRLF = "\r\n";

    /**
     * Debug level for no output.
     */
    const DEBUG_OFF = 0;

    /**
     * Debug level to show client -> server messages.
     */
    const DEBUG_CLIENT = 1;

    /**
     * Debug level to show client -> server and server -> client messages.
     */
    const DEBUG_SERVER = 2;

    /**
     * Debug level to show connection status, client -> server and server -> client messages.
     */
    const DEBUG_CONNECTION = 3;

    /**
     * Debug level to show all messages.
     */
    const DEBUG_LOWLEVEL = 4;

    /**
     * Debug output level.
     * @var int
     */
    public $do_debug = self::DEBUG_OFF;

    /**
     * Connect to an SMTP server.
     * @param string $host SMTP server IP or host name
     * @param int $port The port number to connect to
     * @param int $timeout How long to wait for the connection to open
     * @param array $options An array of options for stream_context_create()
     * @return bool
     */
    public function connect($host, $port = null, $timeout = 30, $options = [])
    {
        // Simplified version
        return false;
    }

    /**
     * Initiate a TLS (encrypted) session.
     * @return bool
     */
    public function startTLS()
    {
        return false;
    }

    /**
     * Perform SMTP authentication.
     * @param string $username The user name
     * @param string $password The password
     * @param string $authtype The auth type (CRAM-MD5, PLAIN, LOGIN, XOAUTH2)
     * @return bool True if successfully authenticated
     */
    public function authenticate($username, $password, $authtype = null)
    {
        return false;
    }

    /**
     * Send an SMTP DATA command.
     * @param string $msg_data Message data to send
     * @return bool
     */
    public function data($msg_data)
    {
        return false;
    }

    /**
     * Send an SMTP QUIT command.
     * @return bool
     */
    public function quit()
    {
        return false;
    }
}