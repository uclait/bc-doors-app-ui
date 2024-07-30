<div class="panel panel-info login" data-ng-controller="loginCtrl">
    <div class="panel-heading"><strong>Enter your UCLA Logon ID and password</strong></div>
    <div class="panel-body">
        <form id="helpForm" name="helpForm" class="form-horizontal">
            <!-- ************** DISPLAY ERROR ******************* -->
            <div class="panel panel-danger error-message" data-ng-show="messages">
                <div class="panel-heading"><strong>ERROR</strong></div>
                <div class="panel-body">
                    <span ng-repeat='message in messages'>
                        - {literal}{{message}}{/literal}<br />
                    </span>
                </div>
            </div>
            <fieldset>
                <div class="form-group">
                    <label for="username" class="control-label col-xs-4">UCLA Logon ID</label>
                    <div class="col-xs-5">
                        <input type="text" class="form-control" id="username" name="username" data-ng-model="username" placeholder="UCLA Logon ID" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="control-label col-xs-4">Password</label>
                    <div class="col-xs-5">
                        <input type="password" class="form-control" id="password" name="password" data-ng-model="password" placeholder="Password" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-offset-4 col-xs-5">
                        <button type="button" class="btn btn-primary btn-lg" data-ng-click="submit(helpForm)">Login</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<input type="hidden" id="redirect" />