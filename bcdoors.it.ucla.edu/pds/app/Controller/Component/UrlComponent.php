<?php
class UrlComponent extends Object
{
	var $components = array('Session');
    public function initialize(Controller $controller)
    {

    }
	function startup(Controller $controller)
	{
            $this->controller =& $controller;
	}
    public function shutdown(Controller $controller)
    {

    }
    public function beforeRender(Controller $controller)
    {

    }
    public function beforeRedirect()
    {

    }
	public function community($params = "")
	{
		$url = "";
		if ($this->Session->read("userLevel") > npc::user()->levels()->EMPLOYEE)
		{
			$url = "/phpBB3/" . ($params == "" ? "index" : "viewtopic") . ".php?sid=" . $this->Session->read("phpbb_sid") . (!empty($params) ? "&{$params}" : "");
		}
		else
			$url = self::roadblock();

		return $url;
	}
	public function affiliate()
	{
		App::import('Component', "ClickBank");
		$ClickBank = new ClickBankComponent();
		$url = "https://imi.infusionsoft.com/go/npc/" . $this->Session->read("affiliateId");

		if ($this->Session->read("groups") != "")
		{
			if ($ClickBank->isCustomer($this->Session->read("groups")))
				$url = "https://www.clickbank.com/go/npc/" . $this->Session->read("affiliateId");
		}
		return $url;
	}
	public function techSupport($username, $password, $remember = 0)
	{
		$url = str_replace("https:", "http:", npc::web()->root) .
			 "/bbpress/bb-autologin.php" .
			 "?user_login=" . urlencode($username) .
			 "&password=" . urlencode($password) .
			 "&remember=" . $remember;

		return $url;
	}
	public function affiliateControlPanel()
	{
		$url = "https://imi.infusionsoft.com/Affiliate/login/processLogin.jsp" .
			 "?username=" . $this->Session->read("affiliateId") .
			 "&password=" . $this->Session->read("affiliatePassword") .
			 "&savePassword=true";

		return $url;
	}
	public function roadblock()
	{
		return "/notices/forbidden";
	}
	public function login()
	{
		return '/logins';
	}
	public function loginVersion()
	{
		return '/logins/version';
	}
}
?>