<?php
class SearchController extends AppController 
{
    var $name = 'Search';
    var $uses = array('CardHolder');
    var $components = array('GrouperApi', 'Http', 'String', 'Validate');

    public function json_card_holder()
    {
        set_time_limit(SEARCH_TIMEOUT);
        $this->autoRender = false;

        $params = $this->params->query;
        $debug = isset($params['debug']);

        if ($debug) {logExecutionTime("Start App"); };

        $cacheValues = Cache::read(CACHE_NAME_APPLICATION);
        $prefixes = array("Search");

        $cardHolders = array();
        $errors = array();
        $limit = $cacheValues['search']['card_holder']['max'];

        $reload = isset($params['reload']) && $params['reload'] == 1;
        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");

        if (!isset($form[$prefixes[0]]['name']) && !isset($form[$prefixes[0]]['uid']))
            $errors[] = "Card Holder UCLA UID or Card Holder Name is required";

        if (!isset($form[$prefixes[0]]['type']))
            $form[$prefixes[0]]['type'] = 'card';

        $continue = sizeof($errors) == 0;
        if ($continue)
        {
            $uid = isset($form[$prefixes[0]]['uid']) ? $form[$prefixes[0]]['uid'] : '';
            $name = isset($form[$prefixes[0]]['name']) ? $form[$prefixes[0]]['name'] : '';

            $cache = $cacheValues['cache']['search']['card_holder']['data'] == 1;
            $this->CacheObject->duration = strtolower($cacheValues['cache']['search']['card_holder']['duration']);
            $cacheName = "search." . $form[$prefixes[0]]['type'] . "." . (!empty($uid) ? $uid : $name);
            if ($reload)
                $this->CacheObject->clear($cacheName);

            if ($cache && $this->CacheObject->exists($cacheName))
            {
                if ($debug) {logExecutionTime("Get Cached API Start");};
                $cardHolders = $this->CacheObject->get($cacheName);
                if ($debug) {logExecutionTime("Get Cached API End");};
            }
            else
            {
                if ($debug) {logExecutionTime("Call Search API Start");};
                $api = empty($uid) ? $cacheValues['api']['card_holder_search'] : $cacheValues['api']['card_holder_search_uid'];
                $searchParam = $api['param'];
                $searchValue = !empty($uid) ? $uid : $name;

                $response = $this->Http->get($api['url'], array($searchParam => $searchValue));

                if ($debug) {logExecutionTime("End Search API Start");};
                if ($this->Http->status == $this->Http->STATUS_CODE_OK)
                {
                    $response = $this->Http->content;
                    $xml = $this->Xml->load($response);
                    if ($xml)
                    {
                        if ($debug) {logExecutionTime("Generate Results Start");};
                        $rowCNT = sizeof($xml->CustomerVO);
                        for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
                        {
                            $row = $xml->CustomerVO[$loopCNT];
                            if (strlen($row->CustomerNumber) == 9)
                            {
                                $data = array('value' => (string)$row->FirstName . " " . (string)$row->LastName . " - " . (string)$row->CustomerNumber,
                                              'uid' => (string)$row->CustomerNumber,
                                              'first_name' => (string)$row->FirstName,
                                              'last_name' => (string)$row->LastName,
                                              'type' => $form[$prefixes[0]]['type']);

                                $cardHolders[] = $data;
                            }
                        }
                        if ($cache)
                        {
                            $this->CacheObject->duration = 'medium';
                            $this->CacheObject->set($cacheName, $cardHolders);
                        }

                        if ($debug) {logExecutionTime("Generate Results End");};                            
                    }
                }
            }
            if (sizeof($cardHolders) > $limit)
                $cardHolders = array_slice($cardHolders, 0, $limit);
        }

        if (sizeof($cardHolders) == 0)
            $cardHolders[] = array('value' => '', 'id' => '', 'uid' => '', 'type' => $form[$prefixes[0]]['type']);

        if ($debug) {logExecutionTime("End App"); };

        $this->response->type('json');
        $this->response->body(json_encode($cardHolders));

        if ($debug) {displayExecutionTime();}
    }
    public function json_door_plan() 
    {
        set_time_limit(SEARCH_TIMEOUT);
        $this->autoRender = false;

        $userId = $this->Session->read('id');
        $debug = $this->Param->url("debug", 0) == 1;

        if ($debug) {logExecutionTime("Start App"); };
        $params = $this->params->query;
        $prefixes = array("Search");
        $minimumFields = array();

        $results = array();
        $errors = array();
        $limit = 250;

        $reload = isset($params['reload']) && $params['reload'] == 1;
        $cacheValues = Cache::read(CACHE_NAME_APPLICATION);
        
        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        if (AUDIT_ACTIVITY)
            $this->Audit->user("Find Door Access Plan", $userId, null, http_build_query($form[$prefixes[0]], '', '|'));
        
        $form[$prefixes[0]]['search'] = isset($form[$prefixes[0]]['search']) ? trim(strtolower($form[$prefixes[0]]['search'])): '';
        $form[$prefixes[0]]['type'] = isset($form[$prefixes[0]]['type']) ? strtolower($form[$prefixes[0]]['type']): 'door';
        
        $continue = sizeof($errors) == 0;
        if ($continue)
        {
            $cache = $cacheValues['cache']['search']['door_plan']['data'] == 1;
            $this->CacheObject->duration = strtolower($cacheValues['cache']['search']['door_plan']['duration']);

            $merchants = $this->Session->read('merchants');
            if ($debug) {logExecutionTime("Grouper API Start");};
            $groups = $this->GrouperApi->loadGroups();

            if ($debug) {logExecutionTime("Grouper API End");};
            $merchantCNT = sizeof($merchants);
            for ($loopCNT = 0; $loopCNT < $merchantCNT; $loopCNT++)
            {
                $merchantName = $merchants[$loopCNT];
                $cacheName = "search.{$form[$prefixes[0]]['type']}.{$merchantName}.{$form[$prefixes[0]]['query']}";
                
                if (!$cache || $reload)
                    $this->CacheObject->clear($cacheName);
            
                if ($cache && $this->CacheObject->exists($cacheName))
                {
                    $results = array_merge($results, $this->CacheObject->get($cacheName));
                }
                else
                {
                    if ($debug) {logExecutionTime("Generate Results Start");};

                    $merchantResults = array();
                    $rowCNT = sizeof($groups);
                    for ($loopCNT2 = 0; $loopCNT2 < $rowCNT; $loopCNT2++)
                    {
                        if ($merchantName == $groups[$loopCNT2]['GrouperStem']['extension'])
                        {
                            $values = $groups[$loopCNT2]['GrouperStem']['groups'];
                            $groupCNT = sizeof($values);
                            for ($loopCNT3 = 0; $loopCNT3 < $groupCNT; $loopCNT3++)
                            {
                                $row = $values[$loopCNT3]['GrouperGroup'];
                                if (empty($form[$prefixes[0]]['query']) ||
                                    $this->String->contains($row['description'], $form[$prefixes[0]]['query']) ||
                                    $this->String->contains($row['extension'], $form[$prefixes[0]]['query']))
                                {
                                    $merchantResults[] = array('value' => $row['description'], 'plan_id' => $row['name'], 'type' => $form[$prefixes[0]]['type']);
                                }
                            }
                        }
                    }
                    $results = array_merge($results, $merchantResults);
                    if ($cache)
                    {
                        $this->CacheObject->duration = 'medium';
                        $this->CacheObject->set($cacheName, $merchantResults);
                    }

                    if ($debug) {logExecutionTime("Generate Results End");};
                }
            }
        }

        if (sizeof($results) == 0)
            $results[] = array('value' => '', 'id' => '', 'plan_id' => '', 'type' => $form[$prefixes[0]]['type']);

        if (AUDIT_ACTIVITY)
            $this->Audit->user("Find Door Access Plan Results", $userId, null, http_build_query($form[$prefixes[0]], '', '|') . "|results=" . sizeof($results));
        
        if ($debug) {logExecutionTime("End App"); };
        
        $this->response->type('json');
        $this->response->body(json_encode($results));

        if ($debug) {displayExecutionTime();}
    }
}
?>