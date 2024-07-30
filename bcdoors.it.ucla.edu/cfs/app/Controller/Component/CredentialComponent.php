<?php
App::uses('Component', 'Controller');
class CredentialComponent extends Component
{
	var $components = array('Cookie', 'Param', 'Session');
	var $controller = null;
	var $isAdmin = false;
	var $_session = array("admin" => array("admin_id",
                                           "admin_userLevel",
                                           "admin_username",
                                           "admin_password",
                                           "admin_email",
                                           "admin_firstName",
                                           "admin_lastName",
                                           "admin_dateCreated",
                                           "admin_isAdmin",
                                           "admin_isSuperUser",
                                           "admin_pages"),
                              "site" => array("id",
                                              "username",
                                              "password",
                                              "email",
                                              "uid",
                                              "firstName",
                                              "lastName",
                                              "dateCreated",
                                              "token"));
    var $_excludeSession = array("Config");

    public function initialize(Controller $controller)
    {
            $this->controller =& $controller;
    }
	function startup(Controller $controller)
	{
            $this->controller =& $controller;
            $this->isAdmin = isset($this->controller->isAdmin) ? $this->controller->isAdmin : false;
	}
    public function shutdown(Controller $controller)
    {

    }
    public function beforeRender(Controller $controller)
    {

    }
	function get()
	{
		$data = array();
		$data["error"] = "";

		try
		{
			if ($this->isAdmin)
			{
				$data["admin_id"] = $this->Session->read("admin_id");
				$data["admin_userLevel"] = $this->Session->read("admin_userLevel");
				$data["admin_username"] = $this->Session->read("admin_username");
				$data["admin_password"] = $this->Session->read("admin_password");
				$data["admin_email"] = $this->Session->read("admin_email");
				$data["admin_firstName"] = $this->Session->read("admin_firstName");
				$data["admin_lastName"] = $this->Session->read("admin_lastName");
				$data["admin_dateCreated"] = $this->Session->read("admin_dateCreated");
				$data["admin_isAdmin"] = $this->Session->read("admin_isAdmin");
				$data["admin_isSuperUser"] = $this->Session->read("admin_isSuperUser");
				$data["admin_pages"] = $this->Session->read("admin_pages");
			}
			else
			{
				$data["id"] = $this->Session->read("id");
				$data["username"] = $this->Session->read("username");
				$data["password"] = $this->Session->read("password");
                                $data["uid"] = $this->Session->read("uid");
				$data["firstName"] = $this->Session->read("firstName");
				$data["lastName"] = $this->Session->read("lastName");
				$data["email"] = $this->Session->read("email");
                                $data["dateCreated"] = $this->Session->read("dateCreated");
				$data["token"] = $this->Session->read("token");
			}
		}
		catch (Exception $e)
		{
			$data["error"] = $e;
		}

		return $data;
	}
	function set($data)
	{
		$message = "";
		try
		{
			if ($this->isAdmin)
			{
				$this->Cookie->write("ckfolder", '/', false);
				$this->Cookie->write("admin_id", $this->Param->get($data, "id"), false);
				$this->Session->write("admin_id", $this->Param->get($data, "id"));
				$this->Session->write("admin_userLevel", $this->Param->get($data, "userLevel"));
				$this->Session->write("admin_username", $this->Param->get($data, "username"));
				$this->Session->write("admin_password", $this->Param->get($data, "password"));
				$this->Session->write("admin_email", $this->Param->get($data, "email"));
				$this->Session->write("admin_firstName", $this->Param->get($data, "firstName"));
				$this->Session->write("admin_lastName", $this->Param->get($data, "lastName"));
				$this->Session->write("admin_dateCreated", date('Y-m-d H:i:s', strtotime($this->Param->get($data, "dateCreated"))));
				$this->Session->write("admin_isAdmin", $this->Param->get($data, "isAdmin", false));
				$this->Session->write("admin_isSuperUser", $this->Param->get($data, "isSuperUser", 0));
				$this->Session->write("admin_pages", isset($data["pages"]) && is_array($data["pages"]) ? $data["pages"] : array());
			}
			else
			{
				$this->Session->write("id", $this->Param->get($data, "id"));
				$this->Session->write("username", $this->Param->get($data, "username"));
				$this->Session->write("password", $this->Param->get($data, "password"));
				$this->Session->write("uid", $this->Param->get($data, "ucla_uid"));
				$this->Session->write("firstName", $this->Param->get($data, "firstName"));
				$this->Session->write("lastName", $this->Param->get($data, "lastName"));
				$this->Session->write("email", $this->Param->get($data, "email"));
                                $this->Session->write("dateCreated", date('Y-m-d H:i:s', strtotime($this->Param->get($data, "dateCreated"))));
				$this->Session->write("token", $this->Param->get($data, "token"));
			}

		}
		catch (Exception $e)
		{
			$message = $e;
		}

		return $message;
	}
	function valid($param = "")
	{
        $result = false;
        if ($this->Session->check('id') || $this->Session->check('admin_id'))
        {
            $data = $this->get();

            if ($this->isAdmin)
            {
                if (!empty($data["id"]) && $data["id"] > 0)
                    $result = true;
            }
            else
            {
                if (!empty($data["id"]) && $data["id"] > 0)
                {
                	$shib = $this->controller->shibbolithParams;
                	if (isset($data['uid']) && (isset($_SERVER[$shib[0]]) && $data['uid'] == $_SERVER[$shib[0]]))
                    	$result = true;
                }
                else if ($param == "logins/affiliate" || substr($param, 0, strlen("affiliates")) == "affiliates")
                {
                    $result = !empty($data["affiliateId"]) && !empty($data["affiliatePassword"]);
                }
            }
        }
        return $result;
	}
	function terminate($isAdmin = false)
	{
	  $admin_id = $this->Session->read("admin_id");
	  if ($this->Session->check('id') || $this->Session->check('admin_id'))
	  {
	  	if ($isAdmin)
	  	{
		    $this->Cookie->del("admin_id");
		    for ($loopCNT = 0; $loopCNT < sizeof($this->_session["admin"]); $loopCNT++)
		    {
			    $this->Session->delete($this->_session["admin"][$loopCNT]);
		    }
	  	}
		else
        {
		    foreach($_SESSION as $key => $value)
		    {
                if (!in_array($key, $this->_excludeSession) && !$this->controller->String->beginsWith($key, "admin-"))
                    $this->controller->Session->delete($key);
		    }
        }
	  }
	}
}
?>