<?php
class ArrayComponent extends Object 
{
    public function initialize(Controller $controller)
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
    static function last($values)
    {
        return is_array($values) ? end($values) : '';
    }
	static function sort($values, $key = "")
	{
		$result = array();
		if ($key == "")
		{
			sort($values);
			$result = $values;
		}
		else
		{
			$b = array();
			foreach($values as $k=>$v) 
			{
				if (isset($v[$key]))
					$b[$k] = strtolower($v[$key]);
			}
			asort($b);
			foreach($b as $key=>$val) 
			{
				$result[] = $values[$key];
			}
		}

		return $result;
	}
        static function findByKey($array, $key, $value = '')
        {
            $results = array();

            if (is_array($array))
            {
                if (isset($array[$key]) && (empty($value) || $array[$key] == $value))
                    $results[] = $array;

                foreach ($array as $subarray)
                    $results = array_merge($results, self::findByKey($subarray, $key, $value));
            }

            return $results;
        }
        static function extractValues($array, $key, $value = '')
        {
            $results = array();

            if (is_array($array))
            {
                if (isset($array[$key]) && (empty($value) || $array[$key] == $value))
                    $results[] = $array;

                foreach ($array as $subarray)
                {
                    $data = self::findByKey($subarray, $key, $value);
                    if (isset($data[0]))
                        $results[] = $data[0][$key];
                }
            }

            return $results;
        }
	static function trimValues($data)
	{
		foreach ($data as $key => $value)
		{
			$data[$key] = trim($value);
		}

		return $data;
	}
	static function display($values)
	{
		echo "<PRE>";
		print_r($values);
		echo "</PRE>";
	}
	static function convertLikeModel($name, $values)
	{
            $result = array();
            if (is_array($values) && !empty($name))
            {
                $rowCNT = sizeof($values);
                for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
                {
                    if (!isset($result[$loopCNT][$name]))
                        $result[$loopCNT][$name] = array();

                    $result[$loopCNT][$name] = $values[$loopCNT];
                }   
            }
            
            return $result;
	}
}
?>