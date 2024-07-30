<?php
class Schema extends AppModel
{
    var $name = 'Schema';
    var $useTable = false;

    function retrieve($model)
    {
        $data = array();
        if (isset($model->name))
        {
            $data[$model->name] = array();
            if (isset($model->_schema))
            {
                foreach ($model->_schema as $key => $value)
                {
                    $default = null;
                    switch ($value['type'])
                    {
                            case "string":
                            case "text":
                            case "datetime":
                            case "timestamp":
                            case "date":
                                    $default = "";
                                    break;
                    }
                    $data[$model->name][$key] = $default;
                }
            }
        }
        return $data;
    }
}
?>