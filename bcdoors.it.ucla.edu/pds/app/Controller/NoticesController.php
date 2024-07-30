<?php
class NoticesController extends AppController
{
    var $name = 'Notices';
    var $uses = array();
    var $components = array();

    public function forbidden()
    {
    	echo "<!--"; print_r($_SERVER); echo "-->";
    }
}
?>