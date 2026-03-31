<div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
	<div>
		<p style="font-size:13px;color:#4b5563;margin:0;">{t key="wizard.home.description"}</p>
	</div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:10px;max-height:520px;overflow:auto;padding-right:4px;">
		{foreach $wizard_groups as $g}
			{if $g.enabled == 1}
			<div class="ajax-link" invoke-function="{$g.button_function|escape}" style="position:relative;border:1px solid #d7deea;border-radius:10px;background:#fff;padding:12px;box-shadow:0 1px 2px rgba(0,0,0,0.03);overflow:hidden;cursor:pointer;transition:border-color 0.15s ease, box-shadow 0.15s ease, border-width 0.15s ease;" onmouseover="this.style.borderColor='#facc15';this.style.borderWidth='3px';this.style.boxShadow='0 4px 12px rgba(250,204,21,0.22)';" onmouseout="this.style.borderColor='#d7deea';this.style.borderWidth='1px';this.style.boxShadow='0 1px 2px rgba(0,0,0,0.03)';">
		{else}
			<div style="position:relative;border:1px solid #d7deea;border-radius:10px;background:#fff;padding:12px;box-shadow:0 1px 2px rgba(0,0,0,0.03);overflow:hidden;opacity:0.78;">
		{/if}
			{if $g.icon_path|default:'' != ''}
				<img src="{$g.icon_path|escape}" alt="" style="position:absolute;top:10px;right:10px;width:80px;height:80px;object-fit:contain;opacity:0.92;pointer-events:none;">
			{/if}
			<p style="font-weight:bold;margin:0 0 8px 0;font-size:14px;">{$g.title|escape}</p>
			<ul style="margin:0 0 12px 16px;padding:0;font-size:12px;color:#334155;line-height:1.6;">
				{foreach $g.items as $item}
					<li>{$item.label|escape}</li>
				{/foreach}
			</ul>
			{if $g.enabled != 1}
				<p style="margin:0;color:#64748b;font-size:12px;">{$g.button_label|escape}</p>
			{/if}
		</div>
	{/foreach}
</div>
