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
                <select id="list-selector-import" data-list="import" name="list" class="list-selector">
                    <option value="0"> - </option>
                    {foreach from=$mailchimp_list item=list}
                    <option value="{$list.id}">{$list.name}</option>
                    {/foreach}
                </select>
            </div>
            <div class="input-holder">
                <label>{l s='Attach fields' mod='minicmailchimp'}:</label>
                {foreach from=$mailchimp_list key=k item=list}
                {if isset($list.fields)}
                    <div id="import-{$list.id}" class="fields-holder">
                        {foreach from=$list.fields item=field}
                            {if $field.tag != 'EMAIL'}
                            <div style="clear:both;">
                                <span>{$field.name}</span>
                                <select name="fields[{$list.id}][{$field.tag}]">
                                    <option value="0"> - </option>
                                    <option value="firstname">{l s='First Name' mod='minicmailchimp'}</option>
                                    <option value="lastname">{l s='Last Name' mod='minicmailchimp'}</option>
                                </select>
                                {if $field.req}<b>{l s='required' mod='minicmailchimp'}</b>{/if}
                                {if $field.show == false}<b>{l s='hidden' mod='minicmailchimp'}</b>{/if}
                            </div>
                            {/if}
                        {/foreach}
                    </div>
                {/if}
                {/foreach}
            </div>
            <div class="switch-holder inline">
                <label for="">{l s='Confirmation email'}:</label>
                <div class="switch small inactive">
                    <input type="radio" class="" name="optin"  value="0" checked="true" />
                </div>
                <p style="clear:both;">{l s='Turn on if you wish to send confirmation email for customers after import is done.' mod='minicmailchimp'}</p>
            </div>
            <div class="switch-holder inline">
                <label for="">{l s='Update if exists'}:</label>
                <div class="switch small inactive">
                    <input type="radio" class="" name="update_users"  value="0" checked="true" />
                </div>
                <p style="clear:both;">{l s='Turn on if you wish to update the record if alredy exists. If you dont you\'ll receive a warning for those customers.' mod='minicmailchimp'}</p>
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