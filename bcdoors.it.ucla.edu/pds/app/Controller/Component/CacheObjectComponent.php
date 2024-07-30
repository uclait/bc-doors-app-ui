<?php

class CacheObjectComponent extends Object
{
	var $duration = "short";
	public function __construct()
	{

	}
    public function initialize()
    {

    }
    public function startup()
    {

    }
    public function shutdown()
    {

    }
    public function beforeRender(Controller $controller)
    {

    }
    public function beforeRedirect()
    {

    }
	function get($cacheName)
	{
            $result = null;
            if (!empty($cacheName))
            {
                if ($this->exists($cacheName))
                {
                    $result = Cache::read($cacheName, $this->duration);
                }
            }
            return $result;
	}
	function set($cacheName, $value)
	{
            $result = false;
            if (!empty($cacheName))
            {
                if (!$this->exists($cacheName))
                {
                    Cache::write($cacheName, $value, $this->duration);
                    $result = true;
                }
            }
            return $result;
	}
	function exists($cacheName)
	{
            return !(Cache::read($cacheName, $this->duration) === FALSE);
	}
	function clear($cacheName)
	{
            $result = false;
            if ($this->exists($cacheName))
            {
                Cache::delete($cacheName, $this->duration);
                $result = true;
            }

            return $result;
	}
}
?>