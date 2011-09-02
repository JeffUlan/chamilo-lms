{extends file="default/layout/main.tpl"}

{block name=header}
	{include file="default/layout/header.tpl"}	
{/block}

{block name=body}	
	<div id="maincontent" class="maincontent">
		{$plugin_courses_block}
		{$content}
	</div>
	
	<div id="menu-wrapper">	
		{$profile_block}	
		{$account_block}
		{$teacher_block}
		{$navigation_course_links}
		{$plugin_courses_right_block}
		{$reservation_block}
		{$search_block}
		{$classes_block}
	</div>
{/block}

{block name=footer}
	{include file="default/layout/footer.tpl"}	
{/block}