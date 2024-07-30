<?php
class BreadcrumbsComponent extends Object
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
    public function beforeRender()
    {

    }
    public function beforeRedirect()
    {

    }
    public function generate()
    {
        $breadCrumbs = array('card' => array('Home' => BASE_URL . '/home', 'Card Holders' => BASE_URL . '/home'),
                             'door' => array('Home' => BASE_URL . '/home', 'Access Plans' => BASE_URL . '/home?tab=door'));
        
        return $breadCrumbs;
    }
}
?>