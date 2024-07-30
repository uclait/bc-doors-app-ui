<?php
class XmlComponent extends Object
{
    var $specialCharacters =  array('<', '>', '&', '\[', ']');
    var $invalidCharacters = array('\f', '');
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
    public function enclose($nodeName, $value, $returnAfter = true, $returnBefore = false)
    {
            $result = "";
            $values = is_array($value) ? $value : array($value);
            $valuesCNT = sizeof($values);

            if (trim($nodeName) != "")
            {
                    for ($loopCNT = 0; $loopCNT < $valuesCNT; $loopCNT++)
                    {
                            $values[$loopCNT] = trim((string)$values[$loopCNT]);
                            $result .= "<{$nodeName}>" . ($returnBefore ? "\n" : "") . ($values[$loopCNT] == "" ? "" : self::encode($values[$loopCNT])) . "</{$nodeName}>" . ($returnAfter ? "\n" : "");
                    }
            }

            return $result;
    }
    public function encode($value, $type = "string")
    {

    $needsEncoding = false;
            $encodeTypes = array("tinyblob", "blob", "mediumblob", "longblob", "string", "tinytext", "text", "mediumtext", "longtext", "char", "varchar", "long varchar");
            if (trim($value) != "")
            {
                    if ($this->containsSpecialChar($value))
                    {
            //htmlentities($text, ENT_COMPAT, $charset, false);
            $value = self::removeInvalid($value);
                            $value = "<![CDATA[" . trim($value) . "]]>";
                    }
            }

            return $value;
    }
    public function containsSpecialChar($value)
    {
            $result = false;
            $matches = array();
            $pattern = '/(' . implode("|", $this->specialCharacters) . ')/i';
            if (!empty($value))
            {
                    preg_match($pattern, $value, $matches);
                    $result = sizeof($matches) > 0;
            }

            return $result;
    }
public function removeInvalid($value)
{
    $matches = array();
    $pattern = '/(' . implode("|", $this->invalidCharacters) . ')/i';
    if (!empty($value))
    {
        $value = preg_replace($pattern, '', $value);
    }

    return $value;
}
    public function serialize($data)
    {
            $html = "";
            $rowCNT = 0;
            $tableKey = "";
            if (is_array($data) && sizeof($data) > 0)
            {
                    if (preg_match('/[a-z]/i', implode(array_keys($data))) == 1)
                            $data = array($data);

                    $rowCNT = sizeof($data);
                    for ($loopCNT = 0; $loopCNT < $rowCNT; $loopCNT++)
                    {
                            $nodeKey = "row";
                            $tableKey = "";
                            if (preg_match('/[a-z]/i', implode(array_keys($data[$loopCNT]))) == 1)
                            {
                                    $tableKey = key($data[$loopCNT]);
                                    if (!is_array($data[$loopCNT][$tableKey]))
                                            $tableKey = "";
                                    else
                                            $nodeKey = $tableKey;

                            }
                            $row = $tableKey == "" ? $data[$loopCNT] : $data[$loopCNT][$tableKey];
                            $html .= "<{$nodeKey} index='{$loopCNT}'>";
                            foreach ($row as $key => $value)
                            {
                                    if (is_array($value) && sizeof($value) == 0)
                                            $value = '';

                                    if (!is_array($value))
                                            $html .= "<{$key}>" . self::encode($value) . "</{$key}>\n";
                            }
                            $html .= "</{$nodeKey}>";
                    }
            }

            return $html;
    }
    function load($value)
    {
        $xml = @simplexml_load_string($value);
        
        if (!$xml)
        {
            $xml = false;
        }

        return $xml;
    }
    function toArray($xml)
    {
        $result = array();

        $json = json_encode($xml);
        $result = json_decode($json, true);

        return $result;
    }
}
?>