{extends file="default/layout/main.tpl"}

{block name=header}
	{if $show_header == 1}
		{include file="default/layout/header.tpl"}
	{/if}	
{/block}

{block name=body}
	{if (!empty($actions) ) }
		<div class="actions">
		{$actions}	
		</div>
	{/if}
	{$message}
	{$content}
{/block}

{block name=footer}
	{if $show_header == 1}
		{include file="default/layout/footer.tpl"}
	{/if}	
{/block}
