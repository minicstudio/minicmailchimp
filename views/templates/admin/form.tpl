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
                <label>{l s='Choose where to show the form' mod='minicmailchimp'}:</label>
                <select multiple name="hooks[]" id="">
                    {foreach from=$minic.hooks item=hook}
                    <option value="display{$hook}">{$hook}</option>
                    {/foreach}
                </select>
                <p>{l s='Hold down the CTRL to select multiple.' mod='minicmailchimp'}</p>
            </div>
            <div class="input-holder">
                <label>{l s='Insert the form code here' mod='minicmailchimp'}:</label>
                <textarea name="form"></textarea>
            </div>
            <div class="minic-comments"> 
                <h3>{l s='How to get the form code' mod='minicmailchimp'}</h3>
                <ol style="list-style: decimal;">
                    <li>{l s='Log in into' mod='minicmailchimp'} <a href="https://login.mailchimp.com/" target="_blank">Mailchimp</a></li>
                    <li>step 2.</li>
                </ol>
            </div>
        </div>
        <div class="minic-bottom">
            <input type="submit" name="submitForm" class="button-large green" value="{l s='Save' mod='minicmailchimp'}" />
            <a href="#form" class="minic-close button-large lgrey">{l s='Close' mod='minicmailchimp'}</a>
        </div>
	</form>
</div>