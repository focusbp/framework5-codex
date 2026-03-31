

<div class="edit_100">
	<h4>{t key="db.fields_settings"}</h4>
	<div class="edit_left">
		<div id="parameters_area">
			{include file="_fields.tpl"}
		</div>
	</div>


		{if $screen == null}
			<div class="edit_right">
			<h6>{t key="db.select_screen"}</h6>
			{html_options name="screen_id" options=$screen_opt id="dropdown_screen"}
		{else}
			<div class="edit_right" style="border: 3px #4ba3ff solid;">
			<h6>{$screen.screen_name}</h6>
			<input type="hidden" name="screen_id" value="{$screen.id}" id="dropdown_screen">
		{/if}

		<script>
			var dropdown_screen_event = function () {
				let screen_id = $("#dropdown_screen").val();
				$("#set_all").data("screen_id",screen_id);
				let fd = new FormData();
				fd.append("screen_id", screen_id);
				fd.append("class", "db");
				fd.append("function", "screen_fields_area");
				appcon("app.php", fd);
			}
			$("#dropdown_screen").on("change", function () {
				dropdown_screen_event();
			});
			dropdown_screen_event();
		</script>

		<div id="screen_fields_area"></div>
		<a id="set_all" class="ajax-link" invoke-class="db" invoke-function="set_all_field" data-id="{$data.id}" style="text-align: right;display: block;color: #1974d2;text-decoration: underline;">{t key="db.set_all"}</a>


	</div>
</div>
