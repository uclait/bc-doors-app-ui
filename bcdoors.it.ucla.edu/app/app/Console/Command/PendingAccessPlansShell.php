<?php
class PendingAccessPlansShell extends AppShell
{
	//public $uses = array('PendingAccessPlan');
	public $component = array('AccessPlan');
	public function clean_up()
	{
		global $executionTimes;

		//$values = Cache::read(CACHE_NAME_DBASE);
		//print_r($values);
		//exit(1);

		logExecutionTime("==> Start \n");
		App::import('Component', array('LoadConfigs'));
		$params = array(CACHE_NAME_DBASE => INI_PATH . "/dbase.ini", CACHE_NAME_APPLICATION => INI_PATH . "/app.ini");

		$LoadConfigs = new LoadConfigsComponent($params);
		$LoadConfigs->load($params);

		$PendingAccessPlan = ClassRegistry::init("PendingAccessPlan");

		//$this->args[0]
		$sql = "SELECT\n" .
			   "pending_access_plans.ID,\n" .
			   "pending_access_plans.UCLA_UID,\n" .
			   "pending_access_plans.PATH\n" .
			   "FROM\n" .
			   "pending_access_plans,\n" .
			   "grouper_log_mapping\n" .
			   "WHERE\n" .
			   "    grouper_log_mapping.UUID = pending_access_plans.UCLA_UID\n" .
			   "AND grouper_log_mapping.ACCESS_GROUP = pending_access_plans.path\n" .
			   "AND grouper_log_mapping.status = 'completed'\n" .
			   "AND TO_DATE(grouper_log_mapping.updated_date, 'YYYY-MM-DD HH24:MI:SS') >= TO_DATE(pending_access_plans.INSERTED, 'YYYY-MM-DD HH24:MI:SS') \n" . 
			   //"AND grouper_log_mapping.updated_date >= pending_access_plans.INSERTED\n" .
			   "GROUP BY\n" .
			   "pending_access_plans.ID,\n" .
			   "pending_access_plans.UCLA_UID,\n" .
			   "pending_access_plans.PATH\n";

			   //$this->out($sql . "\n");

	    /*
		$sql = "SELECT\n" .
			   "ID,\n" .
			   "UCLA_UID,\n" .
			   "PATH,\n" .
			   "ACTION,\n" .
			   "INSERTED,\n" .
			   "LAST_MODIFIED\n" .
			   "FROM\n" .
			   "pending_access_plans\n";
		*/
        $accessPlan = $PendingAccessPlan->query($sql);
        /*
        $fields = array("ID", "UCLA_UID", "PATH");
        $conditions = array('exists '.
        					'(SELECT 1 FROM GROUPER_LOG_MAPPING '.
        					'WHERE ' .
        					'    GROUPER_LOG_MAPPING.UUID = PendingAccessPlan.UCLA_UID ' .
        					'AND GROUPER_LOG_MAPPING.ACCESS_GROUP = PendingAccessPlan.PATH ' .
        					'AND GROUPER_LOG_MAPPING.updated_date >= PendingAccessPlan.INSERTED ' .
        					'AND GROUPER_LOG_MAPPING.status = \'completed\')');

        $accessPlan = $this->PendingAccessPlan->find('all', array('conditions' => $conditions, 'fields' => $fields));
		*/

        $rowCNT = sizeof($accessPlan);
        $this->out("To Be Deleted: " . number_format($rowCNT, 0)) . "\n\n";

        for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
        {
        	$id = $accessPlan[$loopCNT][0];
        	$uid = $accessPlan[$loopCNT][1];
        	$path = $accessPlan[$loopCNT][2];

        	$PendingAccessPlan->delete($id);

        	$this->out("==> \t" . $uid . ": " . $path . "\n");
        }
        //$this->out(print_r($accessPlan, true));

        logExecutionTime("==> Complete \n");

        $this->out(implode("\n", $executionTimes));

        $this->out("==> Time: " . calculateExecutionSeconds() . "\n");
    }

    public function main()
    {
        $this->out('Hello world.');
    }
}
?>