<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{//
    public $components = array('Array', 'Audit', 'CacheObject', 'Credential', 'Debug', 'DefinitionCache', 'GrouperApi', 'File', 'Http', 'Login', 'Param', 'Session', 'String', 'Url', 'Xml');
    public $viewClass = 'SmartyView.Smarty';
    public $helpers = array(
        'SmartyView.SmartyHtml',
        'SmartyView.SmartyForm',
        'SmartyView.SmartySession',
        'SmartyView.SmartyJavascript',
        'Html',
        'Session'
    );

    public $pageTitle = 'BruinCard';
    public $isAdmin = false;
    public $alreadyLogged = false;
    public $dontValidateControllers = array("deposits", "general", "logins", "notices", "photos");
    public $blockIps = array("86.80.234.154");
    public $remoteAddress = null;
    public $shibbolithParams = array('SHIBUCLAUNIVERSITYID', 'SHIBSN', 'SHIBGIVENNAME');

    public function beforeFilter()
    {
        $this->set('title_for_layout', $this->pageTitle);
        $this->remoteAddress = $_SERVER["REMOTE_ADDR"];
        $admin = Configure::read('Routing.admin');

        if (isset($this->params[$admin]) && $this->params[$admin]) {
            ini_set('session.gc_maxlifetime', 60 * (60 * 2));
            $this->isAdmin = true;
        }
        $this->_shouldIPBeDenied();
        $this->_logPageView();

        if (isset($this->request->url) && empty($this->request->url)) {

            //$this->redirect('/index.html');
            //exit(1);
        }

        Configure::write('Application.Id', $this->DefinitionCache->getId("Application", "BruinCard"));

        $params = isset($this->request->url) ? $this->request->url : $this->request->params["controller"];
        $params = substr($params, -1, 1) == "/" ? substr($params, 0, strlen($params) - 1) : $params;

        if (!in_array($this->request->params["controller"], $this->dontValidateControllers)) {
            $this->validatePage();
        }
        $merchants = $this->GrouperApi->loadDCs();
    }
    public function beforeRender()
    {

    }
    function _logPageView($uri = null)
    {
        return;
        $dontLogPages = array("\/admin\/ping", "\/ping\/index");
        $uri = empty($uri) ? $_SERVER["REQUEST_URI"] : $uri;

        if (!$this->alreadyLogged) {
            if (!preg_match('/^(' . implode("|", $dontLogPages) . ')/i', $uri)) {
                $userId = is_null($this->Session->read("id")) ? null : $this->Session->read("id");
                $contactId = is_null($this->Session->read("infusionId")) ? null : $this->Session->read("infusionId");
                $remoteAddress = strtolower($_SERVER["REMOTE_ADDR"]);
                $protocol = (isset($_SERVER["HTTPS"]) ? "https" : "http");
                $host = strtolower($_SERVER["HTTP_HOST"]);
                $uri = explode("?", $uri);
                $queryString = sizeof($uri) > 1 ? $uri[1] : "";
                $uri = empty($this->here) ? $uri[0] : $this->here;

                $this->Audit->pageViews($userId, $contactId, $remoteAddress, $protocol, $host, $uri, $queryString);

                $this->alreadyLogged = true;
            }
        }
    }
    function validatePage()
    {
        $param = isset($this->request->url) ? $this->request->url : $this->request->params["controller"];
        $param = substr($param, -1, 1) == "/" ? substr($param, 0, strlen($param) - 1) : $param;
        if (!$this->Credential->valid($param)) {
            // DEBUG FOR LOCAL ENVIRONMENT TESTING
            // if (in_array($_SERVER['SERVER_NAME'], array('bcdoors-dev.it.ucla.edu')))
            // {
            //     $_SERVER['SHIBUCLAUNIVERSITYID'] = '804554016';
            //     $_SERVER['SHIBGIVENNAME'] = 'Rudolph';
            //     $_SERVER['SHIBSN'] = 'De Arco';
            // }
            if ($this->Login->validate($_SERVER)) {

            } else {
                $this->Credential->terminate();
                $this->redirect($this->Url->roadblock());
            }
            /*
            if ($this->HasPageAccess->loginRequired($param))
            {
                if ($param == "logins/affiliate" || substr($param, 0, strlen("affiliates")) == "affiliates")
                {
                    $this->_dispatch(npc::web()->login_url->affiliate);
                }
                else
                {
                    $this->_dispatch("logins", array("redirect" => $_SERVER["REQUEST_URI"]));
                }
            }
            */
            //$this->_dispatch("logins", array("redirect" => $_SERVER["REQUEST_URI"]));
            //$this->_dispatch(npc::web()->login_url->affiliate);
            //$this->redirect($this->Url->roadblock());
        }
    }
    function _dispatch($path, $params = array())
    {
        $this->autoRender = false;
        $dispatcher = new Dispatcher();
        $dispatcher->dispatch(new CakeRequest($path), new CakeResponse());
        exit(1);
    }
    function _shouldIPBeDenied()
    {
        $IP = $_SERVER["REMOTE_ADDR"];
        if (in_array($IP, $this->blockIps)) {
            header('Location: /roadblock.html');
            exit(1);
        }
    }
}
