<?php
class GrouperController extends AppController 
{
    var $name = 'Grouper';
    var $uses = array();
    var $components = array('Array', 'GrouperApi', 'Http', 'Validate');
    
    public function index()
    {
        $this->autoRender = false;
    }
    public function rest()
    {
        $this->autoRender = false;
        $this->autoLayout = false;
        
        $appValues = Cache::read(CACHE_NAME_APPLICATION);
        $merchants = $this->Session->read('merchants');
        
        $stemName = $appValues['stem']['path']['ag'] . ":" . $merchants[1];
        $groups = $this->GrouperApi->getGroups($stemName);

        pr($groups);
        
        exit(1);
        $merchants = $this->GrouperApi->getGroups($appValues['stem']['path']['dc']);
        $groupCNT = sizeof($merchants);
            
        for ($loopCNT = 0; $loopCNT < $groupCNT; $loopCNT++)
        {
            $groupName = $merchants[$loopCNT]['name'];
            echo $groupName . "<BR>";
            $merchants[$loopCNT]['subjects'] = $this->Array->convertLikeModel('GrouperSubjects', $this->GrouperApi->getMembers($groupName));
        }
            
        echo "<pre>"; print_r($merchants); echo "</pre>";
        
        //==> Get Stems
        $stemName = $appValues['stem']['path']['ag'];
        $stems = $this->Array->convertLikeModel('GrouperStem', $this->GrouperApi->getStems($stemName));
        $stemCNT = sizeof($stems);
        for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++)
        {
            //==> Get Groups
            $groupStemName = $stems[$loopCNT]['name'];
            $stems[$loopCNT]['groups'] = $this->GrouperApi->getGroups($groupStemName);
            $groupCNT = sizeof($stems[$loopCNT]['groups']);
            
            for ($loopCNT2 = 0; $loopCNT2 < $groupCNT; $loopCNT2++)
            {
                $groupName = $stems[$loopCNT]['groups'][$loopCNT2]['name'];
                $stems[$loopCNT]['groups'][$loopCNT2]['subjects'] = $this->GrouperApi->getMembers($groupName);
            }
        }

        pr($stems);
    }
    public function retrieve()
    {
        $this->autoRender = false;
        $this->autoLayout = false;
        
        $appValues = Cache::read(CACHE_NAME_APPLICATION);
        
        //==> Get Stems
        $stemName = $appValues['stem']['path']['ag'];
        $stems = $this->Array->convertLikeModel('GrouperStem', $this->GrouperApi->getStems($stemName));

        $stemCNT = sizeof($stems);
        for ($loopCNT = 0; $loopCNT < $stemCNT; $loopCNT++)
        {
            //==> Get Groups
            $groupStemName = $stems[$loopCNT]['GrouperStem']['name'];
            $stems[$loopCNT]['GrouperStem']['groups'] = $this->Array->convertLikeModel('GrouperGroup', $this->GrouperApi->getGroups($groupStemName));
        }

        pr($stems);
    }
}
?>