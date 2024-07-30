<!-- ----------------- BEGIN - BREADCRUMB ----------------- -->
<ul class="breadcrumb" style="margin-top: 1%;">
    {foreach from=$crumbs key="key" item="value" name=crumbs}
        <li>
            {if $smarty.foreach.crumbs.last}
                {$key}
            {else}
                <a href="{if empty($value)}#{else}{$value}{/if}">{$key}</a>
            {/if}
        </li>
    {/foreach}
</ul>
<!-- ----------------- END - BREADCRUMB ----------------- -->