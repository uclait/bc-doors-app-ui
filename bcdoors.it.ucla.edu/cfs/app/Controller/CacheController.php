<?php
class CacheController extends AppController
{
	var $name = 'Cache';
	var $uses = array();
	var $components = array('Directory', 'File', 'LoadConfigs');

    function _clearSmarty()
    {
        $cacheRoot = APP . "tmp" . DS . "smarty/";
        $cachePaths = array('cache', 'compile');
        foreach($cachePaths as $config)
        {
            self::_deleteDir($cacheRoot . $config);
        }
    }
    function _clearIni($durations)
    {
        for ($loopCNT2 = 0; $loopCNT2 < sizeof($durations); $loopCNT2++)
        {
            Cache::delete(CACHE_NAME_DBASE, $durations[$loopCNT2]);
            Cache::delete(CACHE_NAME_APPLICATION, $durations[$loopCNT2]);
        }
    }
    function _clearSearches($durations)
    {
        $cacheRoot = APP . "tmp/cache";
        $cachePaths = $this->File->listing($cacheRoot, 'cake_search_');
        $rowCNT = sizeof($cachePaths);
        for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
        {
            $cacheName = str_replace('cake_', '', $cachePaths[$loopCNT]);
            for ($loopCNT2 = 0; $loopCNT2 < sizeof($durations); $loopCNT2++)
            {
                Cache::delete($cacheName, $durations[$loopCNT2]);
            }
        }
    }
    function _clearMerchants($durations)
    {
        for ($loopCNT2 = 0; $loopCNT2 < sizeof($durations); $loopCNT2++)
        {
            Cache::delete(CACHE_NAME_GROUPER_MERCHANTS, $durations[$loopCNT2]);
            Cache::delete(CACHE_NAME_GROUPER_MERCHANT_GROUPS, $durations[$loopCNT2]);
        }
    }
	function xml_clear()
	{
        $this->autoLayout = false;
        $this->autoRender = false;
        $key = $this->Param->request('key', '');

        $durations = array('default', 'short', 'medium', 'long');
        $error = "";

        if (AUDIT_ACTIVITY)
        {
            if (DEBUG_WRITE) {$this->Debug->write("AUDIT-Clear Cache Start|`{$userId}`|`{$key}`");};
            $this->Audit->user("Clear Cache", $userId, null, "{$userId}|{$key}");
            if (DEBUG_WRITE) {$this->Debug->write("AUDIT-Clear Cache End|`{$userId}`|`{$key}`");};
        }

        if (empty($key))
        {
            self::_clearSmarty();
            self::_clearIni($durations);
            self::_clearSearches($durations);
            self::_clearMerchants($durations);

            Cache::Clear();

            $cachePaths = array('views', 'persistent', 'models');
            foreach($cachePaths as $config)
            {
                clearCache(null, $config);
            }
            $cacheRoot = APP . "tmp" . DS . "smarty/";
            $cachePaths = array('cache', 'compile');
            foreach($cachePaths as $config)
            {
                $files = $this->Directory->listing($cacheRoot . $config);
                for ($loopCNT = 0; $loopCNT < sizeof($files); $loopCNT++)
                {
                    if (!$files[$loopCNT]["isDir"])
                    {
                        unlink($files[$loopCNT]["name"]);
                    }
                }
            }
        }
        else
        {
                if ($key == 'smarty')
                {
                    self::_clearSmarty();
                }
                else if ($key == 'ini')
                {
                    self::_clearIni($durations);
                }
                else if ($key == 'search')
                {
                    self::_clearSearches($durations);
                }
                else if ($key == 'merchants')
                {
                    self::_clearMerchants($durations);
                }
                else
                {
                    $cacheRoot = APP . "tmp/cache";
                    $cachePaths = explode(',', $key);

                    for ($loopCNT = 0; $loopCNT < sizeof($cachePaths); $loopCNT++)
                    {
                            if (is_dir($cacheRoot . DS . $cachePaths[$loopCNT]))
                            {
                                    self::_deleteDir($cacheRoot . DS . $cachePaths[$loopCNT]);
                            }
                            else
                            {
                                    for ($loopCNT2 = 0; $loopCNT2 < sizeof($durations); $loopCNT2++)
                                    {
                                            Cache::delete($cachePaths[$loopCNT], $durations[$loopCNT2]);
                                    }
                            }
                    }
                }
        }

        $html = "<result>\n" .
                "<key>" . $this->Xml->encode($key) . "</key>\n" .
                "<errors>" . $error . "</errors>\n" .
                "</result>\n";

        $this->response->type('xml');
        $this->response->body($html);
	}
	function _deleteDir($path)
	{
            $files = $this->Directory->listing($path);
            $rowCNT = sizeof($files);
            for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
            {
                    if (!$files[$loopCNT]["isDir"])
                    {
                            unlink($files[$loopCNT]["name"]);
                    }
            }
	}
	function index()
	{
        $uid = date('YmdHis');
        $additionalFiles = array('css' => array(),
                                 'js' => array('/js/jquery.blockUI-2.65.min.js',
                                               '/js/search.min.js?uid=' . $uid,
                                               '/js/jquery.dataTables.v1.10.4.min.js',
                                               '/js/dataTables.bootstrap.js',
                                               '/js/card-holder.min.js?uid=' . $uid,
                                               '/js/cache.min.js?uid=' . $uid,
                                               '/js/jquery.functions.min.js',
                                               '/js/jquery.blockMessage.min.js'));

        $key = isset($this->params["pass"][0]) ? $this->params["pass"][0] : "";
        $this->set('tab', 'help');
        $this->set('additional_files', $additionalFiles);
	}
	function clear()
	{
            $this->autoRender = false;
            $this->Audit->add("jobs", "Clear Cache", null, null, "start");

            Cache::Clear();
            echo "Cache cleared @ " . date("m/d/Y H:i:s") . "<BR><BR>";

            $this->Audit->add("jobs", "Clear Cache", null, null, "end");
	}
	function afterFilter()
	{
            Configure::write('debug', 0);
	}
}
?>