<div id="settings" class="minic-container" {if !$minic.settings.apikey}style="display:block;"{/if}>{debug}
	<form id="form-feed" class="" method="post" action="{$minic.form_action}">
        <div class="minic-top">
            <h3>{l s='Settings' mod='minicmailchimp'}
                <a href="#" target="_blank" class="help">{l s='help & tips' mod='minicmailchimp'}</a>
            </h3>
            <a href="#settings" class="minic-close">x</a>
        </div>
        <div class="minic-content">
    		<div class="input-holder">
    			<label>{l s='Mailchimp API Key' mod='minicmailchimp'}:</label>
    			<input id="settings-key" class="apikey" type="text" name="apikey" value="{if $minic.settings.apikey}{$minic.settings.apikey}{/if}" />
    		</div>
            <div class="switch-holder inline">
                <label>{l s='Use SSL'}: </label>
                <div class="switch small {if isset($minic.settings.ssl) && $minic.settings.ssl}active{else}inactive{/if}">
                    <input type="radio" class="" name="ssl"  value="{if isset($minic.settings.ssl) && $minic.settings.ssl}1{else}0{/if}" checked="true" />
                </div>
            </div>
            <div class="switch-holder inline">
                <label>{l s='Sync new registration'}: </label>
                <div class="switch small {if isset($minic.settings.registration) && $minic.settings.registration}active{else}inactive{/if}">
                    <input type="radio" class="" name="registration"  value="{if isset($minic.settings.registration) && $minic.settings.registration}1{else}0{/if}" checked="true" />
                </div>
            </div>
            <div class="minic-comments"> 
                <h3>{l s='How to get your API key' mod='minicmailchimp'}</h3>
                <p>{l s='If you alredy have an API key click'} <a href="https://us6.admin.mailchimp.com/account/api-key-popup/" target="_blank">{l s='here' mod='minicmailchimp'}</a>, {l s='otherwise you can get a new one from'} <a href="https://us6.admin.mailchimp.com/account/api/" target="_blank">{l s='here' mod='minicmailchimp'}</a></p>
                <h3>{l s='Tutorial to get an API key' mod='minicmailchimp'}</h3>
                <p>{l s='You can find more info'} <a href="http://kb.mailchimp.com/article/where-can-i-find-my-api-key" target="_blank">{l s='here' mod='minicmailchimp'}</a></p>
            </div>
        </div>
        <div class="minic-bottom">
            <input type="submit" name="submitSettings" class="button-large green" value="{l s='Save' mod='minicmailchimp'}" />
            <a href="#settings" class="minic-close button-large lgrey">{l s='Close' mod='minicmailchimp'}</a>
        </div>
	</form>
</div>