<?php
class CardHolderDoorPlan extends AppModel
{
    var $name = 'CardHolderDoorPlan';
    var $useTable = "card_holder_door_plans";

    function available($cardHolderId, $doorPlanId = NULL)
    {
       
        $sql = sprintf("CALL card_holder_available_door_plans_sp(%s, %s);", $cardHolderId, (empty($doorPlanId) ? 'NULL' : $doorPlanId));
                
        return $this->query($sql);
    }
    function assigned($cardHolderId, $doorPlanId = NULL)
    {
       
        $sql = sprintf("CALL card_holder_assigned_door_plans_sp(%s, %s);", $cardHolderId, (empty($doorPlanId) ? 'NULL' : $doorPlanId));
                
        return $this->query($sql);
    }
}
?>