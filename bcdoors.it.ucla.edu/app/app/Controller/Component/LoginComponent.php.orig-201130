<?php class LoginComponent extends Component
{
	var $controller = null;
	var $crossReference = array();
    public function initialize(Controller $controller)
    {
        $this->controller = $controller;
        $this->crossReference['280161818'] = '804554016';
    }
	function startup(Controller $controller)
	{

	}
    public function shutdown(Controller $controller)
    {

    }
    public function beforeRender(Controller $controller)
    {

    }
    function setGrouperAccess($id)
    {
        $result = false;
        if (DEBUG_WRITE) {$this->controller->Debug->write("Start Set Grouper Access `{$id}`");};
        $subject = self::_setMerchantAccess($id);
        if (isset($subject['ppid']))
        {
            $result = true;
            //==> Set additional parameters needed
            $this->controller->Session->write('ppid', $subject['ppid']);
            $this->controller->Session->write('merchants', $subject['merchants']);

            $this->controller->Session->write('access_plans', self::_setAccessPlans($subject['merchants']));
        }
        if (DEBUG_WRITE) {$this->controller->Debug->write("End Set Grouper Access `{$id}`");};

        return $result;
    }
    function validate($values)
    {
    	$result = false;

        $data = array();
        $data['uid'] = $this->controller->Session->read('uid');
        if (empty($data['uid']))
        {
        	$data['uid'] = isset($values['SHIBUCLAUNIVERSITYID']) ? trim($values['SHIBUCLAUNIVERSITYID']) : null;
            $this->controller->Audit->user("Login", $data['uid'], null, "{$_SERVER['REMOTE_ADDR']}|{$_SERVER['HTTP_USER_AGENT']}");
        	if (!empty($data['uid']))
        	{
                if (DEBUG_WRITE) {$this->controller->Debug->write("Start Login `{$data['uid']}`");};
        		$data['id'] = $data['uid'];
		    	$data['firstName'] = trim($values['SHIBGIVENNAME']);
		    	$data['lastName'] = trim($values['SHIBSN']);

		    	$this->controller->Credential->set($data);
                $result = self::setGrouperAccess($data['uid']);
                if (DEBUG_WRITE) {$this->controller->Debug->write("End Login `{$data['uid']}`");};

                $this->controller->Audit->user("Login Success", $data['id'], null, http_build_query($data, '', '|'));
        	}
            else
            {
                $this->controller->Audit->user("Login Failed", null, null, "{$_SERVER['REMOTE_ADDR']}|{$_SERVER['HTTP_USER_AGENT']}");
            }
        }
        else
        {
            $result = true;
        }

    	return $result;
    }
    private function _setMerchantAccess($uid)
    {
    	$results = array();
        if (DEBUG_WRITE) {$this->controller->Debug->write("Start Get Grouper Subject `{$uid}`");};
        $subject = $this->controller->GrouperApi->getSubjects(null, $uid);
        if (DEBUG_WRITE) {$this->controller->Debug->write("End Get Grouper Subject `{$uid}`");};
        if (sizeof($subject) > 0)
        {
            $results['ppid'] = trim($subject[0]['id']);
            if (DEBUG_WRITE) {$this->controller->Debug->write("Start Get Grouper Merchant Access `{$subject[0]['id']}`");};
            $results['merchants'] = $this->controller->GrouperApi->getMerchantAccess($subject[0]['id']);
            if (DEBUG_WRITE) {$this->controller->Debug->write("End Get Grouper Merchant Access `{$subject[0]['id']}`");};
        }

        return $results;
    }
    private function _setAccessPlans($merchants)
    {
        $results = array();
        if (DEBUG_WRITE) {$this->controller->Debug->write("Start Set Access Plans Start|" . implode(",", $merchants));};
        $groups = $this->controller->GrouperApi->loadGroups();

        $merchantCNT = sizeof($merchants);
        for ($loopCNT = 0; $loopCNT < $merchantCNT; $loopCNT++)
        {
            $merchantName = $merchants[$loopCNT];
            
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
                        $merchantResults[] = $values[$loopCNT3]['GrouperGroup'];
                    }
                }
            }
            $results = array_merge($results, $merchantResults);
        }
        if (DEBUG_WRITE) {$this->controller->Debug->write("End Set Access Plans Start|" . implode(",", $merchants));};

        return $results;
    }
}
?>