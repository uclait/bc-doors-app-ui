<?php
class CardHolderController extends AppController 
{
    var $name = 'Home';
    var $uses = array('PendingAccessPlan');
    var $components = array('Breadcrumbs', 'GrouperApi', 'Validate');
    
    public function index()
    {
        set_time_limit(SEARCH_TIMEOUT);
        $debug = $this->Param->url("debug", 0) == 1;
        $params = $this->params->query;

        if (DEBUG_WRITE) {$this->Debug->write("Start App");};

        $uid = date('YmdHis');
        $additionalFiles = array('css' => array(), 
                                 'js' => array('/js/jquery.blockUI-2.65.min.js',
                                               '/js/search.min.js?uid=' . $uid,
                                               '/js/jquery.dataTables.v1.10.4.min.js',
                                               '/js/dataTables.bootstrap.js',
                                               '/js/card-holder.min.js?uid=' . $uid,
                                               '/js/jquery.functions.min.js',
                                               '/js/jquery.blockMessage.min.js'));
        
        $breadCrumbs = $this->Breadcrumbs->generate();
        $prefixes = array("CardHolder");
        $minimumFields = array(array(array("uid", "ID", "numeric")));
        
        $form[$prefixes[0]] = $this->Param->extract($params, "");
        
        $uid = '';
        $name = "";
        $data = array();
        $cardholder = array();
        $available = array();
        $assigned = array();
        $pending = array();

        $errors = $this->Validate->fields($form[$prefixes[0]], $minimumFields[0]);
        $continue = sizeof($errors) == 0;
        if ($continue)
        {
            //==> Use web service
            $appValues = Cache::read(CACHE_NAME_APPLICATION);      
            $stemName = $appValues['stem']['path']['ag'];
            
            $uid = $form[$prefixes[0]]['uid'];
            $values = Cache::read(CACHE_NAME_APPLICATION);
            $api = $values['api']['card_holder_info'];

            if (DEBUG_WRITE) {$this->Debug->write("Call Search API Start");};
            $response = $this->Http->get($api['url'], array($api['param'] => $uid));
            if (DEBUG_WRITE) {$this->Debug->write("Call Search API End");};
            if ($this->Http->status == $this->Http->STATUS_CODE_OK)
            {
                $response = $this->Http->content;
                $xml = $this->Xml->load($response);
                if ($xml)
                {
                    $cardholder = array('uid' => (string)$xml->Customer->CustomerNumber,
                                        'first_name' => (string)$xml->Customer->FirstName,
                                        'last_name' => (string)$xml->Customer->LastName);
                    
                    $name = "{$cardholder['first_name']} {$cardholder['last_name']}";
                    $breadCrumbs['card'][$name] = '';
                }
                else
                {
                    $errors[] = "Customer not Found";
                    $continue = false;
                }
            }
            //=================================================================
            //==> Get any pending access plans
            //=================================================================
            if ($continue)
            {
                $conditions = array("UCLA_UID" => $uid);
                if (DEBUG_WRITE) {$this->Debug->write("Start Get Pending");};
                $data = $this->PendingAccessPlan->find("all", array("conditions" => $conditions, 'order' => "ID"));
                $rowCNT = sizeof($data);
                for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
                {
                    $pending[] = array("path" => $data[$loopCNT]['PendingAccessPlan']['path'], "inserted" => $data[$loopCNT]['PendingAccessPlan']['inserted']);
                }
                if (DEBUG_WRITE) {$this->Debug->write("End Get Pending");};
                
                //-----------------------------------------------------------------
                //=================================================================
                //==> If pending get status
                //=================================================================
                $lastLogged = array();
                if (sizeof($pending) > 0)
                {
                    $api = $values['api']['card_holder_status'];
                    $params = array('header' => array('Content-Type' => 'text/plain'),
                                    'body' => $uid . (sizeof($pending) > 0 ? "/" . implode(",", $this->Array->extractValues($pending, "path")) : ''));

                    if (DEBUG_WRITE) {$this->Debug->write("Start Get Card Holder Status");};
                    $response = $this->Http->post($api['url'], array(), $params);
                    if (DEBUG_WRITE) {$this->Debug->write("End Get Card Holder Status");};
                    if ($this->Http->status == $this->Http->STATUS_CODE_OK)
                    {
                        $response = $this->Http->content;
                        $xml = $this->Xml->load($response);
                        if ($xml)
                        {
                            $rowCNT = sizeof($xml->AccessPlans);
                            for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
                            {
                                $row = (array)$xml->AccessPlans[$loopCNT];
                                if (!isset($row['UpdateDate']))
                                    $row['UpdateDate'] = $row['EntryDate'];

                                $lastLogged[$row['GroupName']] = array(
                                                                       'sequence_number' => $row['SeqNum'],
                                                                       'status' => strtolower($row['Status'] == 'completed'), 
                                                                       'inserted' => date('Y-m-d H:i:s', strtotime($row['UpdateDate'])));
                            }
                        }
                    }   
                }
                if (sizeof($lastLogged) > 0)
                {
                    $rowCNT = sizeof($pending);
                    for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
                    {
                        $path = $pending[$loopCNT]['path'];
                        if (isset($lastLogged[$path]))
                        {
                            //==> Make sure completed and that the logged entry has a greater entry date
                            if ($lastLogged[$path]['status'] && 
                                (strtotime($lastLogged[$path]['inserted']) >=  strtotime($pending[$loopCNT]['inserted'])))
                            {
                                $fields = array("UCLA_UID" => $uid, "PATH" => $path);
                                $result = $this->PendingAccessPlan->deleteAll($fields);
                                unset($pending[$loopCNT]);
                            }
                        }
                    }
                    $pending = array_values($pending);
                }
            }
            //-----------------------------------------------------------------
            //=================================================================
            //==> Get groups Card Holder is assigned. But only show ones that
            //==>   DC has access to
            //=================================================================
            if ($continue)
            {
                if (DEBUG_WRITE) {$this->Debug->write("Start Get Card Holder Access Plans");};
                $data = $this->GrouperApi->getMemberships($uid);
                if (DEBUG_WRITE) {$this->Debug->write("End Get Card Holder Access Plans");};
                $merchants = $this->Session->read('merchants');
                $merchantCNT = sizeof($merchants);
                $assignedCNT = sizeof($data);
                for ($loopCNT = 0; $loopCNT < $assignedCNT; $loopCNT++)
                {
                    for ($loopCNT2 = 0; $loopCNT2 < $merchantCNT; $loopCNT2++)
                    {
                        $accessGroup = $stemName . ":" . $merchants[$loopCNT2];
                        if ($this->String->beginsWith($data[$loopCNT]['name'], $accessGroup))
                        {
                            $assigned[] = $data[$loopCNT];
                            break;
                        }
                    }
                }   
                //-----------------------------------------------------------------
                //=================================================================
                //==> Get groups Card Holder can be assigned. But only show ones that
                //==>   DC has access to
                //=================================================================
                if (DEBUG_WRITE) {$this->Debug->write("Start Get Card Holder Allowed Access Plans");};
                $data = $this->GrouperApi->loadGroups();
                if (DEBUG_WRITE) {$this->Debug->write("End Get Card Holder Allowed Access Plans");};
                $merchantCNT = sizeof($merchants);
                for ($loopCNT = 0; $loopCNT < $merchantCNT; $loopCNT++)
                {
                    $accessGroup = $stemName . ":" . $merchants[$loopCNT];
                    $results = Set::extract("/GrouperStem[name={$accessGroup}]", $data);
                    if (sizeof($results) > 0)
                    {
                        $results  = $results[0]['GrouperStem']['groups'];
                        $availableCNT = sizeof($results);
                        for ($loopCNT2 = 0; $loopCNT2 < $availableCNT; $loopCNT2++)
                        {
                            if (!self::_alreadyAssigned($results[$loopCNT2], $assigned))
                            {
                                $available[] = $results[$loopCNT2];
                            }
                        }
                    }
                }
            }
            //-----------------------------------------------------------------
        }
        if (DEBUG_WRITE) {$this->Debug->write("End App");};

        $this->set('additional_files', $additionalFiles);
        $this->set('errors', $errors);
        $this->set('bread_crumbs', $breadCrumbs);
        $this->set('uid', $uid);
        $this->set('data', $cardholder);
        $this->set('available', $available);
        $this->set('assigned', $assigned);
        $this->set('pending', $this->Array->extractValues($pending, "path"));
        $this->set('name', $name);

        $this->set('door_plans', $this->Session->read('access_plans'));
    }
    function _alreadyAssigned($data, $assigned)
    {
        $result = false;
        $assignedCNT = sizeof($assigned);

        $name = $data['GrouperGroup']['name'];
        for ($loopCNT2 = 0; $loopCNT2 < $assignedCNT; $loopCNT2++)
        {
            $name = $assigned[$loopCNT2]['name'];
            $results = Set::extract("/GrouperGroup[name={$name}]", $data);
            if (sizeof($results) > 0)
            {
                $result = true;
                break;
            }
        }

        return $result;
    }
   public function xml_info()
    {
        $this->autoRender = false;
        $cacheValues = Cache::read(CACHE_NAME_APPLICATION);
        $params = $this->Param->url("debug", 0) == 1 ? $this->params->query : $this->params->data;

        if (DEBUG_WRITE) {$this->Debug->write("Start App");};

        $prefixes = array("CardHolder");
        $minimumFields = array(array(array("uid", "UID")));
        $html = '';
        $uid = "";

        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");

        $errors = $this->Validate->fields($form[$prefixes[0]], $minimumFields[0]);
        $continue = sizeof($errors) == 0;
        if ($continue)
        {
            $uid = $form[$prefixes[0]]['uid'];
            $api = $cacheValues['api']['card_holder_info'];
            if (DEBUG_WRITE) {$this->Debug->write("Start Get Card Holder Info - {$uid}");};
            $response = $this->Http->get($api['url'], array($api['param'] => $uid));
            if (DEBUG_WRITE) {$this->Debug->write("End Get Card Holder Info - {$uid}");};
            if ($this->Http->status == $this->Http->STATUS_CODE_OK)
            {
                $response = $this->Http->content;
                $xml = $this->Xml->load($response);
                if ($xml)
                {
                    $cardholder = json_decode(json_encode($xml));
                    $html = "<id>" . $cardholder->Customer->CustomerID . "</id>" . "\n" .
                            "<birth_date>" . (empty($cardholder->Customer->BirthDate) ? '' : $this->Xml->encode($cardholder->Customer->BirthDate)) . "</birth_date>" . "\n" .
                            "<last_name>" . $this->Xml->encode($cardholder->Customer->LastName) . "</last_name>" . "\n" .
                            "<first_name>" . $this->Xml->encode($cardholder->Customer->FirstName) . "</first_name>" . "\n" .
                            "<photo_path>" . (empty($cardholder->PhotoPath) ? '' : $this->Xml->encode($cardholder->PhotoPath)) . "</photo_path>" . "\n";

                    $storedValue = array();
                    if (isset($cardholder->StoreValueAccType))
                    {
                        for ($loopCNT = 0; $loopCNT < sizeof($cardholder->StoreValueAccType); $loopCNT++)
                        {
                            $storedValue[] = $cardholder->StoreValueAccType[$loopCNT];
                        }
                    }
                    $html .= "<stored_value_accounts>\n" . 
                             $this->Xml->enclose("type", $storedValue) . "\n" .
                             "</stored_value_accounts>";
                }
                else
                {
                    $continue = false;
                }
                $html .= "<error>" . ($continue ? "" : "Card Holder not Found") . "</error>\n";
            }
        }
        if (DEBUG_WRITE) {$this->Debug->write("End App");};

        $html = "<card_holders>" . "\n" .
                "<user uid=\"{$uid}\">" . $html . "</user>" . "\n" .
                "<error>" . implode("\n", $errors) . "</error>" . "\n" .
                "</card_holders>";

        $this->response->type('xml');
        $this->response->body($html);
    }
}
?>