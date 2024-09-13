<?php /* Smarty version Smarty-3.1.19, created on 2024-07-29 11:30:57
         compiled from "/var/www/bcdoors.it.ucla.edu/app/app/View/CardHolder/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3298777266a7dfe1782840-33709049%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b0b6e14f1b732c488ba9857512562dbcc8fadc8d' => 
    array (
      0 => '/var/www/bcdoors.it.ucla.edu/app/app/View/CardHolder/content.tpl',
      1 => 1424386411,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3298777266a7dfe1782840-33709049',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'name' => 0,
    'data' => 0,
    'assigned' => 0,
    'pending' => 0,
    'available' => 0,
    'uid' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_66a7dfe17c44b5_94208001',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_66a7dfe17c44b5_94208001')) {function content_66a7dfe17c44b5_94208001($_smarty_tpl) {?><?php if (!empty($_smarty_tpl->tpl_vars['name']->value)) {?><h1>BruinCard Holder: <?php echo $_smarty_tpl->tpl_vars['name']->value;?>
</h1><?php }?>
<form action="/card_holder" class="form-horizontal" role="form">
    <!-- ************** DISPLAY ERROR ******************* -->
    <div class="alert alert-danger alert-error card-error-message <?php if (!empty($_smarty_tpl->tpl_vars['name']->value)) {?>hide<?php }?>">
        <strong>BruinCard Card Holder information not found</strong>
    </div>
    <div class="card-holder-container <?php if (empty($_smarty_tpl->tpl_vars['name']->value)) {?>hide<?php }?>">
        <div class="panel panel-default" style="float: left;">
            <div class="panel-body">
                <div class="form-group">
                    <label for="name" class="control-label col-sm-3">Card Holder Name</label>
                    <div class="col-sm-9">
                        <p class="form-control-static"><?php echo $_smarty_tpl->tpl_vars['name']->value;?>
</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="uid" class="control-label col-xs-3">UID</label>
                    <div class="col-xs-9">
                        <p class="form-control-static"><?php echo $_smarty_tpl->tpl_vars['data']->value['uid'];?>
</p>
                    </div>
                </div>
                <div class="form-group<?php if (!isset($_smarty_tpl->tpl_vars['data']->value['ucla_email_id'])) {?> hide<?php }?>">
                    <label for="email" class="control-label col-xs-3">Email</label>
                    <div class="col-xs-9">
                        <p class="form-control-static"><?php if (isset($_smarty_tpl->tpl_vars['data']->value['ucla_email_id'])) {?><?php echo $_smarty_tpl->tpl_vars['data']->value['email'];?>
<?php }?></p>
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
                                        <?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['i'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['assigned']->value) ? count($_loop) : max(0, (int) $_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?>
                                            <li data-id="<?php echo $_smarty_tpl->tpl_vars['assigned']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['name'];?>
"><span class="<?php if (in_array($_smarty_tpl->tpl_vars['assigned']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['name'],$_smarty_tpl->tpl_vars['pending']->value)) {?>icon-assigned<?php }?>">&nbsp;</span><?php echo $_smarty_tpl->tpl_vars['assigned']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['description'];?>
</li>
                                        <?php endfor; endif; ?>
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
                                        <?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['i'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['available']->value) ? count($_loop) : max(0, (int) $_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?>
                                            <li data-id="<?php echo $_smarty_tpl->tpl_vars['available']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['GrouperGroup']['name'];?>
"><span class="<?php if (in_array($_smarty_tpl->tpl_vars['available']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['GrouperGroup']['name'],$_smarty_tpl->tpl_vars['pending']->value)) {?>icon-available<?php }?>">&nbsp;</span><?php echo $_smarty_tpl->tpl_vars['available']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['GrouperGroup']['description'];?>
</li>
                                        <?php endfor; endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <?php echo $_smarty_tpl->getSubTemplate ("../Common/legend.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

            </div>
        </div>
        <div class="clearfix visible-xs-block"></div>
        <div class="panel panel-info pull-right">
            <div class="panel-heading"><strong>More Actions</strong></div>
            <div class="panel-body">
                <a href="<?php echo @constant('BASE_URL');?>
/home">Find Another BruinCard Holder</a>
            </div>
        </div>
    </div>
    <input type="hidden" id="uid" name="uid" value="<?php echo $_smarty_tpl->tpl_vars['uid']->value;?>
" />
    <input type="hidden" id="name" name="name" value="<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
" />
    <input type="hidden" id="CardHolder-id" name="CardHolder-id" value="<?php if (isset($_smarty_tpl->tpl_vars['data']->value['id'])) {?><?php echo $_smarty_tpl->tpl_vars['data']->value['id'];?>
<?php }?>" />
</form>
<div id="mod_remove-access" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">CONFIRM</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you wish to remove <span class="Var-Name"><?php echo $_smarty_tpl->tpl_vars['name']->value;?>
's</span> access to:</p>
                <p></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Remove</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div><?php }} ?>
