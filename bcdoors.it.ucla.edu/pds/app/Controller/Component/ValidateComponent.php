<?php
   class ValidateComponent extends Object
   {
        /**
        Validate an email address.
        Provide email address (raw input)
        Returns true if the email address has the email
        address format and the domain exists.
        */
        var $components = array('Date');
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
        function password($value, $min = 3, $max = 25)
        {
            $result = true;
            if (!(strrpos($value, " ") === FALSE))
                $result = false;
            else
                $result = preg_match('/^([a-zA-Z0-9-_~`!@#\$%^&*()\=\'";:\/\?\.>,<|\{\}\[\\]+]){' . $min . ',' . $max . '}$/', $value) > 0;

            return $result;
        }
        function numeric($value)
        {
            return preg_match('/^\d+$/', $value) > 0;
        }
        function float($value)
        {
            return preg_match('/^[0-9]+(.[0-9]+)?$/', $value) > 0;
        }
        function domain($value)
        {
            return preg_match('/^(?:[a-zA-Z0-9]+(?:\_*\-*[a-zA-Z0-9])*\.)+[a-zA-Z]{2,6}$/', $value) > 0;
        }
        function emailAddress($value)
        {
           $isValid = true;
           $atIndex = strrpos($value, "@");
           if (is_bool($atIndex) && !$atIndex)
           {
              $isValid = false;
           }
           else
           {
              $domain = substr($value, $atIndex+1);
              $local = substr($value, 0, $atIndex);
              $localLen = strlen($local);
              $domainLen = strlen($domain);
              if ($localLen < 1 || $localLen > 64)
              {
                 // local part length exceeded
                 $isValid = false;
              }
              else if ($domainLen < 1 || $domainLen > 255)
              {
                 // domain part length exceeded
                 $isValid = false;
              }
              else if ($local[0] == '.' || $local[$localLen-1] == '.')
              {
                 // local part starts or ends with '.'
                 $isValid = false;
              }
              else if (preg_match('/\\.\\./', $local))
              {
                 // local part has two consecutive dots
                 $isValid = false;
              }
              else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
              {
                 // character not valid in domain part
                 $isValid = false;
              }
              else if (preg_match('/\\.\\./', $domain))
              {
                 // domain part has two consecutive dots
                 $isValid = false;
              }
              else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                         str_replace("\\\\","",$local)))
              {
                 // character not valid in local part unless
                 // local part is quoted
                 if (!preg_match('/^"(\\\\"|[^"])+"$/',
                     str_replace("\\\\","",$local)))
                 {
                    $isValid = false;
                 }
              }
              if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
              {
                 // domain not found in DNS
                 $isValid = false;
              }
           }
           return $isValid;
        }
        function fields($data, $dataset)
        {
            $errors = array();
            $setCNT = sizeof($dataset);
            $field = "";
            $dataType = "string";

            if ($setCNT > 0)
            {
                for ($loopCNT = 0; $loopCNT < $setCNT; $loopCNT++)
                {
                    $field = $dataset[$loopCNT][0];
                    if (!isset($data[$field]))
                        $data[$field] = "";

                    if (is_null($data[$field]) || $data[$field] == '')
                        $errors[] = "`{$dataset[$loopCNT][1]}` is required";
                    else
                    {
                        $dataType = isset($dataset[$loopCNT][2]) ? strtolower($dataset[$loopCNT][2]) : "string";
                        switch ($dataType)
                        {
                            case "date":
                                if (!($this->Date->validate($data[$field])))
                                    $errors[] = "`{$dataset[$loopCNT][1]}` is invalid";

                                break;
                            case "domain":
                                if (!(self::domain($data[$field])))
                                    $errors[] = "`{$dataset[$loopCNT][1]}` is invalid";
                                break;
                            case "email":
                                if (!(self::emailAddress($data[$field])))
                                    $errors[] = "`{$dataset[$loopCNT][1]}` is invalid";
                                break;
                            case "float":
                                if (!(self::float($data[$field])))
                                    $errors[] = "`{$dataset[$loopCNT][1]}` is invalid";
                                break;
                            case "numeric":
                                if (!(self::numeric($data[$field])))
                                    $errors[] = "`{$dataset[$loopCNT][1]}` is invalid";
                                break;
                        }
                    }
                }
            }

            return $errors;
        }
   }
?>