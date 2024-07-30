<?php
class PhotosController extends AppController 
{
    var $name = 'Photos';
    var $uses = array('Photo');
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
        $prefixes = array("Photo");

        $minimumFields = array(array(array("uid", "UID"),
                                     array("url", "Photo Path")));
        
        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        if (AUDIT_ACTIVITY)
            $this->Audit->user("Photo Add", $userId, null, http_build_query($form[$prefixes[0]], '', '|'));
        
        $uid = '';
        $html = "";
        $results = array();

        $errors = $this->Validate->fields($form[$prefixes[0]], $minimumFields[0]);
        $continue = sizeof($errors) == 0;
        if ($continue)
        {
            $form[$prefixes[0]]['uid'] = explode("|", $form[$prefixes[0]]['uid']);
            $form[$prefixes[0]]['url'] = explode("|", $form[$prefixes[0]]['url']);

            $rowCNT = sizeof($form[$prefixes[0]]['uid']);
            $urlCNT = sizeof($form[$prefixes[0]]['url']);

            if ($rowCNT != $urlCNT)
            {
                $errors[] = "The length of Photo Paths do not match UIDs";
            }
            else
            {
                for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
                {
                    $form[$prefixes[0]]['insert_date'] = date("Y-m-d H:i:s");
                    $fields = array("UCLA_UNIVERSITY_ID" => $form[$prefixes[0]]['uid'][$loopCNT],
                                    "PHOTO_PATH" => $form[$prefixes[0]]['url'][$loopCNT],
                                    "INSERTED_DATE" => $form[$prefixes[0]]['insert_date']);

                    $this->Photo->id = null;
                    $result = $this->Photo->save(array($prefixes[0] => $fields));

                    $form[$prefixes[0]]['id'] = $this->Photo->id;
                    $form[$prefixes[0]]['processed_date'] = '';
                    if (!isset($form[$prefixes[0]]['status']))
                        $form[$prefixes[0]]['status'] = 'pending';

                    $uid = $form[$prefixes[0]]['uid'][$loopCNT];
                    if (!isset($results[$uid]))
                        $results[$uid] = array('html'=> '');

                    $results[$uid]['html'] .= "<photo id=\"{$form[$prefixes[0]]['id']}\">\n" .
                                              "<url>" . $this->Xml->encode($form[$prefixes[0]]['url'][$loopCNT]) . "</url>\n" .
                                              "<inserted>" . $form[$prefixes[0]]['insert_date'] . "</inserted>\n" .
                                              "<processed_date>" . $form[$prefixes[0]]['processed_date'] . "</processed_date>\n" .
                                              "<status>" . $form[$prefixes[0]]['status'] . "</status>\n" .
                                              "</photo>\n";
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
                $this->Audit->user("Photo Add Failed", $userId, null, join(",", $errors));
            else
                $this->Audit->user("Photo Add Success", $userId, null, $html);
        }
        
        $html = "<response>" . "\n" .
                $html . "\n" .
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
        $prefixes = array("Photo");
        $minimumFields = array();
        
        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        if (AUDIT_ACTIVITY)
            $this->Audit->user("Photo Status", $userId, null, http_build_query($form[$prefixes[0]], '', '|'));
        
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

                $conditions["inserted_date BETWEEN ? and ?"] = array(date('Y-m-d 00:00:00', strtotime($form[$prefixes[0]]["start_date"])),
                                                                   date('Y-m-d 23:59:00', strtotime($form[$prefixes[0]]["end_date"])));
                $order = array('inserted_date', 'ucla_university_id');
            }
            else if (isset($form[$prefixes[0]]['uid']))
            {
                $conditions = array('ucla_university_id' => $form[$prefixes[0]]['uid']);
                $order = array('ucla_university_id', 'inserted_date');
            }
            else
            {
                $continue = false;
            }
            if ($continue)
            {
                $data = $this->Photo->find("all", array("conditions" => $conditions, 'order' => $order));
                $rowCNT = sizeof($data);
                for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
                {
                    $row = $data[$loopCNT][$prefixes[0]];

                    $uid = $row['ucla_university_id'];
                    if (!isset($results[$uid]))
                        $results[$uid] = array('html'=> '');

                    $results[$uid]['html'] .= "<photo id=\"{$row['id']}\">\n" .
                                              "<url>" . $this->Xml->encode($row['photo_path']) . "</url>\n" .
                                              "<inserted>" . $row['inserted_date'] . "</inserted>\n" .
                                              "<processed_date>" . $row['updated_date'] . "</processed_date>\n" .
                                              "<status>" . $row['status'] . "</status>\n" .
                                              "</photo>\n";
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
                $this->Audit->user("Photo Status Failed", $userId, null, join(",", $errors));
            else
                $this->Audit->user("Photo Status Success", $userId);
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