<div id="form" class="minic-container">
	<form id="form-feed" class="" method="post" action="{$minic.form_action}">
        <div class="minic-top">
            <h3>{l s='Signup form settings' mod='minicmailchimp'}
                <!-- <a href="#" target="_blank" class="help">{l s='help & tips' mod='minicmailchimp'}</a> -->
            </h3>
            <a href="#form" class="minic-close">x</a>
        </div>
        <div class="minic-content">
            <div class="input-holder">
                <label for="">{l s='Block title' mod='minicmailchimp'}:</label>
                {foreach from=$minic.languages item=language}
                    <div id="title_{$language.id_lang}" style="display: {if $language.id_lang == $minic.default_lang}block{else}none{/if};">
                        <input type="text" name="title_{$language.id_lang}" value="{if $minic.form.data.{$language.id_lang}.title}{$minic.form.data.{$language.id_lang}.title}{/if}">
                    </div>
                {/foreach}
                {$minic.flags.title}
                <p style="float: left; clear: both;">{l s='Leave it empty if you do not want to appear on the front of the site.' mod='minicmailchimp'}</p>
            </div>
            <div class="input-holder">
                <label>{l s='Choose where to show the form' mod='minicmailchimp'}:</label>
                <select multiple name="hooks[]" id="">
                    {foreach from=$minic.hooks item=hook}
                    <option value="{$hook}" {if $minic.form.hooks && in_array($hook, $minic.form.hooks)}selected{/if}>{$hook}</option>
                    {/foreach}
                </select>
                <p>{l s='Hold down the CTRL to select multiple.' mod='minicmailchimp'}</p>
            </div>
            <div class="input-holder">
                <label>{l s='Insert the form code here' mod='minicmailchimp'}:</label>
                {foreach from=$minic.languages item=language}
                    <div id="form_{$language.id_lang}" style="display: {if $language.id_lang == $minic.default_lang}block{else}none{/if};">
                        <textarea name="form_{$language.id_lang}">{if $minic.form.data.{$language.id_lang}.form}{$minic.form.data.{$language.id_lang}.form}{/if}</textarea>
                    </div>
                {/foreach}
                {$minic.flags.form}
            </div>
            <div class="minic-comments"> 
                <h3>{l s='How to get the form code' mod='minicmailchimp'}</h3>
                <ol style="list-style: decimal;">
                    <li>{l s='Log in into' mod='minicmailchimp'} <a href="https://login.mailchimp.com/" target="_blank">Mailchimp</a>.</li>
                    <li>{l s='Go to the lists, and select a list where you want the subscribers to subscribe.' mod='minicmailchimp'}</li>
                    <li>{l s='Search the "For Your Website" dropwdown menu and select the "Signup Form Embed Code".' mod='minicmailchimp'}</li>
                    <li>{l s='Configure your form to serve your needs.' mod='minicmailchimp'}</li>
                    <li>{l s='Click the "Create Embed Code" button, and copy the HTML code below.' mod='minicmailchimp'}</li>
                    <li>{l s='Paste the code into the textfield.' mod='minicmailchimp'}</li>
                </ol>
                <h3>{l s='Important' mod='minicmailchimp'}</h3>
                <p>{l s='If you wish to use the module properly and you have multiple languages enabled, then do not forget to change the titles and other texts in the form code (if you do not understand the HTML code you can do it when you configure the form on the Mailchimp website, just repeat the steps above and change the titles).' mod='minicmailchimp'}</p>
            </div>
        </div>
        <div class="minic-bottom">
            <input type="submit" name="submitForm" class="button-large green" value="{l s='Save' mod='minicmailchimp'}" />
            <a href="#form" class="minic-close button-large lgrey">{l s='Close' mod='minicmailchimp'}</a>
        </div>
	</form>
</div>