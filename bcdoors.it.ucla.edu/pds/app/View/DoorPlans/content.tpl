{if !empty($name)}<h1>Access Plan: {$name}</h1>{/if}
<!-- ************** DISPLAY ERROR ******************* -->
<div class="alert alert-danger alert-error card-error-message {if !empty($name)}hide{/if}">
    <strong>Access Plan not found</strong>
</div>
<div class="access-plan card-holder-container {if empty($name)}hide{/if}">
    <div class="panel panel-default" style="float: left;">
        <div class="panel-heading"><strong>Current Plan Members</strong></div>
        <div class="panel-body">
            <div class="door-plan-container">
                <table id="door-plan" class="table table-striped table-condensed pull-left" style="width: 75%;">
                    <thead>
                        <tr>
                            <th><input id="chk-all" type="checkbox" /></th>
                            <th>Card Holder/Group Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        {section name=i loop=$data}
                            <tr>
                            <td style="width: 10px;">{if !empty($data[i].uclauniversityid)}<input type="checkbox" data-id="{$data[i].uclauniversityid}" value="{$data[i].uclauniversityid}" />{/if}</td>
                            <td><span class="{if isset($pending[$data[i].uclauniversityid])}icon-assigned{/if}">&nbsp;</span>(p) {trim($data[i].name)}</td>
                            </tr>
                        {/section}
                    </tbody>
                </table>

                <div class="card-holder-plan-buttons pull-right">
                    <p class="text-center"><button class="btn btn-success btn-sm">Add Card Holder</button></p>
                    <p class="text-center"><button class="btn btn-danger btn-sm" disabled>Remove Members</button></p>   
                </div>
            </div>
            <div class="door-plan-container" style="clear: both">
                {include file="../Common/legend.tpl"}
            </div>
        </div>
    </div>
    <input type="hidden" id="DoorPlan-id" value="{if isset($id)}{$id}{/if}" />
    <input type="hidden" id="DoorPlan-plan_id" value="{if isset($plan_id)}{$plan_id}{/if}" />
    
    <div class="panel panel-info pull-right">
        <div class="panel-heading"><strong>More Actions</strong></div>
        <div class="panel-body">
            <a href="{$smarty.const.BASE_URL}/home?tab=door">Browse Access Plans</a>
        </div>
    </div>
</div>
<div id="mod_remove-access" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">CONFIRM</h4>
            </div>
            <div class="modal-body">
                <p><big>Are you sure you wish to remove access to <span class="Var-Name">{$name}'s</span> for the following Card Holders?</big></p>
                <p></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Remove</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div id="mod_assign-access" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add Card Holders to <span class="Var-Name">{$name}'s</span>:</h4>
            </div>
            <div class="modal-body">
                <h3 class="hide">Find BruinCard Holder</h3>
                <p>
                   <div class="form-group">
                       <label for="card_holder_uid_modal" class="control-label col-xs-3">UCLA UID</label>
                       <div class="input-group col-xs-6">
                            <input type="text" id="card_holder_uid_modal" data-ng-change="change(this)" class="form-control" placeholder="Type 9 digit UCLA UID">
                            <div id="grp_card_holder_uid_modal" class="input-group-btn" data-loading-text="Loading...">
                                <button type="button" id="btn_card_holder_uid_modal" class="btn btn-success">Search</button>
                            </div>
                        </div>
                   </div>
                   <div class="spacer"></div>
                   <div class="form-group">
                       <label for="card_holder_name_modal" class="control-label col-xs-3">Card Holder Name</label>
                       <div class="input-group col-xs-6">
                            <input type="text" id="card_holder_name_modal" data-ng-change="change(this)" class="form-control" placeholder="Type Card Holder Name to begin search">
                            <div id="grp_card_holder_name_modal" class="input-group-btn" data-loading-text="Loading...">
                                <button type="button" id="btn_card_holder_name_modal" class="btn btn-success">Search</button>
                            </div>
                        </div>
                    </div>
                </p>
                <div class="search-containers" style="width: 100%; max-width: 100%;">
                    <table id="card-holders-door" class="table table-striped table-condensed hide" style="margin-bottom: 0px;">
                    <thead>
                        <tr>
                            <th style="width: 10%;">&nbsp;</th>
                            <th>BruinCard Holder</th>
                        </tr>
                    </thead>
                    <tbody class="search-content">
                    <tr>
                    <td>&nbsp;</td>
                    <td data-uid="">There are no results
                    </td>
                    </tr>
                    </tbody>
                    </table>
                </div>
                <div class="table-responsive"> 
                    <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>Card Holder/Group Name</th>
                        </tr>
                    </thead>
                    </table>
                    <div>
                        <table id="door-plan-assign" class="table table-striped table-condensed">
                        <tbody>

                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" disabled>Save Changes</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>