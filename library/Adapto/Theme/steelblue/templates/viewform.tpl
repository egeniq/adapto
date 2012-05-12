    <table cellpadding="3">

      <tr>
        <td valign="top">
          <table>
				  {foreach from=$fields item=field}
			    {if $field.column != 1}
				    <tr{if $field.rowid != ""} id="{$field.rowid}"{/if}{if !$field.initial_on_tab} style="display: none"{/if} class="{$field.class}">
				      {if isset($field.line)}
				        <td colspan="2" valign="top" class="field">{$field.line}</td>
				      {else}
				        {if $field.label!=="AF_NO_LABEL"}<td valign="top" class="fieldlabel">{if $field.label!=""}<b>{$field.label}:</b> {/if}</td>{/if}
				        <td valign="top" class="field" {if $field.label==="AF_NO_LABEL"}colspan="2"{/if}>{$field.full}</td>
				      {/if}
				    </tr>
			    {/if}
				  {/foreach}
          </table>
        </td>
        <td valign="top">
          <table>
          {foreach from=$fields item=field}
			    {if $field.column == 1}
			      <tr{if $field.rowid != ""} id="{$field.rowid}"{/if}{if !$field.initial_on_tab} style="display: none"{/if} class="{$field.class}">
			        {if isset($field.line)}
			          <td colspan="2" valign="top" class="field">{$field.line}</td>
			        {else}
			          {if $field.label!=="AF_NO_LABEL"}<td valign="top" class="fieldlabel">{if $field.label!=""}<b>{$field.label}:</b> {/if}</td>{/if}
			          <td valign="top" class="field" {if $field.label==="AF_NO_LABEL"}colspan="2"{/if}>{$field.full}</td>
			        {/if}
			      </tr>
			    {/if}
			    {/foreach}
          </table>
        </td>
      </tr>
      
    </table>