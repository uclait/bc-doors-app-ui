<div class="container system">
	<div class="panel-group" id="accordion">
	    <!-- ****************** BEGIN - Debug Time ****************** -->
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h4 class="panel-title">
	                <a data-toggle="collapse" data-parent="#accordion" href="#collapseDebeugTime">Execution Times</a>
	            </h4>
	        </div>
	        <div id="collapseDebeugTime" class="panel-collapse collapse">
	            <div class="panel-body">
	                <pre>{$debug_time}</pre>
	            </div>
	        </div>
	    </div>
		<!-- ****************** BEGIN- Database INI ****************** -->
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h4 class="panel-title">
	                <a data-toggle="collapse" data-parent="#accordion" href="#collapseDbaseINI">Database INI</a>
	            </h4>
	        </div>
	        <div id="collapseDbaseINI" class="panel-collapse collapse">
	            <div class="panel-body">
	                <pre>{$dbase_ini}</pre>
	            </div>
	        </div>
	    </div>
	    <!-- ****************** BEGIN - Applicaton INI ****************** -->
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h4 class="panel-title">
	                <a data-toggle="collapse" data-parent="#accordion" href="#collapseAppINI">Application INI</a>
	            </h4>
	        </div>
	        <div id="collapseAppINI" class="panel-collapse collapse">
	            <div class="panel-body">
	                <pre>{$app_ini}</pre>
	            </div>
	        </div>
	    </div>
	    <!-- ****************** BEGIN - Databse Read ****************** -->
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h4 class="panel-title">
	                Database Read ... {$dbase_read}
	            </h4>
	        </div>
	    </div>
	    <!-- ****************** BEGIN - Database Write ****************** -->
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h4 class="panel-title">
	                Database Write ... {$dbase_write}
	            </h4>
	        </div>
	    </div>
	    <!-- ****************** BEGIN - Grouper Merchants ****************** -->
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h4 class="panel-title">
	                <a data-toggle="collapse" data-parent="#accordion" href="#collapseGrouperMerchants">Grouper Merchants</a>
	            </h4>
	        </div>
	        <div id="collapseGrouperMerchants" class="panel-collapse collapse">
	            <div class="panel-body">
	                <textarea class="form-control">{$grouper_merchants}</textarea>
	            </div>
	        </div>
	    </div>
	    <!-- ****************** BEGIN - Grouper Merchant Groups ****************** -->
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h4 class="panel-title">
	                <a data-toggle="collapse" data-parent="#accordion" href="#collapseGrouperMerchantGroups">Grouper Merchant Groups</a>
	            </h4>
	        </div>
	        <div id="collapseGrouperMerchantGroups" class="panel-collapse collapse">
	            <div class="panel-body">
	                <textarea class="form-control">{$grouper_merchant_groups}</textarea>
	            </div>
	        </div>
	    </div>
	    <!-- ****************** BEGIN - Cached Files ****************** -->
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h4 class="panel-title">
	                <a data-toggle="collapse" data-parent="#accordion" href="#collapseCachedFiles">Cached Files</a>
	            </h4>
	        </div>
	        <div id="collapseCachedFiles" class="panel-collapse collapse">
	            <div class="panel-body">
			        <table class="table table-striped table-condensed">
			        <thead>
			            <tr>
			            	<th>&nbsp;</th>
			                <th class="text-center">Name</th>
			                <th class="text-center">Size</th>
			                <th class="text-center">Time</th>
			            </tr>
			        </thead>
			        <tbody class="search-content">
			        {section name=i loop=$cached_files}
				        <tr>
				        <td class="text-right">{$smarty.section.i.rownum|number_format:0:"":","}.</td>
				        <td>{$cached_files[i].name}</td>
				        <td class="text-right">{$cached_files[i].size}</td>
				        <td class="text-center">{$cached_files[i].filetime}</td>
				        </tr>
			        {/section}
			        </tbody>
			        </table>
	            </div>
	        </div>
	    </div>
	    <!-- ****************** BEGIN - Log Files ****************** -->
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h4 class="panel-title">
	                <a data-toggle="collapse" data-parent="#accordion" href="#collapseLogFiles">Log Files</a>
	            </h4>
	        </div>
	        <div id="collapseLogFiles" class="panel-collapse collapse">
	            <div class="panel-body">
			        <table class="table table-striped table-condensed">
			        <thead>
			            <tr>
			            	<th>&nbsp;</th>
			                <th class="text-center">Name</th>
			                <th class="text-center">Size</th>
			                <th class="text-center">Time</th>
			            </tr>
			        </thead>
			        <tbody class="search-content">
			        {section name=i loop=$log_files}
				        <tr>
				        <td class="text-right">{$smarty.section.i.rownum|number_format:0:"":","}.</td>
				        <td><a href="{$smarty.const.BASE_URL}/logs/debug/{$log_files[i].name}" target="_blank">{$log_files[i].name}</a></td>
				        <td class="text-right">{$log_files[i].size}</td>
				        <td class="text-center">{$log_files[i].filetime}</td>
				        </tr>
			        {/section}
			        </tbody>
			        </table>
	            </div>
	        </div>
	    </div>
	</div>
</div>