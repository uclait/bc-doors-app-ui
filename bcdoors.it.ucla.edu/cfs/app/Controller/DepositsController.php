<?php
class DepositsController extends AppController 
{
    var $name = 'Deposits';
    var $uses = array('Deposit');
    var $components = array('Date', 'Validate');
    
    public function index()
    {
        $this->autoRender = false;
    }
    public function xml_add()
    {
        set_time_limit(60);
        
        $this->autoRender = false;
        $this->autoLayout = false;
        
        $userId = $this->Session->read('id');
        $params = $this->Param->url("debug", 0) == 1 ? $this->params->query : $this->params->data;

        if (DEBUG_WRITE) {$this->Debug->write("Start App");};

        $prefixes = array("Deposit");

        $minimumFields = array(array(array("uid", "UID", "numeric"),
                                     array("amount", "Amount", "float"),
                                     array("order_id", "Order Id"),
                                     array("stored_value_account", "Stored Value Account")));
        
        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        if (AUDIT_ACTIVITY)
        {
            if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Deposit Add Start|" . http_build_query($form[$prefixes[0]], '', '|'));};
            $this->Audit->user("Deposit Add", $userId, null, http_build_query($form[$prefixes[0]], '', '|'));
            if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Deposit Add End");};
        }
        
        $uid = '';
        $html = "";
        $errors = array();
        $continue = false;
        $redirect = '';
        $results = array();

        $errors = $this->Validate->fields($form[$prefixes[0]], $minimumFields[0]);
        $continue = sizeof($errors) == 0;

        if ($continue)
        {
            $uid = $form[$prefixes[0]]['uid'];
            if (strlen($uid) <> 9)
            {
                $continue = false;
                $errors[] = "Invalid UID";
            }
            else
            {
                $conditions = array("ORDER_ID" => $form[$prefixes[0]]['order_id']);
                //$conditions = array('ORDER_ID = \'' . $form[$prefixes[0]]['order_id'] . '\'');

                if (DEBUG_WRITE) {$this->Debug->write("Start Deposit Add Find OrderId: {$form[$prefixes[0]]['order_id']}");};
                $data = $this->Deposit->find("first", array("conditions" => $conditions));
                if (DEBUG_WRITE) {$this->Debug->write("End Deposit Add Find OrderId: {$form[$prefixes[0]]['order_id']}");};

                if (isset($data[$prefixes[0]]))
                {
                    $row = $data[$prefixes[0]];
                    $uid = $row['ucla_id'];
                    $html = "<order id=\"{$row['order_id']}\">\n" .
                            "<inserted>" . $this->Date->fromOracleTimestamp($row['rec_create_timestamp']) . "</inserted>\n" .
                            "<process_date>" . $this->Date->fromOracleTimestamp($row['processed_timestamp']) . "</process_date>\n" .
                            "<status>" . $row['status'] . "</status>\n" .
                            "<error>Order Id already exists</error>\n" .
                            "</order>\n";
                }
                else
                {
                    if (!isset($form[$prefixes[0]]['status']))
                        $form[$prefixes[0]]['status'] = 'pending';

                    if (!isset($form[$prefixes[0]]['reference_number']))
                        $form[$prefixes[0]]['reference_number'] = '';

                    $form[$prefixes[0]]['insert_date'] = date("Y-m-d H:i:s");
                    $fields = array("ORDER_ID" => $form[$prefixes[0]]['order_id'],
                                    "UCLA_ID" => $form[$prefixes[0]]['uid'],
                                    "AMOUNT" => $form[$prefixes[0]]['amount'],
                                    "ACCOUNT_TYPE" => $form[$prefixes[0]]['stored_value_account'],
                                    "STATUS" => $form[$prefixes[0]]['status'],
                                    "REFERENCE_NUMBER" => empty($form[$prefixes[0]]['reference_number']) ? ' ' : $form[$prefixes[0]]['reference_number'],
                                    "REASON" => ' ');

                    if (DEBUG_WRITE) {$this->Debug->write("Start Deposit Add|" . http_build_query($fields, '', '|'));};
                    $this->Deposit->id = null;
                    $result = $this->Deposit->save(array($prefixes[0] => $fields));
                     if (DEBUG_WRITE) {$this->Debug->write("End Deposit Add|" . http_build_query($fields, '', '|'));};

                    $form[$prefixes[0]]['id'] = $this->Deposit->id;
                    $form[$prefixes[0]]['processed_date'] = '';

                    $html = "<order id=\"{$form[$prefixes[0]]['order_id']}\">\n" .
                            "<inserted>" . $form[$prefixes[0]]['insert_date'] . "</inserted>\n" .
                            "<process_date>" . $form[$prefixes[0]]['processed_date'] . "</process_date>\n" .
                            "<status>" . $form[$prefixes[0]]['status'] . "</status>\n" .
                            "<error>" . "" . "</error>\n" .
                            "</order>\n";
                }
                $form[$prefixes[0]]['id'] = $this->Deposit->id;
            }
        }
        if (AUDIT_ACTIVITY)
        {
            if (sizeof($errors) > 0)
            {
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Deposit Add Failed Start|{$userId}|errors=" . sizeof($errors));};
                $this->Audit->user("Deposit Add Failed", $userId, null, join(",", $errors));
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Deposit Add Failed End");};
            }
            else
            {
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Find BruinCard Holder Results Start");};
                $this->Audit->user("Deposit Add Success", $userId, null, $html);
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Find BruinCard Holder Results End");};
            }
        }

        if (DEBUG_WRITE) {$this->Debug->write("End App");};
        
        $html = "<deposits>" . "\n" .
                "<user uid=\"{$uid}\">" . $html . "</user>" . "\n" .
                "<error>" . implode("\n", $errors) . "</error>" . "\n" .
                "</deposits>";
        
        $this->response->type('xml');
        $this->response->body($html);
    }
    public function xml_status()
    {     
        $this->autoRender = false;
        $this->autoLayout = false;
        
        $userId = $this->Session->read('id');
        $params = $this->Param->url("debug", 0) == 1 ? $this->params->query : $this->params->data;

        if (DEBUG_WRITE) {$this->Debug->write("Start App");};

        $prefixes = array("Deposit");
        $minimumFields = array();
        
        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        if (AUDIT_ACTIVITY)
        {
            if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Deposit Status Start|" . http_build_query($form[$prefixes[0]], '', '|'));};
            $this->Audit->user("Deposit Status", $userId, null, http_build_query($form[$prefixes[0]], '', '|'));
            if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Deposit Status End");};
        }
        
        $html = "";
        $results = array();
        $errors = array();

        $continue = sizeof($errors) == 0;
        if ($continue)
        {
            if (isset($form[$prefixes[0]]['date_start']))
            {
                if (!isset($form[$prefixes[0]]['date_end']))
                    $form[$prefixes[0]]['date_end'] = date("Y-m-d");

                $conditions["rec_create_timestamp BETWEEN TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS') and TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')"] = array(date('Y-m-d 00:00:00', strtotime($form[$prefixes[0]]["date_start"])),
                                                                                                                                          date('Y-m-d 23:59:00', strtotime($form[$prefixes[0]]["date_end"])));
                $order = array('rec_create_timestamp', 'ucla_id', 'order_id');
            }
            else if (isset($form[$prefixes[0]]['order_id']))
            {
                $conditions = array('ORDER_ID' => $form[$prefixes[0]]['order_id']);
                //$conditions = array('ORDER_ID = \'' . $form[$prefixes[0]]['order_id'] . '\'');
                $order = array('UCLA_ID', 'ORDER_ID');
            }
            else
            {
                $continue = false;
            }
            if ($continue)
            {
                if (DEBUG_WRITE) {$this->Debug->write("Start Deposit Status|" . http_build_query($conditions, '', '|'));};
                $data = $this->Deposit->find("all", array("conditions" => $conditions, 'order' => $order));
                if (DEBUG_WRITE) {$this->Debug->write("End Deposit Status|" . http_build_query($conditions, '', '|'));};
                $rowCNT = sizeof($data);
                for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
                {
                    $row = $data[$loopCNT][$prefixes[0]];

                    $uid = $row['ucla_id'];
                    if (!isset($results[$uid]))
                        $results[$uid] = array('html'=> '');

                    //comment("date: " . $this->Date->fromOracleTimestamp($row['rec_create_timestamp']));

                    $results[$uid]['html'] .= "<order id=\"" . $this->Xml->encode($row['order_id']) . "\">\n" .
                                              "<inserted>" . $this->Date->fromOracleTimestamp($row['rec_create_timestamp']) . "</inserted>\n" .
                                              "<process_date>" . $this->Date->fromOracleTimestamp($row['processed_timestamp']) . "</process_date>\n" .
                                              "<status>" . $this->Xml->encode($row['status']) . "</status>\n" .
                                              "<reason>" . $this->Xml->encode($row['reason']) . "</reason>\n" .
                                              "</order>\n";
                }
                foreach($results as $key => $value)
                {
                    $html .= "<user uid=\"{$key}\">" . $results[$key]['html'] . "</user>" . "\n";
                }
            }
        }

        if (AUDIT_ACTIVITY)
        {
            if (sizeof($errors) > 0)
            {
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Deposit Status Failed Start|{$userId}|errors=" . sizeof($errors));};
                $this->Audit->user("Deposit Status Failed", $userId, null, join(",", $errors));
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Deposit Status Failed End|{$userId}");};
            }
            else
            {
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Deposit Status Success Start|{$userId}");};
                $this->Audit->user("Deposit Status Success", $userId);
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Deposit Status Success End|{$userId}");};
            }
        }

        if (DEBUG_WRITE) {$this->Debug->write("End App");};
        
        $html = "<deposits>" . "\n" .
                $html . "\n" .
                "<error>" . implode("\n", $errors) . "</error>" . "\n" .
                "</deposits>";
        
        $this->response->type('xml');
        $this->response->body($html);
    }
}
?>