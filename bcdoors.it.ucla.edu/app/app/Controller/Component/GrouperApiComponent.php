<?php
class GrouperApiComponent extends Object
{
    public $url = null;
    public $grouper_v4_0_9_url = null;
    public $username = null;
    public $password = null;
    public $controller = null;
    public $validAttributeNames = array('uclauniversityid', 'uclalogonid', 'edupersonprincipalname');
    public $attributeNames = array('uclauniversityid', 'uclalogonid', 'edupersonprincipalname');

    public function __construct()
    {
        $values = Cache::read(CACHE_NAME_APPLICATION);

        $this->url = $values['api']['url'];
        $this->grouper_v4_0_9_url = $values['api']['v4_0_9'];
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
        // $url = $this->url . "stems?wsLiteObjectType=WsRestFindStemsLiteRequest&" . http_build_query($params);

        // //$response = $this->controller->Http->get($url, array("username" => $this->username, "password" => $this->password));
        // $response = self::process('GET', $url, array("username" => $this->username, "password" => $this->password));


        // if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK) {
        //     //$response = json_decode($this->controller->Http->content);            

        //     if (isset($response->WsFindStemsResults) && $response->WsFindStemsResults) {
        //         if ($response->WsFindStemsResults->resultMetadata->success == 'T') {
        //             $response = $response->WsFindStemsResults->stemResults;
        //             $stemCNT = sizeof($response);
        //             for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++) {
        //                 if ($response[$loopCNT]->name != $params['stemName']) {
        //                     $results[] = (array) $response[$loopCNT];
        //                 }
        //             }
        //         }
        //     }
        // }

        // 20241016 New Grouper GetStems Call
        $newUrlG = $this->grouper_v4_0_9_url . 'stems';
        $appValues = Cache::read(CACHE_NAME_APPLICATION);

            $bodyG = array(
                'WsRestFindStemsRequest' => array(
                    "wsStemQueryFilter" => array(
                        "stemQueryFilterType" => "FIND_BY_STEM_NAME_APPROXIMATE",
                        "stemName" => $name,
                    )
                )
            );

            $responseG2 = self::processWithBody('GET', $newUrlG, array("username" => $this->username, "password" => $this->password), $bodyG);

            

        if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK) {
            //$response = json_decode($this->controller->Http->content);

            // if (DEBUG_WRITE) {
            //     echo ("<script>console.log('getStems: " . json_encode($responseG2->WsFindStemsResults) . "');</script>"); //browser console
            //     echo ("<script>console.log('getStems: " . json_encode($newUrlG) . "');</script>"); //browser console
            //     echo ("<script>console.log('getStems: " . json_encode($bodyG) . "');</script>"); //browser console
            //     $this->controller->Debug->write(json_encode($newUrlG));
            //  }    

            if (isset($responseG2->WsFindStemsResults) && $responseG2->WsFindStemsResults) {
               
                if ($responseG2->WsFindStemsResults->resultMetadata->success == 'T') {
                    $response = $responseG2->WsFindStemsResults->stemResults;
                    $stemCNT = sizeof($response);
                    for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++) {
                        if ($response[$loopCNT]->name != $params['stemName']) {
                            $results[] = (array) $response[$loopCNT];
                        }
                    }
                }
            }
        }

        // END 20241016 NEW GROUPER GET STEMS CALL

         return $results;
    }

    public function getGroups($name = null, $filterType = 'FIND_BY_STEM_NAME')
    {
        $results = array();

        $params = array('stemName' => $name, 'queryFilterType' => $filterType);

        // 20241017 Legacy Grouper v2.1.5 code
        // $url = $this->url . "groups?wsLiteObjectType=WsRestFindGroupsLiteRequest&" . http_build_query($params);

        // $response = self::process('GET', $url, array("username" => $this->username, "password" => $this->password));

        // if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK) {
        //     //$response = json_decode($this->controller->Http->content);

        //     if ($response->WsFindGroupsResults) {
        //         if ($response->WsFindGroupsResults->resultMetadata->success == 'T') {

        //             if (isset($response->WsFindGroupsResults->groupResults)) {

        //                 $response = $response->WsFindGroupsResults->groupResults;
        //                 $stemCNT = sizeof($response);
        //                 for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++) {
        //                     if ($response[$loopCNT]->name != $params['stemName']) {
        //                         $results[] = (array) $response[$loopCNT];
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }


        // 20241016 New Grouper Groups Call for Grouper v4.0.9
        $newUrlG = $this->grouper_v4_0_9_url . 'groups';
        $appValues = Cache::read(CACHE_NAME_APPLICATION);

            $bodyG = array(
                'WsRestFindGroupsRequest' => array(
                    "wsQueryFilter" => array(
                        "queryFilterType" => "FIND_BY_STEM_NAME",
                        "stemName" => $name,
                    )
                )
            );

            $responseG2 = self::processWithBody('GET', $newUrlG, array("username" => $this->username, "password" => $this->password), $bodyG);

            

        if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK) {

            if ($responseG2->WsFindGroupsResults) {
                if ($responseG2->WsFindGroupsResults->resultMetadata->success == 'T') {

                    if (isset($responseG2->WsFindGroupsResults->groupResults)) {
                        // if (DEBUG_WRITE) {
                        //     echo ("<script>console.log('getGroupResult: " . json_encode($newUrlG) . "');</script>"); //browser console
                        //     $this->controller->Debug->write(json_encode($newUrlG));
                        //  }
                        $response = $responseG2->WsFindGroupsResults->groupResults;
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

        // END 20241016 NEW GROUPER GET GROUPS CALL

        return $results;
    }

    public function getMembers($name = null, $filterType = 'ALL')
    {
        error_log('getMembers');
        $results = array();

        $params = array('groupName' => $name, 'memberFilter' => $filterType);
        
        // 20241017 Legacy Grouper v2.1.5 call
        // $url = $this->url . "groups/{$name}/members?retrieveSubjectDetail=true&wsLiteObjectType=WsRestGetMembersLiteRequest&" . http_build_query($params) . "&subjectAttributeNames=" . implode(',', $this->validAttributeNames);

        // $response = self::process('GET', $url, array("username" => $this->username, "password" => $this->password));


        // if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK) {
        //     if (is_object($response)) {
        //         //$response = json_decode($this->controller->Http->content);
        //         if ($response->WsGetMembersLiteResult) {
        //             if ($response->WsGetMembersLiteResult->resultMetadata->success == 'T') {
        //                 // loop through the response 
        //                 if (isset($response->WsGetMembersLiteResult->wsSubjects)) {
        //                     $attributeNames = $response->WsGetMembersLiteResult->subjectAttributeNames;

        //                     $response = $response->WsGetMembersLiteResult->wsSubjects;
        //                     $stemCNT = sizeof($response);
        //                     for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++) {
        //                         // only act on the array that contains attributValues
        //                         if (isset($response[$loopCNT]->attributeValues)) {
        //                             // loop through attributes
        //                             $attrCNT = sizeof($response[$loopCNT]->attributeValues);
        //                             for ($loopCNT2 = 0; $loopCNT2 < $attrCNT; $loopCNT2++) {
        //                                 $key = $attributeNames[$loopCNT2];

        //                                 if (in_array($key, $this->validAttributeNames)) {
        //                                     $response[$loopCNT]->$key = $response[$loopCNT]->attributeValues[$loopCNT2];
        //                                 }
        //                             }
        //                             unset($response[$loopCNT]->attributeValues);
        //                         }
        //                         $results[] = (array) $response[$loopCNT];
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }
     

        // 20241017 New Grouper v4.0.9 getMembers rest Update
        $resultsG = array();
        $newUrlG = $this->grouper_v4_0_9_url . 'groups';
        $appValues = Cache::read(CACHE_NAME_APPLICATION);

        $wsGroupLookups = array("groupName" => $name); // Work around on [] array formatting
        $bodyG = array(
            'WsRestGetMembersRequest' => array(
                "wsGroupLookups" => array(
                    $wsGroupLookups
                ),
                "includeSubjectDetail" => "T",
                "subjectAttributeNames" => array(
                    "uclauniversityid",
                    "uclalogonid",
                    "edupersonprincipalname",
                )
            )
        );

        $responseG2 = self::processWithBody('GET', $newUrlG, array("username" => $this->username, "password" => $this->password), $bodyG);

        if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK) {
            if (is_object($responseG2)) {
                //$response = json_decode($this->controller->Http->content);
                if ($responseG2->WsGetMembersResults) {
                    if ($responseG2->WsGetMembersResults->resultMetadata->success == 'T') {
                        // loop through the response 
                        if (isset($responseG2->WsGetMembersResults->results[0]->wsSubjects)) {
                            
                            $attributeNames = $responseG2->WsGetMembersResults->subjectAttributeNames;

                            $responseG = $responseG2->WsGetMembersResults->results[0]->wsSubjects;
                            $stemCNT = sizeof($responseG);
                            for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++) {
                                // only act on the array that contains attributValues
                                if (isset($responseG[$loopCNT]->attributeValues)) {
                                    // loop through attributes
                                    $attrCNT = sizeof($responseG[$loopCNT]->attributeValues);
                                    for ($loopCNT2 = 0; $loopCNT2 < $attrCNT; $loopCNT2++) {
                                        $key = $attributeNames[$loopCNT2];

                                        if (in_array($key, $this->validAttributeNames)) {
                                            $responseG[$loopCNT]->$key = $responseG[$loopCNT]->attributeValues[$loopCNT2];
                                        }
                                    }
                                    unset($responseG[$loopCNT]->attributeValues); // Removes
                                }
                                $results[] = (array) $responseG[$loopCNT];
                            }
                        }
                    }
                }
            }
        }

        // if (DEBUG_WRITE) {
        //     // echo ("<script>console.log('getNEWMembers: " . json_encode($newUrlG) . "');</script>"); //browser console
        //     // echo ("<script>console.log('getNEWMembers: " . json_encode($bodyG) . "');</script>"); //browser console
        //     echo ("<script>console.log('getNEWMembersResultsG: " . json_encode($resultsG) . "');</script>"); //browser console
        //     $this->controller->Debug->write(json_encode($newUrlG));
        // }

        // END 20241017 New Grouper v4.0.9 getMembers update

        //error_log('$results:\n' . print_r($results));
        return $results;
    }


    public function getSubjects($ppid, $search = null)
    {
        $results = array();

        // 20241017 Legacy Grouper V2.1.5 Call
        // if (empty($ppid))
        //     $params = array('searchString' => $search);
        // else
        //     $params = array('subjectId' => $ppid);

        // $url = $this->url . "subjects?wsLiteObjectType=WsRestGetSubjectsLiteRequest&" . http_build_query($params) . "&subjectAttributeNames=" . implode(',', $this->attributeNames);
        
	    // if (DEBUG_WRITE) {
        //    echo ("<script>console.log('getSubjects: " . json_encode($url) . "');</script>"); //browser console
        //    $this->controller->Debug->write(json_encode($url));
        // }

        // $response = self::process('GET', $url, array("username" => $this->username, "password" => $this->password));

        // if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK) {
        //     //$response = json_decode($this->controller->Http->content);
        //     if ($response->WsGetSubjectsResults) {
        //         if ($response->WsGetSubjectsResults->resultMetadata->success == 'T') {
        //             if (isset($response->WsGetSubjectsResults->wsSubjects)) {
        //                 $response = $response->WsGetSubjectsResults->wsSubjects;
        //                 $stemCNT = sizeof($response);
        //                 for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++) {
        //                     $results[] = (array) $response[$loopCNT];
        //                 }
        //             }
        //         }
        //     }
        // }


        // 20241017 New Grouper API v4.0.9 Restful Call for GetSubjects
        $resultsG = array();
        $newUrlG = $this->grouper_v4_0_9_url . 'subjects';
        $appValues = Cache::read(CACHE_NAME_APPLICATION);

        $bodyG = array();

        if (empty($ppid))
            $bodyG = array(
                'WsRestGetSubjectsRequest' => array(
                    "includeSubjectDetail" => "T",
                    "subjectAttributeNames" => array(
                        "uclauniversityid",
                        "uclalogonid",
                        "edupersonprincipalname",
                    ),
                    "searchString" => $search
                )
            );
        else
            $bodyG = array(
                'WsRestGetSubjectsRequest' => array(
                    "includeSubjectDetail" => "T",
                    "subjectAttributeNames" => array(
                        "uclauniversityid",
                        "uclalogonid",
                        "edupersonprincipalname",
                    ),
                    "actAsSubjectLookup" => array(
                        "subjectId" => $ppid
                    ),
                )
            );
        

        $responseG2 = self::processWithBody('GET', $newUrlG, array("username" => $this->username, "password" => $this->password), $bodyG);

        if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK) {
            //$response = json_decode($this->controller->Http->content);
            if ($responseG2->WsGetSubjectsResults) {
                if ($responseG2->WsGetSubjectsResults->resultMetadata->success == 'T') {
                    if (isset($responseG2->WsGetSubjectsResults->wsSubjects)) {
                        $response = $responseG2->WsGetSubjectsResults->wsSubjects;
                        $stemCNT = sizeof($response);
                        for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++) {
                            $results[] = (array) $response[$loopCNT];
                        }
                    }
                }
            }
        }

        // if (DEBUG_WRITE) {
        //     echo ("<script>console.log('getNewSubjects: " . json_encode($newUrlG) . "');</script>"); //browser console
        //     echo ("<script>console.log('getNewSubjects: " . json_encode($bodyG) . "');</script>"); //browser console
        //     echo ("<script>console.log('getNewSubjects: " . json_encode($resultsG) . "');</script>"); //browser console
        //     $this->controller->Debug->write(json_encode($newUrlG));
        // }

        // END 20241017 New GetSubjects 

        //Temporary to test the addMemberhsip and deleteMembership ability (just refresh the page)
        // self::addMembership("training:bruincard-test:access-plan-group:it-services:itsg-csb1-24x7", "003266233");
        // self::deleteMembership("training:bruincard-test:access-plan-group:it-services:itsg-csb1-24x7", "003266233");

        return $results;
    }

    public function addMembership($groupName, $identifier)
    {
        $results = array();

        // 20241017 Legacy call for Grouper v2.1.5
        // $params = array('subjectIdentifier' => $identifier);
        // $url = $this->url . "groups/{$groupName}/members/{$identifier}?wsLiteObjectType=WsRestAddMemberLiteRequest&" . http_build_query($params);


        // //$response = $this->controller->Http->get($url, array("username" => $this->username, "password" => $this->password));
        // $response = self::process('GET', $url, array("username" => $this->username, "password" => $this->password));

        // if (in_array($this->controller->Http->status, array($this->controller->Http->STATUS_CODE_OK, $this->controller->Http->STATUS_CODE_CREATED))) {
        //     //$response = json_decode($this->controller->Http->content);

        //     if ($response->WsAddMemberLiteResult) {
        //         if ($response->WsAddMemberLiteResult->resultMetadata->success == 'T') {
        //             if (isset($response->WsAddMemberLiteResult->wsSubject)) {
        //                 $results = (array) $response->WsAddMemberLiteResult->wsSubject;
        //             }
        //         }
        //     }
        // }

        // 20241017 New Grouper API v4.0.9 Restful Call for add member
        $resultsG = array();
        $newUrlG = $this->grouper_v4_0_9_url . 'groups';
        $appValues = Cache::read(CACHE_NAME_APPLICATION);

        $lookups = array(
            "subjectIdentifier" => $identifier,
            "subjectSourceId" => "ldap"
        );
        $bodyG = array(
                'WsRestAddMemberRequest' => array(
                    "wsGroupLookup" => array(
                        "groupName" => $groupName
                    ),
                    "subjectLookups" => array(
                        $lookups
                    ),
                    "includeGroupDetail" => "T",
                    "includeSubjectDetail" => "T",
                    "addExternalSubjectIfNotFound" => "F"
                )
            );
        

        $responseG2 = self::processWithBody('GET', $newUrlG, array("username" => $this->username, "password" => $this->password), $bodyG);

        if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK) {
            //$response = json_decode($this->controller->Http->content);
            if ($responseG2->WsAddMemberResults) {
                if ($responseG2->WsAddMemberResults->resultMetadata->success == 'T') {
                    if (isset($responseG2->WsAddMemberResults->wsSubjects)) {
                        $response = $responseG2->WsAddMemberResults->results[0]->wsSubjects;
                        $stemCNT = sizeof($response);
                        for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++) {
                            $results[] = (array) $response[$loopCNT];
                        }
                    }
                }
            }
        }

        // if (DEBUG_WRITE) {
        //     echo ("<script>console.log('addNewMembership: " . json_encode($newUrlG) . "');</script>"); //browser console
        //     echo ("<script>console.log('addNewMembership: " . json_encode($bodyG) . "');</script>"); //browser console
        //     echo ("<script>console.log('addNewMembership: " . json_encode($responseG2) . "');</script>"); //browser console
        //     echo ("<script>console.log('addNewMembership: " . json_encode($results) . "');</script>"); //browser console
        //     $this->controller->Debug->write(json_encode($newUrlG));
        // }

        // END 20241017 New add member

        return $results;
    }
    public function deleteMembership($groupName, $identifier)
    {
        $results = array();

        // $params = array('subjectIdentifier' => $identifier);
        // $url = $this->url . "groups/{$groupName}/members/{$identifier}?wsLiteObjectType=WsRestDeleteMemberLiteRequest&" . http_build_query($params);

        // //if (DEBUG_WRITE) {
        // //    echo ("<script>console.log('deleteMembership: " . json_encode($url) . "');</script>"); //browser console
        // //    $this->controller->Debug->write(json_encode($url));
        // //}

        // //$response = $this->controller->Http->get($url, array("username" => $this->username, "password" => $this->password));
        // $response = self::process('GET', $url, array("username" => $this->username, "password" => $this->password));

        // if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK) {
        //     //$response = json_decode($this->controller->Http->content);
        //     if ($response->WsDeleteMemberLiteResult) {
        //         if ($response->WsDeleteMemberLiteResult->resultMetadata->success == 'T') {
        //             if (isset($response->WsDeleteMemberLiteResult->wsSubject)) {
        //                 $results = (array) $response->WsDeleteMemberLiteResult->wsSubject;
        //             }
        //         }
        //     }
        // }

        // 20241017 New Grouper API v4.0.9 Restful Call for add member
        $resultsG = array();
        $newUrlG = $this->grouper_v4_0_9_url . 'groups';
        $appValues = Cache::read(CACHE_NAME_APPLICATION);

        $lookups = array(
            "subjectIdentifier" => $identifier,
            "subjectSourceId" => "ldap"
        );
        $bodyG = array(
                'WsRestDeleteMemberRequest' => array(
                    "wsGroupLookup" => array(
                        "groupName" => $groupName
                    ),
                    "subjectLookups" => array(
                        $lookups
                    ),
                    "includeGroupDetail" => "T",
                    "includeSubjectDetail" => "T",
                    "addExternalSubjectIfNotFound" => "F"
                )
            );
        

        $responseG2 = self::processWithBody('GET', $newUrlG, array("username" => $this->username, "password" => $this->password), $bodyG);

        if ($this->controller->Http->status == $this->controller->Http->STATUS_CODE_OK) {
            //$response = json_decode($this->controller->Http->content);
            if ($responseG2->WsDeleteMemberResults) {
                if ($responseG2->WsDeleteMemberResults->resultMetadata->success == 'T') {
                    if (isset($responseG2->WsDeleteMemberResults->wsSubjects)) {
                        $response = $responseG2->WsDeleteMemberResults->results[0]->wsSubjects;
                        $stemCNT = sizeof($response);
                        for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++) {
                            $results[] = (array) $response[$loopCNT];
                        }
                    }
                }
            }
        }

        // if (DEBUG_WRITE) {
        //     echo ("<script>console.log('deleteMembership: " . json_encode($newUrlG) . "');</script>"); //browser console
        //     echo ("<script>console.log('deleteMembership: " . json_encode($bodyG) . "');</script>"); //browser console
        //     echo ("<script>console.log('deleteMembership: " . json_encode($responseG2) . "');</script>"); //browser console
        //     echo ("<script>console.log('deleteMembership: " . json_encode($results) . "');</script>"); //browser console
        //     $this->controller->Debug->write(json_encode($newUrlG));
        // }

        // END 20241017 New add member

        return $results;

        
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
        $newGroups = array();
        $this->controller->CacheObject->clear(CACHE_NAME_GROUPER_MERCHANTS);
        if (!$this->controller->CacheObject->exists(CACHE_NAME_GROUPER_MERCHANTS) || $reload) {

            if (DEBUG_WRITE) {
                $this->controller->Debug->write("Start Load DCs");
            }
            ;

            $newMemberships = array();
            $newSubjects = array();
            $appValues = Cache::read(CACHE_NAME_APPLICATION);

            // PART 1: GET LIST OF ALL GROUPS INCLUDING EMPTY ONES
            $newUrlG = $this->grouper_v4_0_9_url . 'groups';

            // if (DEBUG_WRITE) {
            //    echo ("<script>console.log('loadDC: " . json_encode($newUrlG) . "');</script>"); //browser console
            //    $this->controller->Debug->write(json_encode($newUrlG));
            // }

            $bodyG = array(
                'WsRestFindGroupsRequest' => array(
                    "wsQueryFilter" => array(
                        "typeOfGroups" => "group",
                        "queryFilterType" => "FIND_BY_STEM_NAME",
                        "stemName" => $appValues['stem']['path']['dc'],
                        "stemNameScope" => "ALL_IN_SUBTREE",
                        "enabled" => "T"
                    )
                )
            );

            $responseG2 = self::processWithBody('GET', $newUrlG, array("username" => $this->username, "password" => $this->password), $bodyG);
            // End Part 1

            // PART 2: GET ACTIVE MEMBERSHIPS AND SUBJECT INFORMATION
            $newUrl = $this->grouper_v4_0_9_url . 'memberships';

            $body = array(
                'WsRestGetMembershipsRequest' => array(
                    'includeSubjectDetail' => 'T',
                    'scope' => $appValues['stem']['path']['dc'],
                    'stemScope' => 'ALL_IN_SUBTREE',
                    'enabled' => 'T',
                    'subjectAttributeNames' => array('uclauniversityid', 'edupersonprincipalname'),
                    'wsStemLookup' => array('stemName' => $appValues['stem']['path']['dc']),
                )
            );

            $response2 = self::processWithBody('GET', $newUrl, array("username" => $this->username, "password" => $this->password), $body);
            // End Part 2

            // PART 3: Build Searchable Arrays for Formatting Data
            if ($response2 && $response2->WsGetMembershipsResults) {

                if ($response2->WsGetMembershipsResults->wsGroups) {
                    $newGroups = [];
                    $newMemberships = [];
                    $newSubjects = [];

                    foreach ($responseG2->WsFindGroupsResults->groupResults as $groupie) {
                        $newGroups[] = (array) $groupie;
                    }

                    foreach ($response2->WsGetMembershipsResults->wsMemberships as $membershipie) {
                        $newMemberships[] = (array) $membershipie;
                    }
                    foreach ($response2->WsGetMembershipsResults->wsSubjects as $subjectie) {
                        $newSubjects[] = (array) $subjectie;
                    }
                }
            }

            $newGrouperSubject = '';

            // Go through each group and add "Subjects" array
            foreach ($newGroups as $key => $groupie) {
                $tempGroupMembersArray = [];
                $groupName = $groupie['name'];
                $groupie['subjects'] = [];
                $subjectsArray = [];

                // STAGE 1: Find Members of Group
                foreach ($newMemberships as $tempMembership) {
                    // If Member + Group match appears, store it.
                    if ($tempMembership['groupName'] == $groupName) {
                        $tempGroupMembersArray[] = $tempMembership['memberId'];
                    }
                }

                // STAGE 2: Find Subject data for each member
                foreach ($newSubjects as $tempSubject) {
                    // If match found, gather data and push, remove string from $tempGroupMembersArray
                    if (in_array($tempSubject['memberId'], $tempGroupMembersArray)) {
                        // Get first part of email as ucla logonid
                        $email = $tempSubject['attributeValues'][1];
                        $e = explode("@", $email);
                        array_pop($e); #remove last element.
                        $e = implode("@", $e);
                        // Create Subject Object
                        $newGrouperSubject = array(
                            "GrouperSubjects" => array(
                                "resultCode" => $tempSubject['resultCode'],
                                "success" => $tempSubject['success'],
                                "memberId" => $tempSubject['memberId'],
                                "id" => $tempSubject['id'],
                                "name" => $tempSubject['name'],
                                "sourceId" => $tempSubject['sourceId'],
                                "uclauniversityid" => $tempSubject['attributeValues'][0],
                                "uclalogonid" => $e,
                                "edupersonprincipalname" => $tempSubject['attributeValues'][1]
                            )
                        );

                        // Add to subjects array
                        $subjectsArray[] = (array) $newGrouperSubject;

                        //Get Index of $memberId in $tempGroupMembersArray, and remove it
                        unset($tempGroupMembersArray[array_search($tempSubject["memberId"], $tempGroupMembersArray)]);
                        $tempGroupMembersArray = array_values($tempGroupMembersArray);

                        // Check if there's no more members to search for and break accordingly
                        if (sizeOf($tempGroupMembersArray) == 0) {
                            break;
                        }
                    }
                }

                // STAGE 3: Update Subjects property for the current group
                $newGroups[$key]["subjects"] = $subjectsArray;
            }
        } else {
            $newGroups = $this->controller->CacheObject->get(CACHE_NAME_GROUPER_MERCHANTS);
        }


        if (DEBUG_WRITE) {
            $this->controller->Debug->write("End Load DCs");
        }
        ;
        return $newGroups;

        // 20240802 Legacy Call slower performance
        // $merchants = array();

        // 20240808 Just disabling this clear for the time being.
        //$this->controller->CacheObject->clear(CACHE_NAME_GROUPER_MERCHANTS);
        // if (!$this->controller->CacheObject->exists(CACHE_NAME_GROUPER_MERCHANTS) || $reload) {
        //     error_log('loadDCs, Cache not found');
        //     $appValues = Cache::read(CACHE_NAME_APPLICATION);

        //     $this->controller->CacheObject->duration = strtolower($appValues['cache']['grouper']['merchants']);

        //     if (DEBUG_WRITE) {
        //         $this->controller->Debug->write("Start Load DCs");
        //     }
        //     ;
        //     $merchants = self::getGroups($appValues['stem']['path']['dc']);

        //     $groupCNT = sizeof($merchants);


        //     for ($loopCNT = 0; $loopCNT < $groupCNT; $loopCNT++) {
        //         $groupName = $merchants[$loopCNT]['name'];
        //         $merchants[$loopCNT]['subjects'] = $this->controller->Array->convertLikeModel('GrouperSubjects', self::getMembers($groupName));
        //     }

        //     $this->controller->CacheObject->set(CACHE_NAME_GROUPER_MERCHANTS, $merchants);

        //     if (DEBUG_WRITE) {
        //         $this->controller->Debug->write("End Load DCs");
        //     }
        //     ;

        // } else {
        //     $merchants = $this->controller->CacheObject->get(CACHE_NAME_GROUPER_MERCHANTS);
        // }

        // return $merchants;
    }
    public function loadGroups($reload = false)
    {
        $stems = array();

        //$reload = true; // 20241016: Set Reload to true for debugging, comment this line for production
        
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
