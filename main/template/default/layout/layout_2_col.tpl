{extends file="default/layout/main.tpl"}

{block name=header}
    {if $show_header == 1 }
        {include file="default/layout/main_header.tpl"}
    {/if}
{/block}

{block name=body}	

	{* Main content*}
	
	{if $show_sniff == 1 }
	 	{include file="default/layout/sniff.tpl"}
	{/if}
	
	<div class="span9">
		
		{* Course plugin block*}
        <section id="courses_plugin">
		{$plugin_courses_block}
        </section>
		
		{* ?? *}
        <section id="home_page">
		{$home_page_block}
        </section>
		
		{* ?? *}
		{$sniff_notification}
		
		{* Show messages*}
        <section id="messages">
		{$message}
        </section>
		
		{* Main content*}
        <section id="main_content">
		{$content}
        </section>
		
		{* Announcements *}
        <section id="announcements_page">
		{$announcements_block}
        </section>
		
		{* Hot courses template *}
		<section id="hot_courses">
		{include file="default/layout/hot_courses.tpl"}
        </section>
	</div>
		
	{* Right column *}
	<div class="span3">
		
	    {*if user is not login show the login form*}
		{if $_u.logged == 0}
			{include file="default/layout/login_form.tpl"}
		{/if}

		{* My account - user picture *}
		{$profile_block}			
		{$account_block}		
		{$teacher_block}
		
		{* Notices *}
		{$notice_block}
		
		{* Links that are not added in the tabs*}
		{$navigation_course_links}
		
		{* Plugin courses sidebar *}
		{$plugin_courses_right_block}
		
		{* Reservation block *}
		{$reservation_block}
		
		{* Search (xapian)*}
		{$search_block}
		
		{* Classes *}
		{$classes_block}
		
		{* Skills*}
		{$skills_block}
	</div>
{/block}

{* Footer *}
{block name=footer}
    {if $show_footer == 1 }
        {include file="default/layout/main_footer.tpl"}
    {/if}
{/block}