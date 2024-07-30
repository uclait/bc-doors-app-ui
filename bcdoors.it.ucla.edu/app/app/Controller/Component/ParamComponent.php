<?php
class ParamComponent extends Component
{
    public $params = array();

    public function initialize(Controller $controller)
    {

    }
    function startup(Controller $controller)
    {
        $this->params = $controller->params;
    }
    public function shutdown(Controller $controller)
    {

    }
    public function beforeRender(Controller $controller)
    {

    }
    function form($name, $default = null)
    {
            return self::get($this->params->data, $name, $default);
    }
    function url($name, $default = null)
    {
        return self::get($this->params->query, $name, $default);
    }
    function request($name, $default = null)
    {
        $value = self::get($this->params->data, $name);
        if ($value == "")
                $value = self::get($this->params->query, $name, $default);

        return $value;
    }
    function get($type, $name, $default = null)
    {
        $value = "";
        if (is_array($type))
        {
            if (isset($type[$name]))
                $value = trim($type[$name]);
        }
        else if (isset($this->params[$type]))
        {
            if (isset($this->params[$type][$name]))
                $value = trim($this->params[$type][$name]);
        }
        return $value == "" && !is_null($default) ? $default : $value;
    }
     function extract($data, $searchText)
     {
          $result = array();
          $hasPrefix = !empty($searchText);

          while (list($key, $value) = each($data))
          {
            if ($hasPrefix)
            {
                if (substr($key, 0, strlen($searchText)) == $searchText)
                {
                    $result[substr($key, strlen($searchText))] = trim($value);
                }
            }
            else
                $result[$key] = trim($value);
          }

          return $result;
     }
     function collapse($prefix, $data)
     {
          $result = array();

          if (is_array($data))
          {
               foreach ($data as $key => $value)
               {
                    $result["{$prefix}-{$key}"] = $value;
               }
          }

          return $result;
     }
}
?>