<?php
class GrouperApiComponent extends Object
{
    public $url = null;
    public $username = null;
    public $password = null;
    public $controller = null;
    public $validAttributeNames = array('uclauniversityid', 'uclalogonid', 'edupersonprincipalname');
    public $attributeNames = array('uclauniversityid', 'uclalogonid', 'edupersonprincipalname');

    public function __construct()
    {
        $values = Cache::read(CACHE_NAME_APPLICATION);

        $this->url = $values['api']['url'];
        $this->username = $values['api']['username'];
        $this->password = $values['api']['password'];
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
    public function beforeRedirect()
    {

    }

    public function processWithBody($type, $url, $credentials, $body)
    {
        $port = $this->controller->String->beginsWith($url, 'https') ? 443 : 80;
        $opts = array(
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_PORT => $port,
            CURLOPT_USERAGENT => 'curl-php',
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json; charset=UTF-8;', 'Accept: application/json'),
            CURLOPT_POSTFIELDS => json_encode($body)
        );

        $opts[CURLOPT_SSL_VERIFYHOST] = false;
        $opts[CURLOPT_SSL_VERIFYPEER] = false;

        $opts[CURLOPT_USERPWD] = "{$credentials['username']}:{$credentials['password']}";
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

        $this->controller->Http->status = isset($headers['http_code']) ? $headers['http_code'] : $this->controller->Http->STATUS_CODE_BAD_REQUEST;
        if (empty($error)) {
            $response = json_decode($response);
        }

        return $response;
    }


    public function process($type, $url, $credentials, $body = false)
    {
        $port = $this->controller->String->beginsWith($url, 'https') ? 443 : 80;
        $opts = array(
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_PORT => $port,
            CURLOPT_USERAGENT => 'curl-php',
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_HTTPHEADER => array('Content-Type: text/x-json; charset=UTF-8;', 'Accept: application/json')
        );

        $opts[CURLOPT_SSL_VERIFYHOST] = false;
        $opts[CURLOPT_SSL_VERIFYPEER] = false;

        $opts[CURLOPT_USERPWD] = "{$credentials['username']}:{$credentials['password']}";
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

        $this->controller->Http->status = isset($headers['http_code']) ? $headers['http_code'] : $this->controller->Http->STATUS_CODE_BAD_REQUEST;
        if (empty($error)) {
            $response = json_decode($response);
        }

        return $response;
    }
    public function getStems($name = null, $filterType = 'FIND_BY_STEM_NAME_APPROXIMATE')
    {
        $results = array();

        $params = array('stemName' => $name, 'stemQueryFilterType' => $filterType);
        $url = $this->url . "stems?wsLiteObjectType=WsRestFindStemsLiteRequest&" . http_build_query($params);

        //$response = $this->controller->Http->get($url, array("username" => $this->username, "password" => $this->password));
        $response = self::process('GET', $url, array("username" => $this->username, "password" => $this->password));
        if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK) {
            //$response = json_decode($this->controller->Http->content);

            if (isset($response->WsFindStemsResults) && $response->WsFindStemsResults) {
                if ($response->WsFindStemsResults->resultMetadata->success == 'T') {
                    $response = $response->WsFindStemsResults->stemResults;
                    $stemCNT = sizeof($response);
                    for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++) {
                        if ($response[$loopCNT]->name != $params['stemName']) {
                            $results[] = (array) $response[$loopCNT];
                        }
                    }
                }
            }
        }

