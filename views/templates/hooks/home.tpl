<!-- minicmailchimp -->
<div id="minic_mailchimp" class="block {$mailchimp_form.class}">
	{if $mailchimp_form.title}
		<p class="title_block" style="margin-bottom: 10px;">{$mailchimp_form.title}</p>
	{/if}
	{$mailchimp_form.form|unescape:"html"}
</div>
<!-- end minicmailchimp -->