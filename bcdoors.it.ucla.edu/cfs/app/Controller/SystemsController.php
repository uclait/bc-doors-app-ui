<?php
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class SystemsController extends AppController
{
    var $name = 'System';
    var $uses = array();
    var $components = array('Date', 'Directory', 'File', 'GrouperApi', 'GrouperSoapApi');

    public function test()
    {
        $Audit = ClassRegistry::init("Audit");
        $userId = $this->Session->read('id');

        $params = $this->params->query;
        $debug = isset($params['debug']);
        $debug = true;

        if ($debug) {logExecutionTime("Start App"); };

        //==> Display DBASE INI values
        if ($debug) {logExecutionTime("DBASE INI Start");};
        $values = '';
        $cacheValues = Cache::read(CACHE_NAME_DBASE);
        self::recursive($cacheValues, $values);
        $this->set('dbase_ini', $values);
        if ($debug) {logExecutionTime("DBASE INI End");};
        //-------------------------------------
        //==> Display APP INI values
        if ($debug) {logExecutionTime("APP INI Start");};
        $values = '';
        $cacheValues = Cache::read(CACHE_NAME_APPLICATION);
        self::recursive($cacheValues, $values);
        $this->set('app_ini', $values);
        if ($debug) {logExecutionTime("APP INI End");};
        //-------------------------------------
        //==> Test Dbase Connection
        if ($debug) {logExecutionTime("Test Dbase Connection Start");};
        $sql = "SELECT COUNT(ID) FROM AUDITS";
        $values = $Audit->query($sql);
        $values = sizeof($values) > 0 ? $values[0][0] : 0;

        $this->set('dbase_read', $values ? 'success' : 'failed');
        if ($debug) {logExecutionTime("Test Dbase Connection End");};
        //-------------------------------------
        //==> Test Dbase Insert
        if ($debug) {logExecutionTime("Dbase Insert Start");};
        $appId = Configure::read('Application.Id');
        $definitionId = $this->DefinitionCache->getId("System", "System Database Insert Test");

        $sql = "INSERT INTO AUDITS (APP_ID, DEFINITION_ID, USER_ID, ACTION_DATE) VALUES (%s, %s, '%s', '%s')";
        $sql = sprintf($sql, $appId, $definitionId, $userId, date('Y-m-d h:i:s'));

        $values = $Audit->query($sql);
        $this->set('dbase_write', !($values === FALSE) ? 'success' : 'failed');
        if ($debug) {logExecutionTime("Dbase Insert End");};
        //-------------------------------------
        //==> Cached Files
        if ($debug) {logExecutionTime("Cached Files Start");};

        $values = array();
        $folder = new Folder(CACHE);

        $results = $folder->read();
        if (sizeof($results) > 0)
        {
            $results = $results[1];
            $fileCNT = sizeof($results);
            for ($loopCNT = 0; $loopCNT < $fileCNT; $loopCNT++)
            {
                $file = new File(CACHE . $results[$loopCNT]);
                $values[] = array('name' => $results[$loopCNT], 'size' => number_format($file->size(), 2), 'filetime' => date('m/d/Y h:i:s A', $file->lastChange()));
            }
        }
        $this->set('cached_files', $values);
        if ($debug) {logExecutionTime("Cached Files End");};
        //-------------------------------------
        //==> Log Files
        if ($debug) {logExecutionTime("Log Files Start");};

        $values = array();
        $results = array();
        $filter = date("Ymd");
        $startDate = date("Y-m-d H:i:s");
        for ($loopCNT = 0; $loopCNT < 10; $loopCNT++)
        {
            $endDate = $this->Date->add($startDate, 'day', -$loopCNT, 'Ymd');
            $results[] = str_replace($filter, $endDate, DEBUG_FILE);
        }

        if (sizeof($results) > 0)
        {
            $fileCNT = sizeof($results);
            for ($loopCNT = 0; $loopCNT < $fileCNT; $loopCNT++)
            {
                if (file_exists($results[$loopCNT]))
                {
                    $file = new File($results[$loopCNT]);
                    $values[] = array('name' => substr(str_replace(LOG_PATH, "", $results[$loopCNT]), 1), 'size' => number_format($file->size(), 2), 'filetime' => date('m/d/Y h:i:s A', $file->lastChange()));
                }
            }
        }
        $this->set('log_files', $values);
        if ($debug) {logExecutionTime("Log Files End");};
        //-------------------------------------

        $this->set('debug_time', displayExecutionTime(true));
        if ($debug) {logExecutionTime("End App"); };
    }
    public function recursive($array, &$result, $level = 1)
    {
        foreach($array as $key => $value)
        {
            //If $value is an array.

            if(is_array($value))
            {
                $result .= str_repeat("\t", $level) . $key . "\n";
                //We need to loop through it.
                self::recursive($value, $result, $level + 1);
            }
            else
            {
                //It is not an array, so print it out.
                if (strtolower($key) == 'password')
                    $value = str_repeat('*', strlen($value));

                $result .= str_repeat("\t", $level) . $key . ": " . $value . "\n";
            }
        }

    }
}
?>