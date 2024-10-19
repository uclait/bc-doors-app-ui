<?php
class GeneralController extends AppController
{
    var $name = 'General';
    var $uses = array('AuditTest', 'AuditUser', 'CardHolder', 'Definition', 'PendingAccessPlan', 'Users');
    var $components = array('Cookie', 'File', 'Http', 'GrouperApi');

    public function headers()
    {
        $this->autoRender = false;

        echo "<pre>";
        print_r($_SERVER);
        echo "</pre>";
    }
    public function view_session()
    {
        $this->autoRender = false;

        echo "<pre>";

        $id = $this->Session->read('uid');
        print_r($_SESSION);
        echo "</pre>";
    }
    public function view_cookie()
    {
        $this->autoRender = false;

    // Unset all of the session variables.
    $_SESSION = array();
 
    // Destroy cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
    }
 
    session_destroy();
    
        echo "<pre>";
        print_r($_COOKIE);

if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000);
        setcookie($name, '', time()-1000, '/');
    }
}
        print_r($_COOKIE);
        echo "</pre>";
    }
    public function grouper_test()
    {
        $this->autoRender = false;

        $appValues = Cache::read(CACHE_NAME_APPLICATION);
        print_r($appValues);

        $url = "http://iam-as-d02.dev.it.ucla.edu/grouper-ws/servicesRest/v2_1_5/groups?wsLiteObjectType=WsRestFindGroupsLiteRequest&stemName=ucla%3Abruincard%3ADC&queryFilterType=FIND_BY_STEM_NAME";
        $data = array('username' => "grouper-wsuser-bruincard",
                      'password' => "b*r13E#sk3");

        //$response = $this->GrouperApi->process($url, $data);
        //print_r($response);
        //exit(1);
        

        $url = "https://grouperws.it.ucla.edu/grouper-ws/servicesRest/v2_1_5/groups?wsLiteObjectType=WsRestFindGroupsLiteRequest&stemName=ucla%3Abruincard%3Aetc%3Aacl&queryFilterType=FIND_BY_STEM_NAME";

        $data = array('username' => "grouper-wsuser-bar",
                      'password' => "jd!3k4z#cg");

        $response = $this->GrouperApi->process('GET', $url, $data);

        print_r($response);
        exit(1);

        //$response = $this->Http->get($url, $data);
        //print_r($this->Http);

        //exit(1);
        $opts = array(
                            CURLOPT_CONNECTTIMEOUT => 30,
                            CURLOPT_TIMEOUT        => 60,
                            CURLOPT_FRESH_CONNECT  => 1,
                            CURLOPT_PORT           => 443,
                            CURLOPT_USERAGENT      => 'curl-php',
                            CURLOPT_FOLLOWLOCATION => false,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_CUSTOMREQUEST  => 'GET',
                            CURLOPT_HTTPHEADER     => array('Content-Type: text/x-json; charset=UTF-8;','Accept: application/json'));

        //$this->setRequest(array('gateway_id' => $this->username, 'password' => $this->password));

        $opts[CURLOPT_SSL_VERIFYHOST] = false;
        $opts[CURLOPT_SSL_VERIFYPEER] = false;

        $opts[CURLOPT_USERPWD] = "{$data['username']}:{$data['password']}";
        //$opts[CURLOPT_SSL_CIPHER_LIST] = 'SSLv3';
        //$opts[CURLOPT_SSL_CIPHER_LIST] = 'TLSv1';

        $opts[CURLOPT_SSLVERSION] = 0;

        $opts[CURLOPT_URL] = $url;

        $ch = curl_init();
        curl_setopt_array($ch, $opts);

        $response = curl_exec($ch);
        $headers = curl_getinfo($ch);

        $errorNo = curl_errno($ch);
        $error = curl_error($ch);

pr($error);
pr(json_decode($response));

        echo "<pre>";
//        print_r($_SERVER);
        echo "</pre>";

        $data = $this->Definition->find('all');
        pr($data);
        exit(1);
    }
    public function parseOptim()
    {
        set_time_limit(6000);
        ini_set('memory_limit', '1000M');
        $this->autoRender = false;

        $file = WWW_ROOT . "files\\demodataDOBfix_tab.csv_001.txt";
        $output = str_replace('.txt', '-parsed.txt', $file);

        $contents = str_replace("\n", "", $this->File->read($file));
        $contents = explode("\r", $contents);
        echo "<pre>";
        $totalCNT = sizeof($contents);

        $hasCommas = 0;
        $realRecords  = 0;
        $blank = 0;

        $this->File->write($output, "index\tlast_name\tfirst_name\tmiddle_name\r\n", 'w');
        for ($loopCNT = 0; $loopCNT < $totalCNT; $loopCNT++)
        {
            $fields = explode('|', $contents[$loopCNT]);
           //pr($fields[3]);
           //echo $loopCNT . ". " . $contents[$loopCNT] . "\n";
            if (empty($contents[$loopCNT]))
                continue;

            $fields[3] = trim($fields[3]);
            if (!isset($fields[3]))
            {
                $blank++;
                echo "row: " . $loopCNT . "<br>";
            }
            else
            {
                $realRecords++;
                if ($this->String->endsWith($fields[3], ","))
                {
                    $fields[3] = substr($fields[3], 0, -1);
                    echo $fields[3]; exit(1);
                    
                }

                if ($this->String->contains($fields[3], ','))
                {
                    $hasCommas++;
                    $names = explode(',', $fields[3]);

                    $firstNames = explode(' ', trim($names[0]));

                    $text = "{$names[0]}\t" . trim($firstNames[0]);
                    if (sizeof($firstNames) > 1)
                        $text .= "\t" . trim($firstNames[1]);
                }
                else
                {
                    //echo $loopCNT . ". " . $contents[$loopCNT] . "\n";
                    $text = "{$fields[3]}";
                }

                $this->File->write($output, "{$loopCNT}\t{$text}\r\n", 'a');
            }


            //if ($loopCNT > 10)
            //    break;
        }
        echo "Total: " . number_format($totalCNT, 0) . "\n";
        echo "Processed: " . number_format($realRecords, 0) . "\n";
        echo "No Commas: " . number_format($totalCNT - $hasCommas, 0) . "\n";
        echo "Commas: " . number_format($hasCommas, 0) . "\n";
        echo "</pre>";
    }
    public function matchNames()
    {
        set_time_limit(600);
        $this->autoRender = false;

        $file = WWW_ROOT . "APGroupList.txt";
        $output = str_replace('.txt', '.mis', $file);
        $groups = $this->GrouperApi->loadGroups();
        $contents = explode("\r\n", $this->File->read($file));

        echo "<pre>";
        $total = sizeof($contents);
        $groupStemCNT = sizeof($groups);

        $groupNames = array();
        $groupStemCNT = sizeof($groups);
        for ($loopCNT2 = 0; $loopCNT2 < $groupStemCNT; $loopCNT2++)
        {
            $values = $groups[$loopCNT2]['GrouperStem']['groups'];
            $groupCNT = sizeof($values);
            $found = false;
            for ($loopCNT3 = 0; $loopCNT3 < $groupCNT; $loopCNT3++)
            {
                $groupNames[] = $values[$loopCNT3]['GrouperGroup']['description'];
            }
        }

        //pr($groups);
        $matches = array();
        $missing = array();
        for ($loopCNT = 0; $loopCNT < $total; $loopCNT++)
        {
            if (!empty($contents[$loopCNT]))
            {
                if (in_array($contents[$loopCNT], $groupNames))
                    $matches[] = $contents[$loopCNT];
                else
                    $missing[] = $contents[$loopCNT];
            }
        }
        pr($missing);
        echo "</pre>";
    }
    public function parseNames()
    {
        set_time_limit(600);
        $this->autoRender = false;

        $file = WWW_ROOT . "/TRANSACT_MEMBER_NAMES-alt.txt";
        $output = WWW_ROOT . "/TRANSACT_MEMBER_NAMES-alt.sql";

        $contents = explode("\r\n", $this->File->read($file));
        $groups = array();

        echo "<pre>";
        $added = 0;
        $skipped = 0;
        $failed = 0;
        $total = sizeof($contents);
        for ($loopCNT = 0; $loopCNT < $total; $loopCNT++)
        {
            if (!empty($contents[$loopCNT]))
            {
                list($cardNumber, $uid, $name) = explode(";", $contents[$loopCNT], 3);

                if (!$this->String->contains($name, ",") ||
                    $this->String->endsWith($name, ",") ||
                    $this->String->contains($name, "\\") ||
                    $this->String->contains($name, "/"))
                {
                    //pr($contents[$loopCNT]);
                    $skipped++;
                }
                else
                {
                    $nameParts = explode(',', $name);
                    $sql = sprintf("INSERT INTO card_holders (ucla_uid, first_name, last_name) VALUES('%s', '%s', '%s');", $uid, str_replace("'", "''", trim($nameParts[1])), str_replace("'", "''", trim($nameParts[0])));
                    //echo $sql . "\n";

                    $this->File->write($output, $sql . "\n", 'a');

                    /*
                    $this->CardHolder->id = null;
                    $fields = array('UCLA_UID' => $uid, 'FIRST_NAME' => $nameParts[1], 'LAST_NAME' => $nameParts[0]);
                    $result = $this->CardHolder->save(array('CardHolder' => $fields));
                    if (!$result)
                    {
                        pr($result);
                        $added++;
                    }
                    else
                    {
                        $failed++;
                    }
                    */
                }
                if ($added > 1000000)
                    break;
                /*
                if (!isset($groups[$groupName]))
                    $groups[$groupName] = 0;

                $groups[$groupName]++;
                */
            }
        }

        echo "Added: " . number_format($added, 0) . "\n";
        echo "Skipped: " . number_format($skipped, 0) . "\n";
        echo "Failed: " . number_format($failed, 0) . "\n";
        echo "Total: " . number_format($total, 0) . "\n";
        echo "</pre>";
        exit(1);
        ksort($groups);
        echo "<pre>";
        foreach($groups as $key => $value)
        {
            $pattern = '/[\s\+\/\(\)]/i';
            echo preg_replace($pattern, "-", strtolower($key)) . "\t" . $key . "\n";
        }
        echo "</pre>";
        echo sizeof($groups);
        //pr($groups);
    }
    public function parse()
    {
        $this->autoRender = false;
        
        $file = WWW_ROOT . "/GROUPER_MEMBER_GROUP_MAPPING.txt";
        $contents = explode("\r\n", $this->File->read($file));
        $groups = array();
        
        $rowCNT = sizeof($contents);
        for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
        {
            if (!empty($contents[$loopCNT]))
            {
                list($uid, $groupName) = explode(";", $contents[$loopCNT], 2);
                if (!isset($groups[$groupName]))
                    $groups[$groupName] = 0;

                $groups[$groupName]++;   
            }
        }
        ksort($groups);
        echo "<pre>";
        foreach($groups as $key => $value)
        {
            $pattern = '/[\s\+\/\(\)]/i';
            echo preg_replace($pattern, "-", strtolower($key)) . "\t" . $key . "\n";
        }
        echo "</pre>";
        echo sizeof($groups);
        //pr($groups);
    }
    public function mysql()
    {
        $this->autoRender = false;

        $conditions = array();
        $result = $this->AuditUser->find("all", array("conditions" => $conditions));
        pr($result[0]);

        $fields = array("app_id" => 21, "definition_id" => 22, "comments" => date('Y-m-d H:i:s'), "action_date" => date('Y-m-d H:i:s'), "inserted" => date('Y-m-d H:i:s'));

        $this->AuditTest->id = null;
        $result = $this->AuditUser->save(array("AuditUser" => $fields));
        //showLastQuery();
        pr($result);
    }
    public function index()
    {
        $this->autoRender = false;

        $conditions = array();
        $fields = array("ID", "DEFINITION_ID");
        $result = $this->AuditTest->find("all", array("conditions" => $conditions));
        pr($result[0]);
        
        $uid = '202803676';
        $path = 'ucla:bruincard:AG:HHSDoorMerchant:HHSTestAG';
        $fields = array("UCLA_UID" => $uid, "PATH" => $path);
        $result = $this->PendingAccessPlan->deleteAll($fields);
        //$result = $this->PendingAccessPlan->delete(14);
        var_dump($result);
        exit(1);
        
        $id = 3;
        $result = $this->AuditTest->delete($id);
        var_dump($result);
        exit(1);

        $this->AuditTest->id = null;
        $fields = array("APP_ID" => 21, "DEFINITION_ID" => 22, "COMMENTS" => date('Y-m-d H:i:s'), "ACTION_DATE" => date('Y-m-d H:i:s'), "INSERTED" => date('Y-m-d H:i:s'));
        $result = $this->AuditTest->save(array("AuditTest" => $fields));
        //showLastQuery();
        pr($result);

        $fields = array("UCLA_UID" => '804554016', "PATH" => 'ucla:bruincard:AG:HHSDoorMerchant:HHSTestAG', "ACTION" => 'A');

        $result = $this->PendingAccessPlan->find("first", array("conditions" => $fields));
        if (isset($result['PendingAccessPlan']))
        {
            echo "EXISTS<BR><BR>";
            $result = $this->PendingAccessPlan->deleteAll($fields);
            echo "DELETE<BR><BR>";
            $result = $this->PendingAccessPlan->find("count", array("conditions" => $fields));
            echo "CNT: " . $result . "<BR><BR>";
        }
        else
        {
            echo "ADD<BR><BR>";
            $this->PendingAccessPlan->id = null;
            $result = $this->PendingAccessPlan->save(array("PendingAccessPlan" => $fields));
        }

        pr($result);
    }
    public function card_holders_load_text()
    {
        $this->autoRender = false;
        $this->autoLayout = false;

        $filePath = WWW_ROOT . 'files\\';
        $fileName = 'itservices-bc-TSCreate-wd0002.txt';

        if (file_exists($filePath . $fileName))
        {
            $content = explode("\n", $this->File->read($filePath . $fileName));
            $rowCNT = sizeof($content);
            for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
            {
                $cardHolder = array();
                $content[$loopCNT] = trim(str_replace("\r", "", $content[$loopCNT]));
                echo $loopCNT . '. ' . $content[$loopCNT];
                if (!empty($content[$loopCNT]))
                {
                    $fields = explode("|", $content[$loopCNT]);
                    /*
                        0 = uid
                        1 = last name
                        2 = first name
                        3 = middle name
                    */
                    if (isset($fields[0]) && !empty($fields[0]))
                    {
                        $uid = trim($fields[0]);
                        $cardHolder['uid'] = trim($uid);
                        $cardHolder['first_name'] = trim($fields[2]);
                        $cardHolder['last_name'] = trim($fields[1]);
                        $cardHolder['ucla_login_id'] = strtolower($this->_generateUCLALoginId(array($cardHolder['first_name'], $cardHolder['last_name'])));
                        $cardHolder['email'] = $cardHolder['ucla_login_id'] . '@ucla.edu';

                        if ($this->CardHolder->find('count', array('conditions' => array('ucla_login_id' => $cardHolder['ucla_login_id']))) == 0)
                        {
                            $cardHolder['inserted'] = date('Y-m-d H:m:s');

                            $this->CardHolder->id = null;
                            try
                            {
                                $result = $this->CardHolder->save(array('CardHolder' => $cardHolder));
                                echo ' - ADDED';
                            }
                            catch (Exception $ex)
                            {
                                pr($cardHolder);
                            }

                        }
                    }
                }
                echo "<BR>";
            }
        }
    }
    public function card_holders_load_outlook()
    {
        $this->autoRender = false;
        $this->autoLayout = false;

        $filePath = WWW_ROOT . 'files\\';
        $fileName = 'contacts.txt';
        $uid = 9000000001;

        if (file_exists($filePath . $fileName))
        {
            $content = explode("\n", $this->File->read($filePath . $fileName));
            $rowCNT = sizeof($content);
            for ($loopCNT = 1; $loopCNT < $rowCNT; $loopCNT++)
            {
                $cardHolder = array();
                $content[$loopCNT] = trim(str_replace("\r", "", $content[$loopCNT]));
                echo $loopCNT . '. ' . $content[$loopCNT];
                if (!empty($content[$loopCNT]))
                {
                    $fields = explode("\t", $content[$loopCNT]);

                    if (isset($fields[2]) && !empty($fields[2]))
                    {
                        $cardHolder['email'] = $this->_extractEmail($fields[2]);
                        $cardHolder['ucla_login_id'] = strtolower($this->_generateUCLALoginId($fields));

                        if ($this->CardHolder->find('count', array('conditions' => array('ucla_login_id' => $cardHolder['ucla_login_id']))) == 0)
                        {
                            $cardHolder['uid'] = $uid;
                            $cardHolder['first_name'] = $fields[0];
                            $cardHolder['last_name'] = $fields[1];

                            $cardHolder['inserted'] = date('Y-m-d H:m:s');

                            $uid++;

                            $this->CardHolder->id = null;
                            try
                            {
                                $result = $this->CardHolder->save(array('CardHolder' => $cardHolder));
                                echo ' - ADDED';
                            }
                            catch (Exception $ex)
                            {
                                pr($cardHolder);
                            }
                        }
                    }
                }
                echo "<BR>";
            }
        }
    }
    private function _generateUCLALoginId($values)
    {
        return str_replace(' ', '', substr($values[0], 0, 1) . $values[1]);
    }
    private function _extractEmail($value)
    {
        if(preg_match('!\(([^\)]+)\)!', $value, $match))
            $value = $match[1];

        return $value;
    }
}
?>