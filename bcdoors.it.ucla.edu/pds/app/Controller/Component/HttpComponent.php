<?php
App::uses('HttpSocket', 'Network/Http');
class HttpComponent extends Object
{
   var $components = array('String');
   var $socket;
   var $status = null;
   var $contentType = null;
   var $content = null;
   var $timeout = 90;
   var $error = null;
   var $STATUS_CODE_OK = 200;
   var $STATUS_CODE_CREATED = 201;
   var $STATUS_CODE_MOVED_PERMANENTLY = 301;
   var $STATUS_CODE_BAD_REQUEST = 400;
   var $STATUS_CODE_UNAUTHORIZED = 401;
   var $STATUS_CODE_FORBIDDEN = 403;
   var $STATUS_CODE_NOT_FOUND = 404;
   var $STATUS_CODE_INTERNAL_SERVER_ERROR = 500;

    public function __construct()
    {
        $this->setup();
    }
    public function initialize(Controller $controller)
    {

    }
    function startup(Controller $controller)
    {
        $this->params = $controller->params;
    }
    public function shutdown(Controller $controller)
    {

    }
    public function beforeRender(Controller $controller)
    {

    }
    public function beforeRedirect()
    {

    }
   function setup()
   {
        $this->socket = new HttpSocket(array('timeout' => $this->timeout, 'ssl_verify_host' => false, 'ssl_verify_peer' => false));
   }
   function _checkForAuth($data)
   {
       if (isset($data["username"]) && isset($data["password"]))
       {
           $result = $this->socket->configAuth('Basic', $data["username"], $data["password"]);

           unset($data["username"]);
           unset($data["password"]);
       }

       return $data;
   }
   function get($url, $request = array())
   {
        $response = null;
        if (empty($url))
            $this->status = $this->STATUS_CODE_BAD_REQUEST;
        else
        {
            $request = $this->_checkForAuth($request);
            $response = $this->socket->get($url, $request);

            $this->content = isset($response->body) ? $response->body : null;
            $this->_setResponseCode();
        }
        return $response;
   }
   function post($url, $post, $request = array())
   {
        $response = null;
        if (empty($url))
            $this->status = $this->STATUS_CODE_BAD_REQUEST;
        else
        {
            $this->_checkForAuth($request);
            $response = $this->socket->post($url, $post, $request);
            $this->content = isset($response->body) ? $response->body : null;
            $this->_setResponseCode();
        }

        return $response;
   }
   function _setResponseCode()
   {
        if (isset($this->socket->lastError))
        {
            if (is_array($this->socket->lastError) && sizeof($this->socket->lastError)> 0)
            {
                $this->error = $this->socket->lastError["num"];
                if (empty($this->error))
                        $this->status = $this->STATUS_CODE_INTERNAL_SERVER_ERROR;
                else
                {
                    if ($this->String->contains(strtolower($this->error), strtolower("Name or service not known")))
                         $this->status = $this->STATUS_CODE_BAD_REQUEST;
                }
            }
        }
        if (isset($this->socket->response))
        {
            if (isset($this->socket->response["status"]))
            {
                    if (isset($this->socket->response["status"]["code"]))
                    {
                            $this->status = $this->socket->response["status"]["code"];
                            if ($this->status == $this->STATUS_CODE_MOVED_PERMANENTLY)
                                    $this->error = $this->socket->response["status"]["reason-phrase"];
                    }
            }
            if (isset($this->socket->response["header"]))
            {
                    if (isset($this->socket->response["header"]["Content-Type"]))
                    {
                            $this->contentType = $this->socket->response["header"]["Content-Type"];
                    }
            }
        }
   }
}
?>
