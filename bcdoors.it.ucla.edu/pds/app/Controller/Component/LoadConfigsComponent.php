<?php
App::import('Component', 'CacheObject');
class LoadConfigsComponent extends Object 
{
    private $CacheObject = null;
    public function __construct()
    {
        if (is_null($this->CacheObject))
            $this->CacheObject = new CacheObjectComponent();
    }
    public function initialize(Controller $controller)
    {
        if (is_null($this->CacheObject))
            $this->CacheObject = new CacheObjectComponent();
        
    }
    public function startup(Controller $controller)
    {

    }
    public function shutdown(Controller $controller)
    {

    }
    public function beforeRender(Controller $controller)
    {

    }
    public function load($params, $reload = false)
    {
        if (sizeof($params) > 0)
        {
            foreach ($params as $cacheName => $filePath)
            {
                if ($reload)
                    $this->CacheObject->clear($cacheName);
                
                if (!$this->CacheObject->exists($cacheName))
                {
                     if (file_exists($filePath))
                     {
                         $iniSettings = self::parseValues(parse_ini_file($filePath));
                         $this->CacheObject->set($cacheName, $iniSettings);
                     }
                }
            } 
        }
    }
    function parseValues($values) 
    {
        foreach ($values as $key => $value)
        {
            if ($value === '1') 
            {
                $value = true;
            }
            if ($value === '') {
                $value = false;
            }
            unset($values[$key]);
            if (strpos($key, '.') !== false) {
                $values = Hash::insert($values, $key, $value);
            } else {
                $values[$key] = $value;
            }
        }
        return $values;
    }
}
?>