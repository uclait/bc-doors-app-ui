<?php
class GrouperMembersController extends AppController 
{
    var $name = 'GrouperMembers';
    var $uses = array();
    var $components = array('GrouperApi', 'Http', 'Validate');
    
    public function index()
    {
        $this->autoRender = false;
    }
    public function retrieve()
    {
        $this->autoRender = false;
        $this->autoLayout = false;
        
        $params = $this->Param->url("debug", 0) == 1 ? $this->params->query : $this->params->data;
        $prefixes = array("GrouperMember");
        $minimumFields = array(array(array("group_name", "Group Name")));
        
        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        
        $html = "";
        $errors = array();
        $continue = false;
        $redirect = '';

        $errors = $this->Validate->fields($form[$prefixes[0]], $minimumFields[0]);
        $continue = sizeof($errors) == 0;

        if ($continue)
        {      
               $response = $this->GrouperApi->getMembers($form[$prefixes[0]]['group_name']);
               $html = $this->Xml->serialize($response);
        }
        $html = "<response>" . "\n" .
                "<members>" . $html . "</members>" . "\n" .
                "<redirect>" . $this->Xml->encode($redirect) . "</redirect>" . "\n" .
                "<errors>" . $this->Xml->enclose("error", $errors) . "</errors>" . "\n" .
                "</response>";
        
        $this->response->type('xml');
        $this->response->body($html);
    }
    public function add()
    {
        $this->autoRender = false;
        $this->autoLayout = false;
        
        $params = $this->Param->url("debug", 0) == 1 ? $this->params->query : $this->params->data;
        $prefixes = array("GrouperMember");
        $minimumFields = array(array(array("group_name", "Group Name"), array("identifier", "Identifier")));
        
        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        
        $html = "";
        $errors = array();
        $continue = false;
        $redirect = '';

        $errors = $this->Validate->fields($form[$prefixes[0]], $minimumFields[0]);
        $continue = sizeof($errors) == 0;

        if ($continue)
        {
               $response = $this->GrouperApi->addMember($form[$prefixes[0]]['group_name'], $form[$prefixes[0]]['identifier']);
               $html = $this->Xml->serialize($response);
        }
        $html = "<response>" . "\n" .
                "<member>" . $html . "</member>" . "\n" .
                "<redirect>" . $this->Xml->encode($redirect) . "</redirect>" . "\n" .
                "<errors>" . $this->Xml->enclose("error", $errors) . "</errors>" . "\n" .
                "</response>";
        
        $this->response->type('xml');
        $this->response->body($html);
    }
    public function delete()
    {
        $this->autoRender = false;
        $this->autoLayout = false;
        
        $params = $this->Param->url("debug", 0) == 1 ? $this->params->query : $this->params->data;
        $prefixes = array("GrouperMember");
        $minimumFields = array(array(array("group_name", "Group Name"), array("identifier", "Identifier")));
        
        $form[$prefixes[0]] = $this->Param->extract($params, "{$prefixes[0]}-");
        
        $html = "";
        $errors = array();
        $continue = false;
        $redirect = '';

        $errors = $this->Validate->fields($form[$prefixes[0]], $minimumFields[0]);
        $continue = sizeof($errors) == 0;

        if ($continue)
        {
               $response = $this->GrouperApi->deleteMember($form[$prefixes[0]]['group_name'], $form[$prefixes[0]]['identifier']);
               $html = $this->Xml->serialize($response);
        }
        $html = "<response>" . "\n" .
                "<member>" . $html . "</member>" . "\n" .
                "<redirect>" . $this->Xml->encode($redirect) . "</redirect>" . "\n" .
                "<errors>" . $this->Xml->enclose("error", $errors) . "</errors>" . "\n" .
                "</response>";
        
        $this->response->type('xml');
        $this->response->body($html);
    }
}
?>