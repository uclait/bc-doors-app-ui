<?php
class DefinitionCacheComponent extends Object 
{
    var $_cacheName = "definitions";
    public function initialize()
    {

    }
    public function startup()
    {

    }
    public function shutdown()
    {

    }
    public function beforeRender()
    {

    }
    public function beforeRedirect()
    {

    }
    function load($cache = true, $reload = false)
    {   
        $data = array();

        if ($reload || !($this->exists()))
        {
            $Definition = ClassRegistry::init("Definition");
            $data = $Definition->retrieve();

            if ($cache)
                Cache::write($this->_cacheName, $data);
        }
        else
        {
            $data = Cache::read($this->_cacheName);
        }

        return $data;		
    }
    function clear()
    {
        $result = false;
        $data = Cache::read($this->_cacheName);
        if (is_array($data))
        {
            Cache::delete($this->_cacheName);
            $result = true;
        }

        return $result;
    }
    function retrieve($id = null, $group = "", $definition = "")
    {
        $result = array();

        $data = $this->load();
        if (!is_null($id) || !empty($group))
        {
            for ($loopCNT = 0; $loopCNT < count($data); $loopCNT++)
            {
                if (!is_null($id))
                {
                    if ($id == $data[$loopCNT]["Definition"]["id"])
                    {
                            $result = $data[$loopCNT]["Definition"];
                            break;
                    }
                }
                else if ($group != "" && $definition != "")
                {
                    if (strtolower($group) == strtolower($data[$loopCNT]["Definition"]["groups"]) &&
                        strtolower($definition) == strtolower($data[$loopCNT]["Definition"]["definition"]))
                    {
                            $result = $data[$loopCNT]["Definition"];
                            break;
                    }
                }
                else if ($group != "")
                {
                    if (strtolower($group) == strtolower($data[$loopCNT]["Definition"]["groups"]))
                    {
                            $result[] = $data[$loopCNT];
                    }
                }
            }
        }
        else
            $result = $data;

        return $result;
    }
    function getId($group = "", $definition = "")
    {
        $data = $this->retrieve(null, $group, $definition);

        return isset($data["id"]) ? $data["id"] : -1;
    }
    function getDefinition($id = null)
    {
        $data = $this->retrieve($id);

        return isset($data["id"]) ? $data["definition"] : "";
    }
    function exists()
    {
        return !(($data = Cache::read($this->_cacheName) === FALSE));
    }
}
?>