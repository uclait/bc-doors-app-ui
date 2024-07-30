<?php
class CardHolderPPIDComponent extends Object
{
    public $url = null;
    public $contentType = null;
    public $method = null;
    public $controller = null;
    
    public function __construct()
    {
        $values = Cache::read(CACHE_NAME_APPLICATION);

        $this->url = $values['api']['card_holder_info_ppid']['url'];
        $this->method = $values['api']['card_holder_info_ppid']['method'];
        $this->contentType = $values['api']['card_holder_info_ppid']['content']['type'];
    }
    public function initialize(Controller $controller)
    {
        $this->controller = $controller;
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
    public function retrieve($paths)
    {
        $results = array();
        if (sizeof($paths) > 0)
        {
            $params = array('header' => array('Content-Type' => $this->contentType),
                            'body' => implode(",", $paths));

            $response = $this->controller->Http->post($this->url, array(), $params);
            if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK)
            {
                $response = $this->controller->Http->content;
                $xml = $this->controller->Xml->load($response);

                if ($xml)
                {
                    $results = $this->controller->Xml->toArray($xml);
                }
            }   
        }
        
        return isset($results['CustomerInfo']) ? $results['CustomerInfo'] : $results;
    }
}
?>
