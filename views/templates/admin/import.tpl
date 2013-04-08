<div id="import" class="minic-container">
	<form id="form-feed" class="" method="post" action="{$minic.form_action}">
        <div class="minic-top">
            <h3>{l s='Import users' mod='minicmailchimp'}
                <a href="#" target="_blank" class="help">{l s='help & tips' mod='minicmailchimp'}</a>
            </h3>
            <a href="#import" class="minic-close">x</a>
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
            <div class="switch-holder inline">
                <label for="">{l s='Import all Customers'}:</label>
                <div class="switch small inactive">
                    <input type="radio" class="" name="all-user"  value="0" checked="true" />
                </div>
                <p style="clear:both;">{l s='Turn on if you wish to import all of the users, not just the subscribers.' mod='minicmailchimp'}</p>
            </div>
        </div>
        <div class="minic-bottom">
            <input type="submit" name="submitImport" class="button-large green" value="{l s='Import' mod='minicmailchimp'}" />
            <a href="#import" class="minic-close button-large lgrey">{l s='Close' mod='minicmailchimp'}</a>
        </div>
	</form>
</div>