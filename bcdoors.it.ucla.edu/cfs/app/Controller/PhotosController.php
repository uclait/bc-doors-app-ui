<?php
class PhotosController extends AppController 
{
    var $name = 'Photos';
    var $uses = array('Photo');
    var $components = array('Date', 'File', 'Validate');
    
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

        $prefixes = array("Photo");

        $minimumFields = array(array(array("uid", "UID"),
                                     array("url", "Photo Path")));
        
        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        if (AUDIT_ACTIVITY)
        {
            if (DEBUG_WRITE) {$this->Debug->write("AUDIT-Photo Add Start|" . http_build_query($form[$prefixes[0]], '', '|'));};
            $this->Audit->user("Photo Add", $userId, null, http_build_query($form[$prefixes[0]], '', '|'));
            if (DEBUG_WRITE) {$this->Debug->write("AUDIT-Photo Add End|" . http_build_query($form[$prefixes[0]], '', '|'));};
        }
        
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

            if (!isset($form[$prefixes[0]]['status']))
                $form[$prefixes[0]]['status'] = 'pending';

            if ($rowCNT != $urlCNT)
            {
                $errors[] = "The length of Photo Paths do not match UIDs";
            }
            else
            {
                for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
                {
                    $error = "";
                    $form[$prefixes[0]]['insert_date'] = '';
                    $form[$prefixes[0]]['processed_date'] = '';
                    
                    if (strlen($form[$prefixes[0]]['uid'][$loopCNT]) <> 9)
                    {
                        $continue = false;
                        $error = "Invalid UID";
                    }
                    if ($continue)
                    {
                        $conditions = array("ucla_id" => $form[$prefixes[0]]['uid'][$loopCNT]);
                        if (DEBUG_WRITE) {$this->Debug->write("Start Photo Add Find UCLA UID: {$form[$prefixes[0]]['uid'][$loopCNT]}");};
                        if ($this->Photo->find("count", array("conditions" => $conditions)) > 0)
                        {
                            if (DEBUG_WRITE) {$this->Debug->write("Start Photo Update Existing for Find UCLA UID: {$form[$prefixes[0]]['uid'][$loopCNT]}");};
                            $sql = "UPDATE %s SET STATUS = '%s' WHERE UCLA_ID = '%s' AND STATUS = 'pending'";
                            $sql = sprintf($sql, $this->Photo->useTable,
                                                 "skipped",
                                                 $form[$prefixes[0]]['uid'][$loopCNT]);

                            //==> Update to skipped so validation program doesn't process multiple
                            $this->Photo->query($sql);
                            if (DEBUG_WRITE) {$this->Debug->write("End Photo Update Existing for Find UCLA UID: {$form[$prefixes[0]]['uid'][$loopCNT]}");};
                        }
                        if (DEBUG_WRITE) {$this->Debug->write("End Photo Add Photo for UCLA UID: {$form[$prefixes[0]]['uid'][$loopCNT]}");};
                        $form[$prefixes[0]]['insert_date'] = date("Y-m-d H:i:s");
                        $fields = array("UCLA_ID" => $form[$prefixes[0]]['uid'][$loopCNT],
                                        "FILENAME" => $form[$prefixes[0]]['url'][$loopCNT],
                                        "STATUS" => $form[$prefixes[0]]['status'],
                                        "REASON" => ' ');

                        if (DEBUG_WRITE) {$this->Debug->write("Start Deposit Update Existing for Find UCLA UID: {$form[$prefixes[0]]['uid'][$loopCNT]}");};
                        $this->Photo->id = null;
                        $result = $this->Photo->save(array($prefixes[0] => $fields));
                        if (DEBUG_WRITE) {$this->Debug->write("End Deposit Update Existing for Find UCLA UID: {$form[$prefixes[0]]['uid'][$loopCNT]}");};
                        $form[$prefixes[0]]['id'] = $this->Photo->id;
                        $form[$prefixes[0]]['processed_date'] = '';
                        if (!isset($form[$prefixes[0]]['status']))
                            $form[$prefixes[0]]['status'] = 'pending';
                    }

                    $uid = $form[$prefixes[0]]['uid'][$loopCNT];
                    if (empty($results[$uid]))
                        $results[$uid] = array('html'=> '');

                    $results[$uid]['html'] .= "<photo>\n" .
                                              "<url>" . $this->Xml->encode($form[$prefixes[0]]['url'][$loopCNT]) . "</url>\n" .
                                              "<inserted>" . (empty($form[$prefixes[0]]['insert_date']) ? "" : $form[$prefixes[0]]['insert_date']) . "</inserted>\n" .
                                              "<process_date>" . (empty($form[$prefixes[0]]['processed_date']) ? "" : $form[$prefixes[0]]['processed_date']) . "</process_date>\n" .
                                              "<status>" . (empty($error) ? $form[$prefixes[0]]['status'] : "") . "</status>\n" .
                                              "<error>" . $error . "</error>\n" .
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
            {
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Photo Add Failed Start|{$userId}|errors=" . sizeof($errors));};
                $this->Audit->user("Photo Add Failed", $userId, null, join(",", $errors));
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Photo Add Failed End");};
            }
            else
            {
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Photo Add Success Start");};
                $this->Audit->user("Photo Add Success", $userId, null, $html);
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Photo Add Success End");};
            }
        }
        
        if (DEBUG_WRITE) {$this->Debug->write("End App");};
        $html = "<photos>" . "\n" .
                $html . "\n" .
                "<error>" . implode("\n", $errors) . "</error>" . "\n" .
                "</photos>";
        
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

        $prefixes = array("Photo");
        $minimumFields = array();
        
        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        if (AUDIT_ACTIVITY)
        {
            if (DEBUG_WRITE) {$this->Debug->write("AUDIT -  Photo Status Start|" . http_build_query($form[$prefixes[0]], '', '|'));};
            $this->Audit->user("Photo Status", $userId, null, http_build_query($form[$prefixes[0]], '', '|'));
            if (DEBUG_WRITE) {$this->Debug->write("AUDIT -  Photo Status End");};
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
                else if (strtotime($form[$prefixes[0]]['date_end']) <= strtotime($form[$prefixes[0]]['date_start']))
                    $form[$prefixes[0]]['date_end'] = date("Y-m-d");

                $conditions["rec_create_timestamp BETWEEN TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS') and TO_DATE(?, 'YYYY-MM-DD HH24:MI:SS')"] = array(date('Y-m-d 00:00:00', strtotime($form[$prefixes[0]]["date_start"])),
                                                                                                                                          date('Y-m-d 23:59:00', strtotime($form[$prefixes[0]]["date_end"])));
                $order = array('rec_create_timestamp', 'ucla_id');
            }
            else if (isset($form[$prefixes[0]]['uid']))
            {
                //$conditions = array('ucla_id' => explode("|", $form[$prefixes[0]]['uid']));
                $conditions = sprintf('SEQ IN (SELECT MAX(SEQ) FROM photo_transaction WHERE UCLA_ID IN (%s) GROUP BY UCLA_ID)', str_replace("|", ",", $form[$prefixes[0]]['uid']));
                $order = array('seq desc');
            }
            else
            {
                $continue = false;
            }
            if ($continue)
            {
                if (DEBUG_WRITE) {$this->Debug->write("Start Photo Find Pending Start");};
                $data = $this->Photo->find("all", array("conditions" => $conditions, 'order' => $order));
                if (DEBUG_WRITE) {$this->Debug->write("Start Photo Find Pending End");};
                $rowCNT = sizeof($data);

                for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
                {
                    $row = $data[$loopCNT][$prefixes[0]];

                    $uid = $row['ucla_id'];
                    if (!isset($results[$uid]))
                        $results[$uid] = array('html'=> '');

                    $results[$uid]['html'] .= "<photo>\n" .
                                              "<url>" . $this->Xml->encode($row['filename']) . "</url>\n" .
                                              "<inserted>" . $this->Date->fromOracleTimestamp($row['rec_create_timestamp']) . "</inserted>\n" .
                                              "<process_date>" . $this->Date->fromOracleTimestamp($row['processed_timestamp']) . "</process_date>\n" .
                                              "<status>" . $row['status'] . "</status>\n" .
                                              "<reason>" . $this->Xml->encode($row['reason']) . "</reason>\n" .
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
            {
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Photo Status Failed Start|{$userId}|errors=" . sizeof($errors));};
                $this->Audit->user("Photo Status Failed", $userId, null, join(",", $errors));
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Photo Status Failed End|{$userId}");};
            }
            else
            {
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Photo Status Success Start|{$userId}");};
                $this->Audit->user("Photo Status Success", $userId);
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT - Photo Status Success End|{$userId}");};
            }
        }
        
        if (DEBUG_WRITE) {$this->Debug->write("End App");};
        $html = "<photos>" . "\n" .
                $html . "\n" .
                "<error>" . implode("\n", $errors) . "</error>" . "\n" .
                "</photos>";
        
        $this->response->type('xml');
        $this->response->body($html);
    }
    private function _verifySignature($signature, $data)
    {
        $result = false;
        $cacheValues = Cache::read(CACHE_NAME_APPLICATION);

        $privateKey = $this->File->read($cacheValues['photo']['download']['default']['private']['key']);
        //$publicKey = $this->File->read($_SERVER['HTTP_PUBKEY']);

        $computedSignature = base64_encode(hash_hmac('sha1', http_build_query($data), $privateKey, TRUE));
        $result = $computedSignature == $signature;

        return $result;
    }
}
?>