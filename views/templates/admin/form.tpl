<div id="form" class="minic-container">
	<form id="form-feed" class="" method="post" action="{$minic.form_action}">
        <div class="minic-top">
            <h3>{l s='Subscription form settings' mod='minicmailchimp'}
                <a href="#" target="_blank" class="help">{l s='help & tips' mod='minicmailchimp'}</a>
            </h3>
            <a href="#form" class="minic-close">x</a>
        </div>
        <div class="minic-content">
	        <div class="input-holder">
                <label>{l s='Choose the list to import' mod='minicmailchimp'}:</label>
                <select name="list">
                    {foreach from=$mailchimp_list.data item=list}
                    <option value="{$list.id}">{$list.name}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="minic-bottom">
            <input type="submit" name="submitImport" class="button-large green" value="{l s='Save' mod='minicmailchimp'}" />
            <a href="#form" class="minic-close button-large lgrey">{l s='Close' mod='minicmailchimp'}</a>
        </div>
	</form>
</div>