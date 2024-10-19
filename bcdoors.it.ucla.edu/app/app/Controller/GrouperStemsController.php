<?php
class GrouperStemsController extends AppController 
{
    var $name = 'GrouperStems';
    var $uses = array();
    var $components = array('Array', 'GrouperApi', 'Http', 'Validate');
    
    public function index()
    {
        $this->autoRender = false;
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

        $groupNames = array();
        $groupStemCNT = sizeof($stems);
        for ($loopCNT2 = 0; $loopCNT2 < $groupStemCNT; $loopCNT2++)
        {
            $values = $stems[$loopCNT2]['GrouperStem']['groups'];
            $groupCNT = sizeof($values);
            $found = false;
            for ($loopCNT3 = 0; $loopCNT3 < $groupCNT; $loopCNT3++)
            {
                $groupNames[] = $values[$loopCNT3]['GrouperGroup']['description'];
            }
        }
        echo "<pre>";
        echo implode("\r\n", $groupNames);
        echo "</pre>";
    }
}
?>