<?php
class DebugComponent extends Object 
{
    public function initialize(Controller $controller)
    {
        $this->controller =& $controller;
    }
	function startup(Controller $controller)
	{
        $this->controller =& $controller;
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
	public function write($text)
	{
		$result = false;

		$values = array(REQUEST_UID, date("Y-m-d"), showTime(), $text);
	
		$this->controller->File->write(DEBUG_FILE, implode("\t", $values) . "\n", 'a');
	}
}

?>