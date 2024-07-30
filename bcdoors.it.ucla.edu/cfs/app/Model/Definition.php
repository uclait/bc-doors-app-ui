<?php
class Definition extends AppModel
{
    var $name = 'Definition';

    function retrieve($id = null, $group = "", $definition = "", $index = null, $active = null, $order = array("groups desc, definition"))
    {
        $conditions = array();
        if ($id != null)
            $conditions["id"] = $id;
        if ($group != "")
            $conditions["groups"] = $group;
        if ($definition != "")
            $conditions["definition"] = $definition;
        if ($index != null)
            $conditions["index"] = $index;
        if ($active != null)
            $conditions["active"] = $active;	

        return $this->find("all", array("conditions" => $conditions, "order" => $order)); 
    }
}
?>