<h1>Browse Access Plans</h1>
<form action="/door_plans" role="form">
    <div class="search-containers hide">
        <!-- ************** DISPLAY ERROR ******************* -->
        <div class="alert alert-danger alert-error door-error-message hide">
            <strong>Please select an Access Plan from the list</strong>
        </div>
        <div class="form-group">
            <label class="sr-only" for="door_plan">Card Holder Name</label>
            <div class="input-group col-xs-12">
                <input type="text" id="door_plan" class="form-control large" data-ng-change="change(this)" autocomplete="off" spellcheck="false" placeholder="Type here to begin search">
                <div id="grp_door_plan" class="input-group-btn" data-loading-text="Loading...">
                    <button type="button" id="btn_door_plan" class="btn btn-success">Search</button>
                </div>
            </div>
        </div>
    </div>
    <div class="search-containers">
        <table id="door-plans" class="table table-striped table-condensed" style="width: 1000px;">
        <thead>
            <tr>
                <th style="width: 5%;">&nbsp;</th>
                <th>BruinCard Access Plans</th>
            </tr>
        </thead>
        <tbody class="search-content">
        {if isset($door_plans) && $door_plans|@count > 0}
            {section name=i loop=$door_plans}
                <tr>
                <td>&nbsp;</td>
                <td data-uid=""><a href="{$smarty.const.BASE_URL}/door_plans?plan_id={$door_plans[i].name}">{$door_plans[i].description}</a></td>
                </tr>
            {/section}
        {/if}
        <tr {if !isset($door_plans) || (isset($door_plans) && $door_plans|@count > 0)}class="hide"{/if}>
        <td>&nbsp;</td>
        <td data-uid="">There are no results</td>
        </tr>
        </tbody>
        </table>
    </div>
    <input type="hidden" id="plan_id" name="plan_id" value="" />
</form>