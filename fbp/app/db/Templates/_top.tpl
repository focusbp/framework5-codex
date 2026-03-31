<h2 style="display: block;float:left;width:auto;">{$data.tb_name}</h2>

{if $show_reload_button}
<button class="ajax-link lang" invoke-class="db_exe" invoke-function="reload" data-db_id="{$data.id}" data-child="{$child}" data-parent_id="{$parent_id}" style="margin-top:10px;">{t key="db.reload"}</button>
{/if}
