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
        
        $userId = $this->Session->read('id');
        $params = $this->Param->url("debug", 0) == 1 ? $this->params->query : $this->params->data;
        $prefixes = array("GrouperMembership");
        $minimumFields = array(array(array("group_name", "Group Name"), array("identifier", "Identifier")));
        
        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        if (AUDIT_ACTIVITY)
            $this->Audit->user("Add Door Access Plan", $userId, null, http_build_query($form[$prefixes[0]], '', '|'));
        
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
                    $results[] = array_merge($this->GrouperApi->addMembership($form[$prefixes[0]]['group_name'][$loopCNT], $form[$prefixes[0]]['identifier'][$loopCNT2]), array('uid' => $form[$prefixes[0]]['identifier'][$loopCNT2]));   
                    self::_addToPending($form[$prefixes[0]]['identifier'][$loopCNT2], $form[$prefixes[0]]['group_name'][$loopCNT], "A");
               }
            }
            $html = $this->Xml->serialize($results);
        }
        
        if (AUDIT_ACTIVITY)
        {
            if (sizeof($errors) > 0)
                $this->Audit->user("Add Door Access Plan Failed", $userId, null, join(",", $errors));
            else
                $this->Audit->user("Add Door Access Plan Success", $userId, null, $html);
        }
        
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
        
        $userId = $this->Session->read('id');
        $params = $this->Param->url("debug", 0) == 1 ? $this->params->query : $this->params->data;
        $prefixes = array("GrouperMembership");
        $minimumFields = array(array(array("group_name", "Group Name"), array("identifier", "Identifier")));
        
        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        if (AUDIT_ACTIVITY)
            $this->Audit->user("Delete Door Access Plan", $userId, null, http_build_query($form[$prefixes[0]], '', '|'));

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
                    $results[] = array_merge($this->GrouperApi->deleteMembership($form[$prefixes[0]]['group_name'][$loopCNT], $form[$prefixes[0]]['identifier'][$loopCNT2]), array('uid' => $form[$prefixes[0]]['identifier'][$loopCNT2]));
                    self::_addToPending($form[$prefixes[0]]['identifier'][$loopCNT2], $form[$prefixes[0]]['group_name'][$loopCNT], "D");
               }
            }
            $html = $this->Xml->serialize($results);
        }
        
        if (AUDIT_ACTIVITY)
        {
            if (sizeof($errors) > 0)
                $this->Audit->user("Delete Door Access Plan Failed", $userId, null, join(",", $errors));
            else
                $this->Audit->user("Delete Door Access Plan Success", $userId);
        }
        
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
        $fields = array("UCLA_UID" => $uid, "PATH" => $path);
        $result = $this->PendingAccessPlan->find("first", array("conditions" => $fields));
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
        $result = $this->PendingAccessPlan->save(array("PendingAccessPlan" => $fields));
    }
}
?>