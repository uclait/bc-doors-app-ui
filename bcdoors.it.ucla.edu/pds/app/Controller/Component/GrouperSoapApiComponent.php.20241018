<?php
class GrouperSoapApiComponent extends Object
{
    public $instance = NULL;
    public $clientVersion = null;
    public $url = null;
    public $endPoint = null;
    public $username = null;
    public $password = null;
    public $controller = null;
    private $_configValues = array();
    
    public function __construct()
    {
        $this->_configValues = Cache::read(CACHE_NAME_APPLICATION);

        $this->clientVersion = $this->_configValues['api']['version'];
        $this->url = $this->_configValues['api']['wsdl'];
        $this->endPoint = $this->_configValues['api']['url'];
        $this->username = $this->_configValues['api']['username'];
        $this->password = $this->_configValues['api']['password'];
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
    public function getInstance()
    {
     
        if (!$this->instance) 
        {
            $opts = array('ssl' => array('ciphers' => 'TLS1.1'));

            $params = array('exceptions' => 0,
                            'login' => $this->username,
                            'password' => $this->password,
                            'trace' => 1);
             
            try
            {
                /*

                $opts = array('location' => 'http://grouperws.it.ucla.edu/grouper-ws/services/GrouperService_v2_1?wsdl',
                            'login' => $this->username,
                            'password' => $this->password,
                              'uri'      => 'urn:getMemberships');

                $client = new SOAPClient(null, $opts);

                $quote = $client->__soapCall('getMemberships', array('EBAY'));

                var_dump($client);
                var_dump($quote);
                exit(1);
                $local_cert = "/var/www/bcdoors.it.ucla.edu/app/webroot/files/InCommonServerCA.crt";
                */
                /*
                $params['ssl'] = array(
                                    'verify_peer'   => true,
                                    'cafile'        => $local_cert,
                                    'verify_depth'  => 5,
                                    #'CN_match'      => 'api.twitter.com',
                                    'disable_compression' => true,
                                    'SNI_enabled'         => true,
                                    'ciphers'             => 'ALL!EXPORT!EXPORT40!EXPORT56!aNULL!LOW!RC4');
                */
                //$context = array('ssl' => array('ciphers'=>'RC4-SHA'));
                //$params['stream_context'] = stream_context_create(array('ssl' => $params['ssl']));

                //$this->url = null;
                //$params['location'] = 'http://grouperws.it.ucla.edu/grouper-ws/services/GrouperService_v2_1.GrouperService_v2_1HttpSoap11Endpoint/';

                //$this->url = 'https://' . urlencode($this->username) . ': ' . urlencode($this->password) . '@grouperws.it.ucla.edu/grouper-ws/services/GrouperService_v2_1?wsdl';

                $this->instance = new SoapClient($this->url, $params);
            }
            catch (SoapFault $e)
            {
                echo $e->faultstring;

                /*
                echo "REQUEST:\n" . $this->instance->__getLastRequest() . "\n";
                echo "REQUEST HEADERS:\n" . $this->instance->__getLastRequestHeaders() . "\n";
                echo "RESPONSE HEADERS:\n" . $this->instance->__getLastResponseHeaders() . "\n";
                echo "Response:\n" . $this->instance->__getLastResponse() . "\n";
                echo "Exception: \n" . $e->getMessage() . "\n";
                echo "Trace: \n" . $e->getTraceAsString() . "\n";
                */
            }
        }
         
        return $this->instance;
    }
    public function getStems($name, $filterType = 'FIND_BY_STEM_NAME_APPROXIMATE')
    {
        $results = array();
        $client = self::getInstance();
        
        $params = array('clientVersion' => $this->clientVersion,
                        'wsStemQueryFilter' => array('stemQueryFilterType' => $filterType,
                                                     'stemName' => $name));

        $response = $client->findStems($params);
        if ($response->return->resultMetadata->success == 'T')
        {
            $response = $response->return->stemResults;
            $stemCNT = sizeof($response);
            for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++)
            {
                if ($response[$loopCNT]->name != $name)
                {
                    $results[] = (array)$response[$loopCNT];
                }
            }
        }
        
        return $results;
    }
    public function getGroups($name, $filterType = 'FIND_BY_STEM_NAME')
    {
        $results = array();
        $client = self::getInstance();
        
        $params = array('clientVersion' => $this->clientVersion,
                        'wsMemberFilter' => array('memberFilter' => 'All', 'groupName' => $name));
            
        $response = $client->getGroups($params);

        if ($response->return->resultMetadata->success == 'T')
        {
            $response = $response->return->groupResults;
            $stemCNT = sizeof($response);
            for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++)
            {
                if ($response[$loopCNT]->name != $name)
                {
                    $results[] = (array)$response[$loopCNT];
                }
            }
        }
        
