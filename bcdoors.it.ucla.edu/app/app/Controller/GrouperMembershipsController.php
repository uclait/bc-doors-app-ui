<?php
class GrouperMembershipsController extends AppController 
{
    var $name = 'GrouperMemberships';
    var $uses = array('PendingAccessPlan');
    var $components = array('GrouperApi', 'Http', 'Validate');
    
    public function index()
    {
        $this->autoRender = false;
    }
    public function xml_add()
    {
        set_time_limit(GROUPER_TIMEOUT);
        
        $this->autoRender = false;
        $this->autoLayout = false;
        
        $userId = $this->Session->read('uid');
        $params = $this->Param->url("debug", 0) == 1 ? $this->params->query : $this->params->data;

        if (DEBUG_WRITE) {$this->Debug->write("Start App");};

        $prefixes = array("GrouperMembership");
        $minimumFields = array(array(array("group_name", "Group Name"), array("identifier", "Identifier")));
        
        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        if (AUDIT_ACTIVITY)
        {
            if (DEBUG_WRITE) {$this->Debug->write("AUDIT-Add Door Access Plan Start|" . http_build_query($form[$prefixes[0]], '', '|'));};
            $this->Audit->user("Add Door Access Plan", $userId, null, http_build_query($form[$prefixes[0]], '', '|'));
            if (DEBUG_WRITE) {$this->Debug->write("AUDIT-Add Door Access Plan End");};
        }
        

        $html = "";
        $errors = array();
        $continue = false;
        $redirect = '';
        $results = array();

        $errors = $this->Validate->fields($form[$prefixes[0]], $minimumFields[0]);
        $continue = sizeof($errors) == 0;

        if ($continue)
        {
            $form[$prefixes[0]]['group_name'] = explode('|', $form[$prefixes[0]]['group_name']);
            $form[$prefixes[0]]['identifier'] = explode('|', $form[$prefixes[0]]['identifier']);
            $groupCNT = sizeof($form[$prefixes[0]]['group_name']);
            $idCNT = sizeof($form[$prefixes[0]]['identifier']);
            
            for ($loopCNT = 0; $loopCNT < $groupCNT; $loopCNT++)
            {
               for ($loopCNT2 = 0; $loopCNT2 < $idCNT; $loopCNT2++)
               {
                    if (DEBUG_WRITE) {$this->Debug->write("Start Grouper Add Membership `{$form[$prefixes[0]]['identifier'][$loopCNT2]}` - `{$form[$prefixes[0]]['group_name'][$loopCNT]}`");};
                    $results[] = array_merge($this->GrouperApi->addMembership($form[$prefixes[0]]['group_name'][$loopCNT], $form[$prefixes[0]]['identifier'][$loopCNT2]), array('uid' => $form[$prefixes[0]]['identifier'][$loopCNT2]));
                    if (DEBUG_WRITE) {$this->Debug->write("End Grouper Add Membership `{$form[$prefixes[0]]['identifier'][$loopCNT2]}` - `{$form[$prefixes[0]]['group_name'][$loopCNT]}`");};
                    self::_addToPending($form[$prefixes[0]]['identifier'][$loopCNT2], $form[$prefixes[0]]['group_name'][$loopCNT], "A");
               }
            }
            $html = $this->Xml->serialize($results);
        }
        if (AUDIT_ACTIVITY)
        {
            if (sizeof($errors) > 0)
            {
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT-Add Door Access Plan Failed Start|errors=" . sizeof($errors));};
                $this->Audit->user("Add Door Access Plan Failed", $userId, null, join(",", $errors));
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT-Add Door Access Plan Failed End");};
            }
            else
            {
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT-Add Door Access Plan Success Start");};
                $this->Audit->user("Add Door Access Plan Success", $userId, null, $html);
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT-Add Door Access Plan Success End");};
            }
        }

        if (DEBUG_WRITE) {$this->Debug->write("End App");};
        
        $html = "<response>" . "\n" .
                "<member>" . $html . "</member>" . "\n" .
                "<redirect>" . $this->Xml->encode($redirect) . "</redirect>" . "\n" .
                "<errors>" . $this->Xml->enclose("error", $errors) . "</errors>" . "\n" .
                "</response>";
        
        $this->response->type('xml');
        $this->response->body($html);
    }
    public function xml_delete()
    {
        set_time_limit(GROUPER_TIMEOUT);
        
        $this->autoRender = false;
        $this->autoLayout = false;
        
        $userId = $this->Session->read('uid');
        $params = $this->Param->url("debug", 0) == 1 ? $this->params->query : $this->params->data;

        if (DEBUG_WRITE) {$this->Debug->write("Start App");};

        $prefixes = array("GrouperMembership");
        $minimumFields = array(array(array("group_name", "Group Name"), array("identifier", "Identifier")));
        
        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        if (AUDIT_ACTIVITY)
        {
            if (DEBUG_WRITE) {$this->Debug->write("AUDIT-Delete Door Access Plan Start|" . http_build_query($form[$prefixes[0]], '', '|'));};
            $this->Audit->user("Delete Door Access Plan", $userId, null, http_build_query($form[$prefixes[0]], '', '|'));
            if (DEBUG_WRITE) {$this->Debug->write("AUDIT-Delete Door Access Plan End|" . http_build_query($form[$prefixes[0]], '', '|'));};
        }

        $html = "";
        $errors = array();
        $continue = false;
        $redirect = '';
        $results = array();

        $errors = $this->Validate->fields($form[$prefixes[0]], $minimumFields[0]);
        $continue = sizeof($errors) == 0;

        if ($continue)
        {
            $form[$prefixes[0]]['group_name'] = explode('|', $form[$prefixes[0]]['group_name']);
            $form[$prefixes[0]]['identifier'] = explode('|', $form[$prefixes[0]]['identifier']);
            $groupCNT = sizeof($form[$prefixes[0]]['group_name']);
            $idCNT = sizeof($form[$prefixes[0]]['identifier']);
            for ($loopCNT = 0; $loopCNT < $groupCNT; $loopCNT++)
            {
               for ($loopCNT2 = 0; $loopCNT2 < $idCNT; $loopCNT2++)
               {
                    if (DEBUG_WRITE) {$this->Debug->write("Start Grouper Delete Membership `{$form[$prefixes[0]]['identifier'][$loopCNT2]}` - `{$form[$prefixes[0]]['group_name'][$loopCNT]}`");};
                    $results[] = array_merge($this->GrouperApi->deleteMembership($form[$prefixes[0]]['group_name'][$loopCNT], $form[$prefixes[0]]['identifier'][$loopCNT2]), array('uid' => $form[$prefixes[0]]['identifier'][$loopCNT2]));
                    if (DEBUG_WRITE) {$this->Debug->write("End Grouper Delete Membership `{$form[$prefixes[0]]['identifier'][$loopCNT2]}` - `{$form[$prefixes[0]]['group_name'][$loopCNT]}`");};
                    self::_addToPending($form[$prefixes[0]]['identifier'][$loopCNT2], $form[$prefixes[0]]['group_name'][$loopCNT], "D");
               }
            }
            $html = $this->Xml->serialize($results);
        }
        
        if (AUDIT_ACTIVITY)
        {
            if (sizeof($errors) > 0)
            {
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT-Delete Door Access Plan Failed Start|errors=" . sizeof($errors));};
                $this->Audit->user("Delete Door Access Plan Failed", $userId, null, join(",", $errors));
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT-Delete Door Access Plan Failed End");};
            }
            else
            {
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT-Delete Door Access Plan Success Start");};
                $this->Audit->user("Delete Door Access Plan Success", $userId);
                if (DEBUG_WRITE) {$this->Debug->write("AUDIT-Delete Door Access Plan Success End");};
            }
        }

        if (DEBUG_WRITE) {$this->Debug->write("End App");};
        
        $html = "<response>" . "\n" .
                "<member>" . $html . "</member>" . "\n" .
                "<redirect>" . $this->Xml->encode($redirect) . "</redirect>" . "\n" .
                "<errors>" . $this->Xml->enclose("error", $errors) . "</errors>" . "\n" .
                "</response>";
        
        $this->response->type('xml');
        $this->response->body($html);
    }
    function _addToPending($uid, $path, $action)
    {
        if (DEBUG_WRITE) {$this->Debug->write("Start Add To Pending Lookup `{$uid}` - `{$path}` - `{$action}`");};
        $fields = array("UCLA_UID" => $uid, "PATH" => $path);
        $result = $this->PendingAccessPlan->find("first", array("conditions" => $fields));
        if (DEBUG_WRITE) {$this->Debug->write("End Add To Pending  Lookup `{$uid}` - `{$path}` - `{$action}`");};
        if (isset($result['PendingAccessPlan']))
        {
            $this->PendingAccessPlan->id = $result['PendingAccessPlan']['id'];
            $fields = array_merge($fields, array("ACTION" => $action));
            //$result = $this->PendingAccessPlan->deleteAll($fields);
        }
        else
        {
            $this->PendingAccessPlan->id = null;
            $fields = array_merge($fields, array("ACTION" => $action));
        }
        if (DEBUG_WRITE) {$this->Debug->write("Start Add To Pending `{$uid}` - `{$path}` - `{$action}`");};
        $result = $this->PendingAccessPlan->save(array("PendingAccessPlan" => $fields));
        if (DEBUG_WRITE) {$this->Debug->write("End Add To Pending `{$uid}` - `{$path}` - `{$action}`");};
    }
    public function sync()
    {
        set_time_limit(0);
        
        $this->autoRender = false;
        $this->autoLayout = false;
        $debug = true;

        $fileName = 'grouper_load.txt';

        //$this->response->download($fileName);
        
        $cacheValues = Cache::read(CACHE_NAME_GROUPER_MERCHANT_GROUPS);

        //pr($cacheValues);
        //exit(1);

        $totals = array("stem_count" => 0, "groups" => array());

        if ($debug) echo "<pre>\n";
        $stemCNT = sizeof($cacheValues);
        $totals["stem_count"] = $stemCNT;
        for ($stemLoopCNT = 0; $stemLoopCNT < $stemCNT; $stemLoopCNT++)
        {
            $stemName = $cacheValues[$stemLoopCNT]['GrouperStem']['name'];
            if ($debug) echo $stemName . "\n";

            $groups = $cacheValues[$stemLoopCNT]['GrouperStem']['groups'];
            $groupCNT = sizeof($groups);

            $totals["groups"][$stemName] = array("group_cnt" => $groupCNT, "member_cnt" => 0);
            for ($groupLoopCNT = 0; $groupLoopCNT < $groupCNT; $groupLoopCNT++)
            {
                $groupRow = $cacheValues[$stemLoopCNT]['GrouperStem']['groups'][$groupLoopCNT]['GrouperGroup'];
                $groupPath = $groupRow['name'];
                $groupDescription = $groupRow['description'];
                if ($debug) echo "\t" . $groupDescription . " - " . $groupPath . "\n";

                $members = $this->GrouperApi->getMembers($groupPath);
                $memberCNT = sizeof($members);
                $totals["groups"][$stemName]["member_cnt"] = $memberCNT;
                if ($debug) echo "\t" .  " cnt: " . $memberCNT . "\n";
                for ($memberLoopCNT = 0; $memberLoopCNT < $memberCNT; $memberLoopCNT++)
                {
                    $uid = $members[$memberLoopCNT]['uclauniversityid'];
                    echo "{$uid}|[A,{$groupDescription}]\r\n";
                }
            }

            if ($debug) echo "<hr>\n";
        }
        $totalGroups = 0;
        $totalMembers = 0;
        foreach ($totals["groups"] as $key => $value)
        {
            $totalGroups += $value["group_cnt"];
            $totalMembers += $value["member_cnt"];
        }
        //if ($debug) pr($totals);
        if ($debug)
        {
            echo "Groups: " . $totalGroups . "\n";
            echo "Members: " . $totalMembers . "\n";
        }

        if ($debug) echo "</pre>\n";

        //pr($cacheValues);
    }
}
?>