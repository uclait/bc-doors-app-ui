<?php
   class StringComponent extends Object 
   {
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
	static function beginsWith($value, $prefix, $anyCase = true)
	{
            if ($anyCase)
            {
                $value = strtolower($value);
                $prefix = strtolower($prefix);
            }
            
            return strlen($value) >= strlen($prefix) ? substr($value, 0, strlen($prefix)) == $prefix : false;
	}
	static function endsWith($value, $suffix, $anyCase = true)
	{
            if ($anyCase)
            {
                $value = strtolower($value);
                $suffix = strtolower($suffix);
            }
            
            return strlen($value) >= strlen($suffix) ? substr($value, -strlen($suffix)) == $suffix : false;
	}
	function contains($haystack, $needle, $anyCase = true)
	{
            $result = false;
            $needle = is_array($needle) ? $needle : array($needle);
            if ($haystack != "" && sizeof($needle) > 0)
            {
                for ($loopCNT = 0; $loopCNT < sizeof($needle); $loopCNT++)
                {
                        $pos = stripos($haystack, $needle[$loopCNT]);
                        $result = !($pos === FALSE);
                        if ($result)
                                break;
                }
            }
            
            return $result;
	}
   }
?>