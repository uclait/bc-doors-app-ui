<?php
class HomeController extends AppController 
{
    var $name = 'Home';
    var $uses = array();
    var $components = array('Breadcrumbs');
    public function index()
    {
        $uid = date('YmdHis');
        $additionalFiles = array('css' => array('/css/typeahead.css'),
                                 'js' => array(
                                               '/js/jquery.blockUI-2.65.min.js',
                                               #'/js/angular.min.js',
                                               #'/js/angular-route.min.js',
                                               '/js/typeahead.min.js',
                                               '//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js',
                                               '//cdn.datatables.net/plug-ins/9dcbecd42ad/integration/bootstrap/3/dataTables.bootstrap.js',
                                               '/js/search.min.js?uid=' . $uid,
                                               '/js/jquery.functions.min.js',
                                               '/js/jquery.blockMessage.min.js'));

        $breadCrumbs = $this->Breadcrumbs->generate();
        $this->set('door_plans', $this->Session->read('access_plans'));
        
        $tab = $this->Param->request('tab', 'card');
        
        $this->set('additional_files', $additionalFiles);
        $this->set('bread_crumbs', $breadCrumbs);
        $this->set('tab', $tab);
    }
}
?>