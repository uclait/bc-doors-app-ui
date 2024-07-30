<?php /* Smarty version Smarty-3.1.19, created on 2024-07-29 15:50:20
         compiled from "/var/www/bcdoors.it.ucla.edu/app/app/View/Home/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:158490246566a81cac5f8531-84535944%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '950470134e433f41ed857ebf61aed3e30d153aeb' => 
    array (
      0 => '/var/www/bcdoors.it.ucla.edu/app/app/View/Home/index.tpl',
      1 => 1424386411,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '158490246566a81cac5f8531-84535944',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'tab' => 0,
    'bread_crumbs' => 0,
    'uid' => 0,
    'plan_id' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_66a81cac6696e9_89980591',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_66a81cac6696e9_89980591')) {function content_66a81cac6696e9_89980591($_smarty_tpl) {?><?php if (!isset($_smarty_tpl->tpl_vars['tab']->value)) {?><?php $_smarty_tpl->tpl_vars['tab'] = new Smarty_variable('card', null, 0);?><?php }?>
<div class="tabbable" data-ng-controller="cardHolderCtrl">
    <ul class="nav nav-tabs nav-pills right-to-left">
        <li><a href="<?php echo @constant('BASE_URL');?>
/logins/out" data-toggle="" class="pull-left"><?php echo $_SESSION['firstName'];?>
 <?php echo $_SESSION['lastName'];?>
 (Logout)</a></li>
        <li class="disabled<?php if ($_smarty_tpl->tpl_vars['tab']->value=='help') {?> active<?php }?>"><a data-toggle="" href="#tab4" data-loading-text="Loading...">Help</a></li>
        <li class="hide disabled<?php if ($_smarty_tpl->tpl_vars['tab']->value=='preference') {?> active<?php }?>"><a data-toggle="" href="#tab3" data-loading-text="Loading...">Preferences</a></li>
        <li class="<?php if ($_smarty_tpl->tpl_vars['tab']->value=='door') {?> active<?php }?>"><a data-toggle="tab" href="#tab2" data-loading-text="Loading...">Access Plans</a></li>
        <li class="<?php if ($_smarty_tpl->tpl_vars['tab']->value==''||$_smarty_tpl->tpl_vars['tab']->value=='card') {?>active<?php }?>"><a data-toggle="tab" href="#tab1" data-loading-text="Loading...">Card Holders</a></li>
    </ul>
    <div class="tab-content">
        <div id="tab1" class="tab-pane<?php if ($_smarty_tpl->tpl_vars['tab']->value==''||$_smarty_tpl->tpl_vars['tab']->value=='card') {?> active<?php }?>">
            <?php echo $_smarty_tpl->getSubTemplate ("../Common/breadcrumb.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('crumbs'=>$_smarty_tpl->tpl_vars['bread_crumbs']->value['card']), 0);?>

            <?php if (isset($_smarty_tpl->tpl_vars['uid']->value)) {?>
                <?php echo $_smarty_tpl->getSubTemplate ("../CardHolder/content.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

            <?php } else { ?>
                <?php echo $_smarty_tpl->getSubTemplate ("../Search/card-holder.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

            <?php }?>
        </div>
        <div id="tab2" data-ng-controller="accessPlanCtrl" class="tab-pane<?php if ($_smarty_tpl->tpl_vars['tab']->value=='door') {?> active<?php }?>">
            <?php echo $_smarty_tpl->getSubTemplate ("../Common/breadcrumb.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('crumbs'=>$_smarty_tpl->tpl_vars['bread_crumbs']->value['door']), 0);?>

            <?php if (isset($_smarty_tpl->tpl_vars['plan_id']->value)) {?>
                <?php echo $_smarty_tpl->getSubTemplate ("../DoorPlans/content.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

            <?php } else { ?>
                <?php echo $_smarty_tpl->getSubTemplate ("../Search/door-plan.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

            <?php }?>
        </div>
        <div id="tab3" class="tab-pane<?php if ($_smarty_tpl->tpl_vars['tab']->value=='preference') {?> active<?php }?>">
            <p>Preferences</p>
        </div>
        <div id="tab4" class="tab-pane<?php if ($_smarty_tpl->tpl_vars['tab']->value=='help') {?> active<?php }?>">
            <p>Help goes here</p>
        </div>
    </div>
</div>
<input type="hidden" id="user_id" value="<?php if (isset($_SESSION['id'])) {?><?php echo $_SESSION['id'];?>
<?php }?>" /><?php }} ?>
