<?php
class DirectoryComponent extends Object
{
	private $fileList = array();
	private $dirCNT;
	private $fileCNT;
	private $objectCNT;

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
	function listing($source, $includeFiles = true)
	{
		$this->fileList = array();
		if (file_exists($source))
		{
			$dir_handle = @opendir($source);

			$this->_listDir($dir_handle, $source);
		}
		return($this->fileList);
	}
	function _listDir($dir_handle, $source)
	{
		//running the while loop
		while (false !== ($file = readdir($dir_handle)))
	   	{
			$dir = $source . '/' . $file;
			if ($file != '.' && $file !='..')
			{
				$this->objectCNT++;
				if(is_dir($dir))
				{
					$this->dirCNT++;
					$handle = @opendir($dir) or die("unable to open file $file");

					$this->fileList[count($this->fileList)] = array('isDir'=>true, 'name'=>$dir);
					$this->_listDir($handle, $dir);
				}
				else
				{
					$this->fileCNT++;
					$this->fileList[count($this->fileList)] = array('isDir'=>false, 'name'=>$dir);
				}
			}
		}
		closedir($dir_handle);
	}
	function create($path, $settings)
	{
		App::import('Core', 'Folder');
		$result = false;

		if (!file_exists($path))
		{
			$folder = &new Folder();
			$folder->create($path);
			if (!empty($settings))
				$folder->chmod($path, $settings);

			$result = file_exists($path);
		}

		return $result;
	}
	function delete($path)
	{
		App::import('Core', 'Folder');
		$result = false;

		if (file_exists($path))
		{
			$folder = &new Folder($path);
			$folder->delete($path);
			$result = !file_exists($path);
		}

		return $result;
	}
	function move($source, $destination)
	{
		App::import('Core', 'Folder');
		$result = false;

		if (file_exists($source))
		{
			$folder = &new Folder($source);
			$folder->move($destination, $source);
			$result = !file_exists($destination);
		}

		return $result;
	}
}

?>