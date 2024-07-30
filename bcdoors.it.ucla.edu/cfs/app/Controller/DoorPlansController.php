<?php
class DoorPlansController extends AppController 
{
    var $name = 'Home';
    var $uses = array('PendingAccessPlan');
    var $components = array('AccessPlan', 'Breadcrumbs', 'CardHolderPPID', 'Validate');

    public function index()
    {
        set_time_limit(60);
        $uid = date('YmdHis');

        $appValues = Cache::read(CACHE_NAME_APPLICATION);
        $additionalFiles = array('css' => array('/css/typeahead.css'), 
                                 'js' => array('/js/jquery.blockUI-2.65.min.js',
                                               '/js/search.min.js?uid=' . $uid,
                                               '/js/typeahead.min.js',
                                               '/js/jquery.dataTables.v1.10.4.min.js',
                                               '/js/dataTables.bootstrap.js',
                                               '/js/door-plan.min.js?uid=' . $uid,
                                               '/js/jquery.functions.min.js',
                                               '/js/jquery.blockMessage.min.js'));

        $breadCrumbs = $this->Breadcrumbs->generate();

        $params = $this->params->query;
        $prefixes = array("CardHolderDoorPlan");
        $minimumFields = array(array(array("plan_id", "Door Id")));
        
        $form[$prefixes[0]] = $this->Param->extract($params, "");
        
        $doorPlanId = null;

        $name = "";
        $data = array();
        $pending = array();

        $errors = $this->Validate->fields($form[$prefixes[0]], $minimumFields[0]);
        $continue = sizeof($errors) == 0;
        if ($continue)
        {
            $doorPlanId = $form[$prefixes[0]]['plan_id'];
            $accessPlan = $this->GrouperApi->allowDoorAccess($this->Session->read('merchants'), $doorPlanId);
            $continue = isset($accessPlan['description']);
            if ($continue)
            {
                //=================================================================
                //==> Get any pending access plans
                //=================================================================
                $pending = array();
                $conditions = array("PATH" => $doorPlanId);
                $data = $this->PendingAccessPlan->find("all", array("conditions" => $conditions, 'order' => "id"));

                $rowCNT = sizeof($data);
                for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
                {
                    $row = $data[$loopCNT]['PendingAccessPlan'];
                    $pending[$row['ucla_uid']][] = array("path" => $row['path'], "inserted" => $row['inserted']);
                }
                //-----------------------------------------------------------------
                //=================================================================
                //==> If pending get status
                //=================================================================
                foreach($pending as $uid => $values)
                {
                    $data = $this->AccessPlan->retrieve($uid, $this->Array->extractValues($values, "path"));
                    $path = $values[0]['path'];
                    if (isset($data[$path]))
                    {
                        //==> Make sure completed and that the logged entry has a greater entry date
                        if ($data[$path][$uid]['status'] && 
                            (strtotime($data[$path][$uid]['inserted']) >=  strtotime($values[0]['inserted'])))
                        {
                            $fields = array("UCLA_UID" => $uid, "PATH" => $path);
                            $result = $this->PendingAccessPlan->deleteAll($fields);
                            unset($pending[$uid]);
                        }
                    }

                }
                //-----------------------------------------------------------------
                $name = $accessPlan['description'];
                $breadCrumbs['door'][$name] = '';

                $data = $this->GrouperApi->getMembers($doorPlanId);
            }
        }
        else
        {
 
            $this->set('door_plans', $this->Session->read('access_plans'));
        }

        $this->set('additional_files', $additionalFiles);
        $this->set('errors', $errors);
        $this->set('bread_crumbs', $breadCrumbs);
        
        $this->set('id', $doorPlanId);
        $this->set('plan_id', $doorPlanId);

        $this->set('data', $data);
        $this->set('name', $name);
        $this->set('tab', 'door');
        $this->set('pending', $pending);
    }
}
?>