        return $results;
    }
    public function getMembers($name = null, $filterType = 'ALL')
    {
        $results = array();
        
        $params = array('groupName' => $name, 'memberFilter' => $filterType);
        $url = $this->url . "groups/{$name}/members?includeSubjectDetail=true&wsLiteObjectType=WsRestGetMembersLiteRequest&" . http_build_query($params);

        $response = $this->controller->Http->get($url, array("username" => $this->username, "password" => $this->password));

        if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK)
        {
            $response = json_decode($this->controller->Http->content);
            if ($response->WsGetMembersLiteResult)
            {
                if ($response->WsGetMembersLiteResult->resultMetadata->success = 'T')
                {
                    $response = $response->WsGetMembersLiteResult->wsSubjects;
                    $stemCNT = sizeof($response);
                    for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++)
                    {
                            $results[] = (array)$response[$loopCNT];
                    }
                }
            }   
        }
        
        return $results;
    }
    public function _getMemberships($subjectId)
    {
        $results = array();
        $client = self::getInstance();

        $params = array('clientVersion' => $this->clientVersion,
                        'includeSubjectDetail' => 'true',
                        'includeGroupDetail' => 'true',
                        'wsSubjectLookups' => array('subjectIdentifier' => $subjectId));

        try
        {
            $response = $client->getMemberships($params);
            //echo "REQUEST:\n" . $client->__getLastRequest() . "\n";

            if ($response->return->resultMetadata->success == 'T')
            {
                $accessGroup = $this->_configValues['stem']['path']['ag'];
                $response = $response->return->wsGroups;
                $groupCNT = sizeof($response);
                for ($loopCNT = 0; $loopCNT < $groupCNT; $loopCNT++)
                {
                    if ($this->controller->String->beginsWith($response[$loopCNT]->name, $accessGroup))
                    {
                        $data = (array)$response[$loopCNT];
                        if (isset($data['detail']))
                            $data['detail'] = (array)$data['detail'];

                        $results[] = $data;
                    }
                }
            }
        }
        catch (SoapFault $E)
        {
            echo $E->faultstring;
        }

        return $results;
    }
    public function getMemberships($subjectId)
    {
        $results = array();

        $request = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
                   '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://soap_v2_1.ws.grouper.middleware.internet2.edu/xsd">' . "\n" .
                   '    <SOAP-ENV:Body>' . "\n" .
                   '        <ns1:getMemberships>' . "\n" .
                   '            <ns1:clientVersion>' . $this->clientVersion . '</ns1:clientVersion>' . "\n" .
                   '            <ns1:wsSubjectLookups>' . "\n" .
                   '                <ns1:subjectIdentifier>' . $subjectId . '</ns1:subjectIdentifier>' . "\n" .
                   '            </ns1:wsSubjectLookups>' . "\n" .
                   '            <ns1:includeSubjectDetail>true</ns1:includeSubjectDetail>' . "\n" .
                   '            <ns1:includeGroupDetail>true</ns1:includeGroupDetail>' . "\n" .
                   '        </ns1:getMemberships>' . "\n" .
                   '    </SOAP-ENV:Body>' . "\n" .
                   '</SOAP-ENV:Envelope>';

        try
        {
            $response = self::_process("getMemberships", $request);
            if (isset($response['soapenvBody']))
            {
                $response = $response['soapenvBody']['nsgetMembershipsResponse'];

                if ($response['nsreturn']['nsresultMetadata']['nssuccess'] == 'T')
                {
                    $accessGroup = $this->_configValues['stem']['path']['ag'];
                    $response = $response['nsreturn']['nswsGroups'];

                    if (sizeof($response) > 0)
                    {
                        if (isset($response['nsdescription']))
                        {
                            $response = array($response);
                        }
                    }
                    $groupCNT = sizeof($response);
                    for ($loopCNT = 0; $loopCNT < $groupCNT; $loopCNT++)
                    {
                        //if ($this->controller->String->beginsWith($response[$loopCNT]['nsname'], $accessGroup))
                        //{
                            $data = array('description' => $response[$loopCNT]['nsdescription'],
                                          'displayExtension' => $response[$loopCNT]['nsdisplayExtension'],
                                          'displayName' => $response[$loopCNT]['nsdisplayName'],
                                          'extension' => $response[$loopCNT]['nsextension'],
                                          'name' => $response[$loopCNT]['nsname'],
                                          'typeOfGroup' => $response[$loopCNT]['nstypeOfGroup'],
                                          'uuid' => $response[$loopCNT]['nsuuid']);

                            if (isset($response[$loopCNT]['nsdetail']))
                            {
                                $data['detail'] = array();
                                foreach ($response[$loopCNT]['nsdetail'] as $key => $value)
                                {
                                    $data['detail'][substr($key, 2)] = $value;
                                }
                            }

                            $results[] = $data;
                        //}
                    }
                }
            }
        }
        catch (SoapFault $e)
        {
            echo $e->faultstring;
        }

        return $results;
    }
    function _process($action, $request)
    {
       $headers = array("Content-type: text/xml;charset=\"utf-8\"",
                        "Accept: text/xml",
                        "Cache-Control: no-cache",
                        "Pragma: no-cache",
                        "SOAPAction: {$this->endPoint}/{$action}", 
                        "Content-length: " . strlen($request));

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username.":".$this->password); // username and password - declared at the top of the doc
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        $response = curl_exec($ch); 
        curl_close($ch);

        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = simplexml_load_string($xml);
        $json = json_encode($xml);
        $responseArray = json_decode($json, true);

        return $responseArray;
    }
}
?>
