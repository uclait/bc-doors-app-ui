<h1>Find BruinCard Holder</h1>
<form action="/card_holder" role="form" class="form-search">
    <div class="search-containers">
        <!-- ************** DISPLAY ERROR ******************* -->
        <div class="alert alert-danger alert-error card-error-message hide">
            <strong>Please select a Card Holder from the list</strong>
        </div>

       <div class="form-group">
           <label for="card_holder_uid" class="control-label col-xs-3">UCLA UID</label>
           <div class="input-group col-xs-6">
                <input type="text" id="card_holder_uid" data-ng-change="change(this)" class="form-control" placeholder="Type 9 digit UCLA UID">
                <div id="grp_card_holder_uid" class="input-group-btn" data-loading-text="Loading...">
                    <button type="button" id="btn_card_holder_uid" class="btn btn-success">Search</button>
                </div>
            </div>
       </div>
       <div class="spacer"></div>
       <div class="form-group">
           <label for="card_holder_name" class="control-label col-xs-3">Card Holder Name</label>
           <div class="input-group col-xs-6">
                <input type="text" id="card_holder_name" data-ng-change="change(this)" class="form-control" placeholder="Type Card Holder Name to begin search">
                <div id="grp_card_holder_name" class="input-group-btn" data-loading-text="Loading...">
                    <button type="button" id="btn_card_holder_name" class="btn btn-success">Search</button>
                </div>
            </div>
        </div>
    </div>
    <div class="search-containers">
        <table id="card-holders" class="table table-striped table-condensed" style="margin-bottom: 0px;">
        <thead>
            <tr>
                <th style="width: 5%;">&nbsp;</th>
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
    <input type="hidden" id="uid" name="uid" value="" />
</form>