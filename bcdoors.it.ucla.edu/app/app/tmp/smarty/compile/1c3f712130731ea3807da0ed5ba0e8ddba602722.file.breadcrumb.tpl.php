<?php /* Smarty version Smarty-3.1.19, created on 2024-07-29 11:30:57
         compiled from "/var/www/bcdoors.it.ucla.edu/app/app/View/Common/breadcrumb.tpl" */ ?>
<?php /*%%SmartyHeaderCode:211642312066a7dfe176cde2-96548573%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1c3f712130731ea3807da0ed5ba0e8ddba602722' => 
    array (
      0 => '/var/www/bcdoors.it.ucla.edu/app/app/View/Common/breadcrumb.tpl',
      1 => 1412711226,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '211642312066a7dfe176cde2-96548573',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'crumbs' => 0,
    'key' => 0,
    'value' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_66a7dfe177fe25_76796171',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_66a7dfe177fe25_76796171')) {function content_66a7dfe177fe25_76796171($_smarty_tpl) {?><!-- ----------------- BEGIN - BREADCRUMB ----------------- -->
<ul class="breadcrumb" style="margin-top: 1%;">
    <?php  $_smarty_tpl->tpl_vars["value"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["value"]->_loop = false;
 $_smarty_tpl->tpl_vars["key"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['crumbs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars["value"]->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars["value"]->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars["value"]->key => $_smarty_tpl->tpl_vars["value"]->value) {
$_smarty_tpl->tpl_vars["value"]->_loop = true;
 $_smarty_tpl->tpl_vars["key"]->value = $_smarty_tpl->tpl_vars["value"]->key;
 $_smarty_tpl->tpl_vars["value"]->iteration++;
 $_smarty_tpl->tpl_vars["value"]->last = $_smarty_tpl->tpl_vars["value"]->iteration === $_smarty_tpl->tpl_vars["value"]->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['crumbs']['last'] = $_smarty_tpl->tpl_vars["value"]->last;
?>
        <li>
            <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['crumbs']['last']) {?>
                <?php echo $_smarty_tpl->tpl_vars['key']->value;?>

            <?php } else { ?>
                <a href="<?php if (empty($_smarty_tpl->tpl_vars['value']->value)) {?>#<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['value']->value;?>
<?php }?>"><?php echo $_smarty_tpl->tpl_vars['key']->value;?>
</a>
            <?php }?>
        </li>
    <?php } ?>
</ul>
<!-- ----------------- END - BREADCRUMB ----------------- --><?php }} ?>
