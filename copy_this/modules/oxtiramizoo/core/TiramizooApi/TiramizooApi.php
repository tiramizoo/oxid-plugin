<?php
/**
 * Pure class used for sending requests to the API via cURL.
 *
 * @package: oxTiramizoo
 */
class TiramizooApi 
{
    /**
     * API url
     *             
     * @var string
     */
    protected $api_url = null;

    /**
     * API token
     *             
     * @var string
     */    
    protected $api_token = null;

    /**
     * Connection Time Out
     *             
     * @var integer
     */    
    protected static $_iConnectionTimeOut = 300;

    /**
     * Time Out
     *             
     * @var integer
     */    
    protected static $_iTimeOut = 60;

    /**
     * Time out curl error
     * 
     */
    const CURLE_OPERATION_TIMEDOUT = 28;

    /**
     * Construct the object with api key and url
     * @param string $api_url   API url
     * @param string $api_token API token to authenticate
     */
    protected function __construct($api_url, $api_token) 
    {
        $this->api_url = $api_url;
        $this->api_token = $api_token;
    }
    
    /**
     * Build http connection to the API via cURL
     * 
     * @param  string  $path API path
     * @param  array   $data  Data to send
     * @param  boolean $result result
     * @return boolean Return true if success otherwise false
     */
    public function request($path, $data = array(), &$result = false) 
    {
        $c = curl_init();

        //@todo: set 1 before launch
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($c, CURLOPT_URL, $this->api_url.'/'.$path.'?api_token='. $this->api_token);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, preg_replace_callback('/(\\\u[0-9a-f]{4})/', array($this, "json_unescape"), json_encode($data)));


        curl_setopt($c, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Accept: application/json"
        ));

        curl_setopt($c, CURLOPT_TIMEOUT, self::$_iTimeOut);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, self::$_iConnectionTimeOut);

        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($c);

        $errno = curl_errno($c);
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);

        $result = array('http_status' => $status, 'response' => json_decode($response), 'errno' => $errno);

        curl_close($c);
    }   
    
    /**
     * Build http connection to the API via cURL
     * 
     * @param  string  $path API path
     * @param  array   $data  Data to send
     * @param  boolean $result result
     * @return boolean Return true if success otherwise false
     */
    public function requestGet($path, $data = array(), &$result = false) 
    {
        $c = curl_init();

        //@todo: set 1 before launch
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($c, CURLOPT_URL, $this->api_url.'/'.$path.'?api_token='. $this->api_token . '&' . http_build_query($data));

        curl_setopt($c, CURLOPT_TIMEOUT, self::$_iTimeOut);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, self::$_iConnectionTimeOut);

        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($c);

        $errno = curl_errno($c);
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);

        $result = array('http_status' => $status, 'response' => json_decode($response), 'errno' => $errno);

        curl_close($c);
    }   

    /**
     * Unescape json items
     * 
     * @param  string $m Element's value
     * @return string unescaped value
     */
    protected function json_unescape($m) 
    {
        return json_decode('"'.$m[1].'"');
    }

    /**
     * Modify connection Timeout for unit tests
     * 
     * @param  integer $iTimeOut The maximum number of seconds to allow cURL functions to execute.  
     * @param  integer $iConnectionTimeOut The number of seconds to wait while trying to connect
     * */
    public static function setConnectionTimeout($iTimeOut, $iConnectionTimeOut)
    {
        if ( !defined( 'OXID_PHP_UNIT' ) ) {
            return;
        }       

        self::$_iTimeOut = $iTimeOut;
        self::$_iConnectionTimeOut = $iConnectionTimeOut;
    }
}