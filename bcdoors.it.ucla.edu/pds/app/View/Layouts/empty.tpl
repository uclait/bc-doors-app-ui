<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{$title_for_layout}</title>
<link rel="stylesheet" href="{$smarty.const.BASE_URL}/css/bootstrap.min.css">
<link rel="stylesheet" href="{$smarty.const.BASE_URL}/css/bootstrap-theme.min.css">
{if isset($additional_files) && isset($additional_files.css)}
    {section name=i loop=$additional_files.css}
        <link rel="stylesheet" href="{if substr($additional_files.css[i], 0, 2) != '//'}{$smarty.const.BASE_URL}{/if}{$additional_files.css[i]}">
    {/section}
{/if}
{if isset($additional_files) && isset($additional_files.js)}
    {section name=i loop=$additional_files.js}
        <script type="text/javascript" src="{if substr($additional_files.js[i], 0, 2) != '//'}{$smarty.const.BASE_URL}{/if}{$additional_files.js[i]}"></script>
    {/section}
{/if}
</head>
<body ng-app="bruinCard">
	{$content_for_layout}
	<input type="hidden" id="BASE_URL" value="{$smarty.const.BASE_URL}" />
</body>
</html>
