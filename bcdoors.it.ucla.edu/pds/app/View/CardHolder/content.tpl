{if !empty($name)}<h1>BruinCard Holder: {$name}</h1>{/if}
<form action="/card_holder" class="form-horizontal" role="form">
    <!-- ************** DISPLAY ERROR ******************* -->
    <div class="alert alert-danger alert-error card-error-message {if !empty($name)}hide{/if}">
        <strong>BruinCard Card Holder information not found</strong>
    </div>
    <div class="card-holder-container {if empty($name)}hide{/if}">
        <div class="panel panel-default" style="float: left;">
            <div class="panel-body">
                <div class="form-group">
                    <label for="name" class="control-label col-sm-3">Card Holder Name</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">{$name}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="uid" class="control-label col-xs-3">UID</label>
                    <div class="col-xs-9">
                        <p class="form-control-static">{$data.uid}</p>
                    </div>
                </div>
                <div class="form-group{if !isset($data.ucla_email_id)} hide{/if}">
                    <label for="email" class="control-label col-xs-3">Email</label>
                    <div class="col-xs-9">
                        <p class="form-control-static">{if isset($data.ucla_email_id)}{$data.email}{/if}</p>
                    </div>
                </div>
                <div class="card-holder-assign">
                    <div class="card-holder-plan-box">
                        <div class="panel panel-default">
                            <div class="panel-heading"><strong>Assigned Access Plans</strong></div>
                            <div class="panel-body">
                                <p><input type="text" class="form-control" id="filter-assigned-doors" placeholder="Filter"></p>
                                <div class="table-container">
                                    <ul id="assigned-doors">
                                        {section name=i loop=$assigned}
                                            <li data-id="{$assigned[i].name}"><span class="{if in_array($assigned[i].name, $pending)}icon-assigned{/if}">&nbsp;</span>{$assigned[i].description}</li>
                                        {/section}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix visible-xs-block visible-sm-block"></div>
                    <div class="card-holder-plan-buttons">
                        <p class="text-center"><button class="btn btn-success btn-sm" disabled><< Assign Access</button></p>
                        <p class="text-center"><button class="btn btn-danger btn-sm" disabled>Remove Access >></button></p>
                    </div>
                    <div class="clearfix visible-xs-block visible-sm-block"></div>
                    <div class="card-holder-plan-box">
                        <div class="panel panel-default">
                            <div class="panel-heading"><strong>Available Access Plans</strong></div>
                            <div class="panel-body">
                                <p><input type="text" class="form-control" id="filter-available-doors" placeholder="Filter"></p>
                                <div class="table-container">
                                    <ul id="available-doors">
                                        {section name=i loop=$available}
                                            <li data-id="{$available[i].GrouperGroup.name}"><span class="{if in_array($available[i].GrouperGroup.name, $pending)}icon-available{/if}">&nbsp;</span>{$available[i].GrouperGroup.description}</li>
                                        {/section}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                {include file="../Common/legend.tpl"}
            </div>
        </div>
        <div class="clearfix visible-xs-block"></div>
        <div class="panel panel-info pull-right">
            <div class="panel-heading"><strong>More Actions</strong></div>
            <div class="panel-body">
                <a href="{$smarty.const.BASE_URL}/home">Find Another BruinCard Holder</a>
            </div>
        </div>
    </div>
    <input type="hidden" id="uid" name="uid" value="{$uid}" />
    <input type="hidden" id="name" name="name" value="{$name}" />
    <input type="hidden" id="CardHolder-id" name="CardHolder-id" value="{if isset($data.id)}{$data.id}{/if}" />
</form>
<div id="mod_remove-access" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">CONFIRM</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you wish to remove <span class="Var-Name">{$name}'s</span> access to:</p>
                <p></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Remove</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>