        return $results;
    }
    public function getGroups($name = null, $filterType = 'FIND_BY_STEM_NAME')
    {
        $results = array();

        // PARAMS FOR TEST
        $params = array('stemName' => $name, 'queryFilterType' => $filterType);

        // PARAMS FOR PRODUCTION
        // $params = array('stemName' => "ucla:bruincard:etc:acl", 'queryFilterType' => $filterType);

        $url = $this->url . "groups?wsLiteObjectType=WsRestFindGroupsLiteRequest&" . http_build_query($params);

        //$response = $this->controller->Http->get($url, array("username" => $this->username, "password" => $this->password));
        $response = self::process('GET', $url, array("username" => $this->username, "password" => $this->password));


        // Legacy Merchant Logging
        if (DEBUG_WRITE) {
            $this->controller->Debug->write("GetGroups1");
        }
        ;
        if (DEBUG_WRITE) {
            $this->controller->Debug->write(json_encode(json_encode($url)));
        }
        ;
        if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK) {
            //$response = json_decode($this->controller->Http->content);

            if ($response->WsFindGroupsResults) {
                if ($response->WsFindGroupsResults->resultMetadata->success == 'T') {
                    if (isset($response->WsFindGroupsResults->groupResults)) {

                        $response = $response->WsFindGroupsResults->groupResults;
                        $stemCNT = sizeof($response);
                        for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++) {
                            if ($response[$loopCNT]->name != $params['stemName']) {
                                $results[] = (array) $response[$loopCNT];
                            }
                        }
                    }
                }
            }

        }

        return $results;
    }
    public function getMembers($name = null, $filterType = 'ALL')
    {
        error_log('getMembers');
        $results = array();

        $params = array('groupName' => $name, 'memberFilter' => $filterType);
        $url = $this->url . "groups/{$name}/members?retrieveSubjectDetail=true&wsLiteObjectType=WsRestGetMembersLiteRequest&" . http_build_query($params) . "&subjectAttributeNames=" . implode(',', $this->validAttributeNames);

        //$response = $this->controller->Http->get($url, array("username" => $this->username, "password" => $this->password));
        $response = self::process('GET', $url, array("username" => $this->username, "password" => $this->password));

        if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK) {
            if (is_object($response)) {
                //error_log('$response:\n' . print_r($response));
                //$response = json_decode($this->controller->Http->content);
                if ($response->WsGetMembersLiteResult) {
                    if ($response->WsGetMembersLiteResult->resultMetadata->success == 'T') {
                        // loop through the response 
                        if (isset($response->WsGetMembersLiteResult->wsSubjects)) {
                            $attributeNames = $response->WsGetMembersLiteResult->subjectAttributeNames;

                            $response = $response->WsGetMembersLiteResult->wsSubjects;
                            $stemCNT = sizeof($response);
                            for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++) {
                                // only act on the array that contains attributValues
                                if (isset($response[$loopCNT]->attributeValues)) {
                                    // loop through attributes
                                    $attrCNT = sizeof($response[$loopCNT]->attributeValues);
                                    for ($loopCNT2 = 0; $loopCNT2 < $attrCNT; $loopCNT2++) {
                                        $key = $attributeNames[$loopCNT2];

                                        if (in_array($key, $this->validAttributeNames)) {
                                            $response[$loopCNT]->$key = $response[$loopCNT]->attributeValues[$loopCNT2];
                                        }
                                    }
                                    unset($response[$loopCNT]->attributeValues);
                                }
                                $results[] = (array) $response[$loopCNT];
                            }
                        }

                        // if (isset($response->WsGetMembersLiteResult->wsSubjects))
                        // {
                        //     $response = $response->WsGetMembersLiteResult->wsSubjects;
                        //     $stemCNT = sizeof($response);
                        //     for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++)
                        //     {
                        //         if (isset($response[$loopCNT]->attributeValues))
                        //         {

                        //             // Altered code for hard coded values. Assuming they're always in this order.
                        //             $key = $this->attributeNames[0];
                        //             if(isset($response[$loopCNT]->attributeValues[0])) {
                        //                 $response[$loopCNT]->$key = $response[$loopCNT]->attributeValues[0];
                        //             }

                        //             $key = $this->attributeNames[1];
                        //             if(isset($response[$loopCNT]->attributeValues[1])) {
                        //                 $response[$loopCNT]->$key = $response[$loopCNT]->attributeValues[1];
                        //             }

                        //             $key = $this->attributeNames[2];
                        //             if(isset($response[$loopCNT]->attributeValues[2])) {
                        //                 $response[$loopCNT]->$key = $response[$loopCNT]->attributeValues[2];
                        //             }


                        //             unset($response[$loopCNT]->attributeValues);
                        //         }
                        //         $results[] = (array)$response[$loopCNT];
                        //     }   
                        // }


                    }
                }
            }
        }

        //error_log('$results:\n' . print_r($results));
        return $results;
    }
    public function getSubjects($ppid, $search = null)
    {
        $results = array();

        if (empty($ppid))
            $params = array('searchString' => $search);
        else
            $params = array('subjectId' => $ppid);

        $url = $this->url . "subjects?wsLiteObjectType=WsRestGetSubjectsLiteRequest&" . http_build_query($params) . "&subjectAttributeNames=" . implode(',', $this->attributeNames);
        //$response = $this->controller->Http->get($url, array("username" => $this->username, "password" => $this->password));
        $response = self::process('GET', $url, array("username" => $this->username, "password" => $this->password));

        if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK) {
            //$response = json_decode($this->controller->Http->content);
            if ($response->WsGetSubjectsResults) {
                if ($response->WsGetSubjectsResults->resultMetadata->success == 'T') {
                    if (isset($response->WsGetSubjectsResults->wsSubjects)) {
                        $response = $response->WsGetSubjectsResults->wsSubjects;
                        $stemCNT = sizeof($response);
                        for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++) {
                            $results[] = (array) $response[$loopCNT];
                        }
                    }
                }
            }
        }

        return $results;
    }
    public function addMembership($groupName, $identifier)
    {
        $results = array();

        $params = array('subjectIdentifier' => $identifier);
        $url = $this->url . "groups/{$groupName}/members/{$identifier}?wsLiteObjectType=WsRestAddMemberLiteRequest&" . http_build_query($params);

        //$response = $this->controller->Http->get($url, array("username" => $this->username, "password" => $this->password));
        $response = self::process('GET', $url, array("username" => $this->username, "password" => $this->password));

        if (in_array($this->controller->Http->status, array($this->controller->Http->STATUS_CODE_OK, $this->controller->Http->STATUS_CODE_CREATED))) {
            //$response = json_decode($this->controller->Http->content);

            if ($response->WsAddMemberLiteResult) {
                if ($response->WsAddMemberLiteResult->resultMetadata->success == 'T') {
                    if (isset($response->WsAddMemberLiteResult->wsSubject)) {
                        $results = (array) $response->WsAddMemberLiteResult->wsSubject;
                    }
                }
            }
        }

        return $results;
    }
    public function deleteMembership($groupName, $identifier)
    {
        $results = array();

        $params = array('subjectIdentifier' => $identifier);
        $url = $this->url . "groups/{$groupName}/members/{$identifier}?wsLiteObjectType=WsRestDeleteMemberLiteRequest&" . http_build_query($params);

        //$response = $this->controller->Http->get($url, array("username" => $this->username, "password" => $this->password));
        $response = self::process('GET', $url, array("username" => $this->username, "password" => $this->password));

        if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK) {
            //$response = json_decode($this->controller->Http->content);
            if ($response->WsDeleteMemberLiteResult) {
                if ($response->WsDeleteMemberLiteResult->resultMetadata->success == 'T') {
                    if (isset($response->WsDeleteMemberLiteResult->wsSubject)) {
                        $results = (array) $response->WsDeleteMemberLiteResult->wsSubject;
                    }
                }
            }
        }

        return $results;
    }
    public function getMerchantAccess($ppid)
    {
        $DCs = self::loadDCs();
        $merchants = array();

        $rowCNT = sizeof($DCs);

        for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++) {
            $results = Set::extract("/GrouperSubjects[id={$ppid}]", $DCs[$loopCNT]['subjects']);
            if (sizeof($results) > 0)
                $merchants[] = $DCs[$loopCNT]['extension'];
        }
        if ($ppid == 'urn:mace:ucla.edu:ppid:person:52CCAE3C0CA842578645757F142C9B84') {
            if (sizeof($merchants) == 0)
                $merchants = array('cfs-business-and-finance-svc', 'cfs-pmt-solutions-compliance', 'it-services');
        }

        return $merchants;
    }
    public function loadDCs($reload = false)
    {

        // 20240731 New Grouper Membership call
        $merchants2 = array();

        $newUrl = 'https://grouperws.it.ucla.edu/grouper-ws/servicesRest/4.9.0/memberships';
        $subPost_data = array('stemName' => 'training:bruincard-test:etc:acl');
        $post_data = array(
            'scope' => 'training:bruincard-test:etc:acl',
            'wsStemLookup' => $subPost_data,
            'stemScope' => 'ALL_IN_SUBTREE',
            'enabled' => 'T'
        );
        $body = array(
            'WsRestGetMembershipsRequest' => array(
                'scope' => 'training:bruincard-test:etc:acl',
                'wsStemLookup' => $subPost_data,
                'stemScope' => 'ALL_IN_SUBTREE',
                'enabled' => 'T'
            )
        );
        $response2 = self::processWithBody('GET', $newUrl, array("username" => $this->username, "password" => $this->password), $body);


        if ($response2 && $response2->WsFindGroupsResults) {
            if ($response2->WsFindGroupsResults->resultMetadata->success == 'T') {
                if (isset($response2->WsFindGroupsResults->groupResults)) {

                    $response2 = $response2->WsFindGroupsResults->groupResults;
                    $stemCNT = sizeof($response2);
                    for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++) {
                        if ($response2[$loopCNT]->name != 'training:bruincard-test:etc:acl') {
                            $results2[] = (array) $response2[$loopCNT];
                            if (DEBUG_WRITE) {
                                $this->controller->Debug->write("Results2");
                            }
                            ;
                            if (DEBUG_WRITE) {
                                $this->controller->Debug->write(json_encode($results2));
                            }
                            ;
                        }
                    }
                }
            }
        }

        $merchants2 = $response2;

        // Merchant2 Logging
        if (DEBUG_WRITE) {
            $this->controller->Debug->write("Merchants2");
        }
        ;
        if (DEBUG_WRITE) {
            $this->controller->Debug->write(json_encode($merchants2));
        }
        ;



        // 20240802 Legacy Call slower performance
        $merchants = array();

        $this->controller->CacheObject->clear(CACHE_NAME_GROUPER_MERCHANTS);
        if (!$this->controller->CacheObject->exists(CACHE_NAME_GROUPER_MERCHANTS) || $reload) {
            error_log('loadDCs, Cache not found');
            $appValues = Cache::read(CACHE_NAME_APPLICATION);

            $this->controller->CacheObject->duration = strtolower($appValues['cache']['grouper']['merchants']);

            if (DEBUG_WRITE) {
                $this->controller->Debug->write("Start Load DCs");
            }
            ;
            $merchants = self::getGroups($appValues['stem']['path']['dc']);

            // Legacy Merchant Logging
            if (DEBUG_WRITE) {
                $this->controller->Debug->write("Merchants1");
            }
            ;
            if (DEBUG_WRITE) {
                $this->controller->Debug->write(json_encode($merchants));
            }
            ;

            $groupCNT = sizeof($merchants);

            for ($loopCNT = 0; $loopCNT < $groupCNT; $loopCNT++) {
                $groupName = $merchants[$loopCNT]['name'];
                $merchants[$loopCNT]['subjects'] = $this->controller->Array->convertLikeModel('GrouperSubjects', self::getMembers($groupName));
            }

            $this->controller->CacheObject->set(CACHE_NAME_GROUPER_MERCHANTS, $merchants);

            if (DEBUG_WRITE) {
                $this->controller->Debug->write("End Load DCs");
            }
            ;

        } else {
            $merchants = $this->controller->CacheObject->get(CACHE_NAME_GROUPER_MERCHANTS);
        }


        return $merchants;
    }
    public function loadGroups($reload = false)
    {
        $stems = array();
        if (!$this->controller->CacheObject->exists(CACHE_NAME_GROUPER_MERCHANT_GROUPS) || $reload) {
            $appValues = Cache::read(CACHE_NAME_APPLICATION);
            $this->controller->CacheObject->duration = strtolower($appValues['cache']['grouper']['merchant']['groups']);

            $stemName = $appValues['stem']['path']['ag'];
            $stems = $this->controller->Array->convertLikeModel('GrouperStem', self::getStems($stemName));

            $stemCNT = sizeof($stems);
            for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++) {
                //==> Get Groups
                $groupStemName = $stems[$loopCNT]['GrouperStem']['name'];
                $stems[$loopCNT]['GrouperStem']['groups'] = $this->controller->Array->convertLikeModel('GrouperGroup', self::getGroups($groupStemName));
            }

            $this->controller->CacheObject->set(CACHE_NAME_GROUPER_MERCHANT_GROUPS, $stems);
        } else {
            $stems = $this->controller->CacheObject->get(CACHE_NAME_GROUPER_MERCHANT_GROUPS);
        }

        return $stems;
    }
    public function allowDoorAccess($merchants, $name)
    {
        $result = array();
        $groups = self::loadGroups();
        $groupCNT = sizeof($groups);

        for ($loopCNT = 0; $loopCNT < $groupCNT; $loopCNT++) {
            //==> Get Groups
            $merchantName = $groups[$loopCNT]['GrouperStem']['extension'];
            if (in_array($merchantName, $merchants)) {
                $rowCNT = sizeof($groups[$loopCNT]['GrouperStem']['groups']);
                for ($loopCNT2 = 0; $loopCNT2 < $rowCNT; $loopCNT2++) {
                    $row = $groups[$loopCNT]['GrouperStem']['groups'][$loopCNT2]['GrouperGroup'];
                    if (strtolower($row['name']) == strtolower($name)) {
                        $result = $row;
                        break;
                    }
                }
            }
        }

        return $result;
    }
}
?>