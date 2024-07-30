<?php
App::import('Component', array("DefinitionCache"));
class AuditComponent extends Object 
{
    var $DefinitionCache = null;
    public function initialize()
    {
        $this->DefinitionCache = new DefinitionCacheComponent();
    }
    public function startup()
    {

    }
    public function shutdown()
    {

    }
    public function beforeRender()
    {

    }
    public function beforeRedirect()
    {

    }
    function user($definition = "", $userId = null, $contactId = null, $comment = null, $actionDate = null) 
    {
        $result = false;
        $Audit = ClassRegistry::init("Audit");

        //$Audit->query('set session wait_timeout = 90;');		
        $appId = Configure::read('Application.Id');

        $definitionId = $this->DefinitionCache->getId("User", $definition);

        if ($appId > 0 && $definitionId > 0)
        {
                $user["Audit"]["app_id"] = $appId;
                $user["Audit"]["definition_id"] = $definitionId;
                $user["Audit"]["user_id"] = $userId;
                $user["Audit"]["crm_user_id"] = $contactId;
                $user["Audit"]["comments"] = $comment;
                $user["Audit"]["action_date"] = is_null($actionDate) ? date('Y-m-d h:i:s') : $actionDate;
                
                //==> Oracle changes everything to UPPER CASE
                $user["Audit"] = array_change_key_case($user["Audit"], CASE_UPPER);

                $Audit->id = null;
                $result = $Audit->save($user);
        }

        return $result;
    }
    function add($group, $definition, $userId = null, $contactId = null, $comment = null, $actionDate = null) 
    {
        $result = false;
        $Audit =& ClassRegistry::init("Audit");
        $Schema =& ClassRegistry::init("Schema");

        $appId = Configure::read('Application.Id');
        $definition_id = $this->AuditDefinitions->retrieveId(null, $group, $definition);

        $user = $Schema->retrieve($Audit);
        unset($user["Audit"]["inserted"]);

        if ($appId > 0 && $definition_id > 0)
        {
                $user["Audit"]["app_id"] = $appId;
                $user["Audit"]["definition_id"] = $definition_id;
                $user["Audit"]["user_id"] = $userId;
                $user["Audit"]["crm_user_id"] = $contactId;
                $user["Audit"]["comment"] = $comment;
                $user["Audit"]["action_date"] = is_null($actionDate) ? date('Y-m-d H:i:s') : $actionDate;

                $Audit->id = null;
                $result = $Audit->save($user);
        }
        return $result;
    }
    function setTimeout($seconds = 90) 
    {
        $Audit =& ClassRegistry::init("Audit");

        $temp = $Audit->query('set session wait_timeout = ' . $seconds . ';');
    }
    function pageViews($userId = null, $contactId = null, $remoteAddress, $protocol, $host, $uri, $querySting = "") 
    {
        $result = false;
        $prefix = "AuditPageView";
        $Audit = ClassRegistry::init($prefix);

        $temp = $Audit->query('set session wait_timeout = 60;');		

        $user[$prefix]["user_id"] = $userId;
        $user[$prefix]["crm_user_id"] = $contactId;
        $user[$prefix]["remote_address"] = $remoteAddress;
        $user[$prefix]["protocol"] = $protocol;
        $user[$prefix]["host"] = $host;
        $user[$prefix]["uri"] = $uri;
        $user[$prefix]["query_string"] = $querySting;

        $Audit->id = null;
        $result = $Audit->save($user);

        return $result;
    }
}
?>