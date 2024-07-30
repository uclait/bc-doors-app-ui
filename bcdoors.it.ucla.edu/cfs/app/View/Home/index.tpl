{if !isset($tab)}{$tab = 'card'}{/if}
<div class="tabbable" data-ng-controller="cardHolderCtrl">
    <ul class="nav nav-tabs nav-pills right-to-left">
        <li><a href="{$smarty.const.BASE_URL}/logins/out" data-toggle="" class="pull-left">{$smarty.session.firstName} {$smarty.session.lastName} (Logout)</a></li>
        <li class="disabled{if $tab == 'help'} active{/if}"><a data-toggle="" href="#tab4" data-loading-text="Loading...">Help</a></li>
        <li class="hide disabled{if $tab == 'preference'} active{/if}"><a data-toggle="" href="#tab3" data-loading-text="Loading...">Preferences</a></li>
        <li class="{if $tab == 'door'} active{/if}"><a data-toggle="tab" href="#tab2" data-loading-text="Loading...">Access Plans</a></li>
        <li class="{if $tab == '' || $tab == 'card'}active{/if}"><a data-toggle="tab" href="#tab1" data-loading-text="Loading...">Card Holders</a></li>
    </ul>
    <div class="tab-content">
        <div id="tab1" class="tab-pane{if $tab == '' || $tab == 'card'} active{/if}">
            {include file="../Common/breadcrumb.tpl" crumbs=$bread_crumbs.card}
            {if isset($uid)}
                {include file="../CardHolder/content.tpl"}
            {else}
                {include file="../Search/card-holder.tpl"}
            {/if}
        </div>
        <div id="tab2" data-ng-controller="accessPlanCtrl" class="tab-pane{if $tab == 'door'} active{/if}">
            {include file="../Common/breadcrumb.tpl" crumbs=$bread_crumbs.door}
            {if isset($plan_id)}
                {include file="../DoorPlans/content.tpl"}
            {else}
                {include file="../Search/door-plan.tpl"}
            {/if}
        </div>
        <div id="tab3" class="tab-pane{if $tab == 'preference'} active{/if}">
            <p>Preferences</p>
        </div>
        <div id="tab4" class="tab-pane{if $tab == 'help'} active{/if}">
            <p>Help goes here</p>
        </div>
    </div>
</div>
<input type="hidden" id="user_id" value="{if isset($smarty.session.id)}{$smarty.session.id}{/if}" />