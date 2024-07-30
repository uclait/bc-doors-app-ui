<?php
class LogsController extends AppController
{
    var $name = 'Logs';
    var $uses = array();
    var $components = array('File');

    public function debug()
    {
        $this->layout = 'empty';

        $content = "";
        $fileName = isset($this->params->params['pass'][0]) ? trim($this->params->params['pass'][0]) : "";
        if (!empty($fileName))
        {
            if (file_exists(LOG_PATH . DS . $fileName))
            {
                $content = "<pre>\n" . $this->File->read(LOG_PATH . DS . $fileName) . "</pre>";
            }
            else
            {
                $content = "<h3 style='color: red; font-weight: bold;'>`{$fileName}` does not exist</h3>";
            }
        }

        $this->set("content", $content);
    }
}
?>