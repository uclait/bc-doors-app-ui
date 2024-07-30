<?php
class AuditUser extends AppModel
{
    var $name = 'AuditUser';
    var $useTable = "audits";
    var $useDbConfig = "mysql";

    function retrieve($conditions, $startIndex = 0, $endIndex = 100, $order = array("id desc"))
    {
        $data = array();
        $whereClause = "";

        while (list($key, $value) = each($conditions))
        {
            $whereClause .= "AND {$key} = {$value}";
        }

        $sql = "SELECT " .
                 " * " .
                 "FROM " .
                 "( " .
                 "SELECT " .
                 "  id " .
                 ", definition_id " .
                 ", `group` " .
                 ", definition " .
                 ", user_id " .
                 ", crm_user_id " .
                 ", comment " .
                 ", action_date " .
                 ", inserted " .
                 "FROM vwAuditUser " .
                 "WHERE 1 = 1 " .
                 $whereClause . " " .
                 "ORDER BY {$order[0]} " .
                 "LIMIT {$startIndex}, {$endIndex} " .
                 ") " . $this->name;


        $data = $this->query($sql);

        return $data;
    }
    function count($conditions)
    {
        $data = array();
        $count = 0;
        $whereClause = "";

        while (list($key, $value) = each($conditions))
        {
            $whereClause .= "AND {$key} = {$value}";
        }

        $sql = "SELECT " .
                 " * " .
                 "FROM " .
                 "( " .
                 "SELECT " .
                 "  COUNT(id) AS `count` " .
                 "FROM vwAuditUser " .
                 "WHERE 1 = 1 " .
                 $whereClause . " " .
                 ") " . $this->name;


        $data = $this->query($sql);
        if (isset($data[0]["AuditUser"]))
             $count = $data[0]["AuditUser"]["count"];

        return $count;
    }
}
?>