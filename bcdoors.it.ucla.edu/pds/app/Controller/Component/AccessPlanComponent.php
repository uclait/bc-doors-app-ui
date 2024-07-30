<?php
class AccessPlanComponent extends Object
{
    public $url = null;
    public $contentType = null;
    public $method = null;
    public $controller = null;
    
    public function __construct()
    {
        $values = Cache::read(CACHE_NAME_APPLICATION);

        $this->url = $values['api']['card_holder_status']['url'];
        $this->method = $values['api']['card_holder_status']['method'];
        $this->contentType = $values['api']['card_holder_status']['content']['type'];
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
    public function retrieve($uid, $paths)
    {
        $results = array();
        $sequenceNumber = 0;
        if (sizeof($paths) > 0)
        {
            $params = array('header' => array('Content-Type' => $this->contentType),
                            'body' => $uid . (sizeof($paths) > 0 ? "/" . implode(",", $paths) : ''));

            $response = $this->controller->Http->post($this->url, array(), $params);
            if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK)
            {
                $response = $this->controller->Http->content;
                $xml = $this->controller->Xml->load($response);
                if ($xml)
                {
                    $rowCNT = sizeof($xml->AccessPlans);
                    for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
                    {
                        $row = (array)$xml->AccessPlans[$loopCNT];
                        //==> Check if last SeqNum is greater in case they are not in order
                        if ($row['SeqNum'] > $sequenceNumber)
                            $results[$row['GroupName']][$row['Uid']] = array('status' => strtolower($row['Status'] == 'completed'), 
                                                                             'inserted' => date('Y-m-d H:i:s', strtotime($row['EntryDate'])));
                        
                        $sequenceNumber = $row['SeqNum'];
                    }
                }
            }   
        }
        
        return $results;
    }
}
?>
