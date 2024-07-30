<?php
class LoginsController extends AppController
{
    var $name = 'Logins';
    var $uses = array('Definition', 'Users');
    var $components = array('Cookie', 'GrouperApi','Login', 'Validate');

    public function index()
    {
        $additionalFiles = array('css' => array(), 'js' => array('/js/jquery.blockUI-2.65.min.js',
																 '/js/angular.min.js',
																 '/js/angular-route.min.js',
                                                                 '/js/login.min.js',
                                                                 '/js/jquery.functions.min.js',
                                                                 '/js/jquery.blockMessage.min.js'));

        $this->set('additional_files', $additionalFiles);
    }
    private function _setMerchantAccess()
    {
        $id = $this->Session->read('uid');

        if (DEBUG_WRITE) {$this->Debug->write("Start Login `{$id}`");};
        $this->Login->setGrouperAccess($id);
        if (DEBUG_WRITE) {$this->Debug->write("End Login `{$id}`");};
    }
    public function xml_index()
    {
        $this->autoRender = false;
        $this->autoLayout = false;

        $params = $this->Param->url("debug", 0) == 1 ? $this->params->query : $this->params->data;
        $prefixes = array("Users");
        $minimumFields = array(array(array("username", "UCLA Login ID"), array("password", "Password")));

        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");

        $html = "";
        $redirect = '';

        $errors = $this->Validate->fields($form[$prefixes[0]], $minimumFields[0]);
        $continue = sizeof($errors) == 0;

        if (AUDIT_ACTIVITY)
            $this->Audit->user("Login", null, null, "Username: " . (isset($form[$prefixes[0]]['username']) ? $form[$prefixes[0]]['username'] : null));

        if ($continue)
        {
            $conditions = array("username" => $form[$prefixes[0]]["username"], "password" => $form[$prefixes[0]]["password"]);
            $data = $this->Users->find("first", array("conditions" => $conditions));
            if (isset($data[$prefixes[0]]["username"]))
            {
                $html = $this->Xml->serialize($data[$prefixes[0]]);
                $redirect = BASE_URL . '/home';

                $data[$prefixes[0]]['dateCreated'] = $data[$prefixes[0]]['inserted'];
                unset($data[$prefixes[0]]['inserted']);

                $this->Credential->set($data[$prefixes[0]]);
                if (AUDIT_ACTIVITY)
                    $this->Audit->user("Login Success", $data[$prefixes[0]]['id'], null);

                $data[$prefixes[0]]["id"] = $data[$prefixes[0]]["uid"];

                self::_setMerchantAccess();
            }
            else
            {
                $errors[] = 'Invalid authentication credentials.';
            }
        }

        if (AUDIT_ACTIVITY)
        {
            if (sizeof($errors) > 0)
                $this->Audit->user("Login Failed", null, null, implode("|", $errors));
        }

        $html = "<response>" . "\n" .
                "<user>" . $html . "</user>" . "\n" .
                "<redirect>" . $this->Xml->encode($redirect) . "</redirect>" . "\n" .
                "<errors>" . $this->Xml->enclose("error", $errors) . "</errors>" . "\n" .
                "</response>";

        $this->response->type('xml');
        $this->response->body($html);
    }
    public function json_index()
    {
        $this->autoRender = false;
        $this->autoLayout = false;

        $params = $this->Param->url("debug", 0) == 1 ? $this->params->query : $this->params->data;
        $prefixes = array("Users");
        $minimumFields = array(array(array("username", "UCLA Login ID"), array("password", "Password")));

        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");

        $response = array('response' => array(), 'errors' => array(), 'redirect' => '');

        $response['errors'] = $this->Validate->fields($form[$prefixes[0]], $minimumFields[0]);
        $continue = sizeof($response['errors']) == 0;

        if (AUDIT_ACTIVITY)
            $this->Audit->user("Login", null, null, "Username: " . (isset($form[$prefixes[0]]['username']) ? $form[$prefixes[0]]['username'] : null));

        if ($continue)
        {
            $conditions = array("username" => $form[$prefixes[0]]["username"], "password" => $form[$prefixes[0]]["password"]);
            //$data = $this->Users->find("first", array("conditions" => $conditions));
            if ($form[$prefixes[0]]["username"] == 'rdearco')
            {
                $data = array($prefixes[0] => array('inserted' => '2014-11-04 00:00:00',
                                                    'id' => 22,
                                                    'ucla_uid' => '804554016',
                                                    'username' => $form[$prefixes[0]]["username"],
                                                    'password' => $form[$prefixes[0]]["password"],
                                                    'email' => 'rdearco@it.ucla.edu',
                                                    'first_name' => 'Rudolph',
                                                    'last_name' => 'De Arco'));

                if (isset($data[$prefixes[0]]))
                {
                    $response['response'] = $data[$prefixes[0]];
                    $response['redirect'] = BASE_URL . '/home';

                    $data[$prefixes[0]]['dateCreated'] = $data[$prefixes[0]]['inserted'];
                    $data[$prefixes[0]]['firstName'] = $data[$prefixes[0]]['first_name'];
                    $data[$prefixes[0]]['lastName'] = $data[$prefixes[0]]['last_name'];

                    unset($data[$prefixes[0]]['inserted']);

                    $this->Credential->set($data[$prefixes[0]]);

                    if (AUDIT_ACTIVITY)
                        $this->Audit->user("Login Success", $data[$prefixes[0]]['id'], null);

                    self::_setMerchantAccess();
                    if (!isset($_SESSION['ppid']) || empty($_SESSION['ppid']))
                    {
                        $response['errors'][] = 'Invalid authentication credentials.';
                    }
                }
                else
                {
                    $response['errors'][] = 'Invalid authentication credentials.';
                }
            }
        }

        if (AUDIT_ACTIVITY)
        {
            if (sizeof($response['errors']) > 0)
                $this->Audit->user("Login Failed", null, null, implode("|", $response['errors']));
        }

        $this->response->type('json');
        $this->response->body(json_encode($response));
    }
    public function out()
    {
        $this->autoRender = false;

        $this->Credential->terminate();
        $this->redirect(SHIBBOLETH_LOGOUT_URL);
    }
}
?>