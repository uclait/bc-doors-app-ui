<?php /* Smarty version Smarty-3.1.19, created on 2024-07-29 15:50:20
         compiled from "/var/www/bcdoors.it.ucla.edu/app/app/View/Search/door-plan.tpl" */ ?>
<?php /*%%SmartyHeaderCode:182360760766a81cac6ccae0-22171436%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3ac45743b37ef9a5dade638ab5b34748e162e22e' => 
    array (
      0 => '/var/www/bcdoors.it.ucla.edu/app/app/View/Search/door-plan.tpl',
      1 => 1424386411,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '182360760766a81cac6ccae0-22171436',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'door_plans' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_66a81cac6e99c0_55636105',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_66a81cac6e99c0_55636105')) {function content_66a81cac6e99c0_55636105($_smarty_tpl) {?><h1>Browse Access Plans</h1>
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
        <?php if (isset($_smarty_tpl->tpl_vars['door_plans']->value)&&count($_smarty_tpl->tpl_vars['door_plans']->value)>0) {?>
            <?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['i'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['door_plans']->value) ? count($_loop) : max(0, (int) $_loop); unset($_loop);
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
                <tr>
                <td>&nbsp;</td>
                <td data-uid=""><a href="<?php echo @constant('BASE_URL');?>
/door_plans?plan_id=<?php echo $_smarty_tpl->tpl_vars['door_plans']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['name'];?>
"><?php echo $_smarty_tpl->tpl_vars['door_plans']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['description'];?>
</a></td>
                </tr>
            <?php endfor; endif; ?>
        <?php }?>
        <tr <?php if (!isset($_smarty_tpl->tpl_vars['door_plans']->value)||(isset($_smarty_tpl->tpl_vars['door_plans']->value)&&count($_smarty_tpl->tpl_vars['door_plans']->value)>0)) {?>class="hide"<?php }?>>
        <td>&nbsp;</td>
        <td data-uid="">There are no results</td>
        </tr>
        </tbody>
        </table>
    </div>
    <input type="hidden" id="plan_id" name="plan_id" value="" />
</form><?php }} ?>
