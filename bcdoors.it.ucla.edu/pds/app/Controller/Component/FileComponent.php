<?php
class FileComponent extends Object 
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
	static function write($fileName, $text, $mode)
	{
		$result = false;
	
		if (is_writable($fileName) || !file_exists($fileName)) 
		{   
			if ($handle = fopen($fileName, $mode))
			{ 
				if (!(fwrite($handle, $text) === FALSE)) 
				{ 
					$result = true;
					
				}
				fclose($handle);
			}
		}
		return $result;
	}
	static function read($fileName)
	{
	   $contents = "";
	
	   try
	   {
		if (file_exists($fileName) && !is_dir($fileName))
		{
			$handle = fopen($fileName, "r");
			$contents = fread($handle, filesize($fileName));
			fclose($handle);
		}
	   }
	   catch (Exception $Error)
	   {
		   echo ("<!-- error: " . $Error . " -->");
	   }
	
	   return($contents);
	   
	}
	static function readDelimited($fileName, $delimiter = ",", $len = 1000, $implodeWith = "~~")
	{
	   $result = array();

	   try
	   {
		if (file_exists($fileName) && !is_dir($fileName))
		{
			$row = 0;
			$result = array();
			$handle = fopen($fileName, "r");
			while($data = fgetcsv($handle, $len, $delimiter)) 
			{
				if (count($data) > 0)
				{
					$result[$row] = ($implodeWith == "") ? $data : implode($implodeWith, $data);
					$row++;
				}
		      }
			fclose($handle);
		}
	   }
	   catch (Exception $Error)
	   {
		   echo ("<!-- error: " . $Error . " -->");
	   }

	   return($result);
	   
	}
	static function copy($source, $destination)
	{
	   $Result = false;
	
	   if (file_exists($source))
	   {
		$Result = copy($source, $destination);
	   }
	
	   return $Result;
	}
	static function listing($path, $pattern)
	{
	   $pattern = ($pattern == null || $pattern == "")? "^" : $pattern;
	   $files = array();
	   $ImageCNT = 0;
	   if (file_exists($path))
	   {
		if($handle = opendir($path)) 
		{
	       while(false !== ($File = readdir($handle)))
		   {
				if(eregi($pattern, $File))			
				{
					if ($File != "." && $File != "..")
					{
						$files[$ImageCNT] = $File;
						$ImageCNT++;
					}
				}
	       }
	
	       closedir($handle);
		}
	   }
	
	   return($files);
	}
	static function delete($fileName)
	{
	   $result = false;

	   try
	   {
		if (file_exists($fileName) && !is_dir($fileName))
		{
			$result = unlink($fileName);
		}
	   }
	   catch (Exception $Error)
	   {
		   echo ("<!-- error: " . $Error . " -->");
	   }

	   return($result);
	   
	}
}

?>