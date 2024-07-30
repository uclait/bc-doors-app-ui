<?php
class DepositsController extends AppController 
{
    var $name = 'Deposits';
    var $uses = array('Deposit');
    var $components = array('Validate');
    
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
        $prefixes = array("Deposit");

        $minimumFields = array(array(array("uid", "UID", "numeric"),
                                     array("amount", "Amount", "float"),
                                     array("order_id", "Order Id"),
                                     array("stored_value_account", "Stored Value Account")));
        
        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        if (AUDIT_ACTIVITY)
            $this->Audit->user("Deposit Add", $userId, null, http_build_query($form[$prefixes[0]], '', '|'));
        
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
            $conditions = array("order_id" => $form[$prefixes[0]]['order_id']);
            $data = $this->Deposit->find("first", array("conditions" => $conditions));

            if (isset($data[$prefixes[0]]))
            {
                $errors[] = "Order id already exists";
            }
            else
            {
                $form[$prefixes[0]]['insert_date'] = date("Y-m-d H:i:s");
                $fields = array("ORDER_ID" => $form[$prefixes[0]]['order_id'],
                                "UCLA_UNIVERSITY_ID" => $form[$prefixes[0]]['uid'],
                                "AMOUNT" => $form[$prefixes[0]]['amount'],
                                "ACCOUNT_TYPE" => $form[$prefixes[0]]['stored_value_account'],
                                "REFERENCE_NUMBER" => null,
                                "INSERTED_DATE" => $form[$prefixes[0]]['insert_date']);

                $this->Deposit->id = null;
                $result = $this->Deposit->save(array($prefixes[0] => $fields));

                //comment("id: " . $this->Deposit->getInsertID());

                $form[$prefixes[0]]['id'] = $this->Deposit->id;
                $form[$prefixes[0]]['processed_date'] = '';
                if (!isset($form[$prefixes[0]]['status']))
                    $form[$prefixes[0]]['status'] = 'pending';

                $html = "<order id=\"{$form[$prefixes[0]]['id']}\">\n" .
                        "<order_id>" . $form[$prefixes[0]]['order_id'] . "</order_id>\n" .
                        "<inserted>" . $form[$prefixes[0]]['insert_date'] . "</inserted>\n" .
                        "<processed_date>" . $form[$prefixes[0]]['processed_date'] . "</processed_date>\n" .
                        "<status>" . $form[$prefixes[0]]['status'] . "</status>\n" .
                        "</order>\n";
            }


            $form[$prefixes[0]]['id'] = $this->Deposit->id;
        }
        if (AUDIT_ACTIVITY)
        {
            if (sizeof($errors) > 0)
                $this->Audit->user("Deposit Add Failed", $userId, null, join(",", $errors));
            else
                $this->Audit->user("Deposit Add Success", $userId, null, $html);
        }
        
        $html = "<response>" . "\n" .
                "<user uid=\"{$uid}\">" . $html . "</user>" . "\n" .
                "<errors>" . $this->Xml->enclose("error", $errors) . "</errors>" . "\n" .
                "</response>";
        
        $this->response->type('xml');
        $this->response->body($html);
    }
    public function xml_status()
    {     
        $this->autoRender = false;
        $this->autoLayout = false;
        
        $userId = $this->Session->read('id');
        $params = $this->Param->url("debug", 0) == 1 ? $this->params->query : $this->params->data;
        $prefixes = array("Deposit");
        $minimumFields = array();
        
        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        if (AUDIT_ACTIVITY)
            $this->Audit->user("Deposit Status", $userId, null, http_build_query($form[$prefixes[0]], '', '|'));
        
        $html = "";
        $results = array();
        $errors = array();

        $continue = sizeof($errors) == 0;
        if ($continue)
        {
            if (isset($form[$prefixes[0]]['start_date']))
            {
                if (!isset($form[$prefixes[0]]['end_date']))
                    $form[$prefixes[0]]['end_date'] = date("Y-m-d");

                $conditions["inserted_date BETWEEN TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS') and TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')"] = array(date('Y-m-d 00:00:00', strtotime($form[$prefixes[0]]["start_date"])),
                                                                   date('Y-m-d 23:59:00', strtotime($form[$prefixes[0]]["end_date"])));
                $order = array('inserted_date', 'ucla_university_id', 'order_id');
            }
            else if (isset($form[$prefixes[0]]['order_id']))
            {
                $conditions = array('order_id' => $form[$prefixes[0]]['order_id']);
                $order = array('ucla_university_id', 'order_id');
            }
            else
            {
                $continue = false;
            }
            if ($continue)
            {
                $data = $this->Deposit->find("all", array("conditions" => $conditions, 'order' => $order));
                $rowCNT = sizeof($data);
                for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
                {
                    $row = $data[$loopCNT][$prefixes[0]];

                    $uid = $row['ucla_university_id'];
                    if (!isset($results[$uid]))
                        $results[$uid] = array('html'=> '');

                    $results[$uid]['html'] .= "<order id=\"{$row['id']}\">\n" .
                                              "<order_id>" . $this->Xml->encode($row['order_id']) . "</order_id>\n" .
                                              "<inserted>" . $row['inserted_date'] . "</inserted>\n" .
                                              "<processed_date>" . $row['updated_date'] . "</processed_date>\n" .
                                              "<status>" . $this->Xml->encode($row['status']) . "</status>\n" .
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
                $this->Audit->user("Deposit Status Failed", $userId, null, join(",", $errors));
            else
                $this->Audit->user("Deposit Status Success", $userId);
        }
        
        $html = "<response>" . "\n" .
                $html . "\n" .
                "<errors>" . $this->Xml->enclose("error", $errors) . "</errors>" . "\n" .
                "</response>";
        
        $this->response->type('xml');
        $this->response->body($html);
    }
}
?>