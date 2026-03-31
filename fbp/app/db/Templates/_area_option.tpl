		{assign var=option_selected value=$data.constant_array_name|default:$post.constant_array_name}
		{assign var=display_fields_value value=$data.display_fields_for_dropdown|default:$post.display_fields_for_dropdown}
		<p class="lang">{t key="db.options"}:</p>
		{html_options name="constant_array_name" output=$constant_array_opt values=$constant_array_opt selected=$option_selected}
		<button class="ajax-link" invoke-function="reload_option" style="display: inline;">{t key="db.refresh_dropdown"}</button>&nbsp;<button class="ajax-link" invoke-function="open_options_dialog" style="display: inline;">{t key="db.edit_dropdown_options"}</button>

		<div id="display_fields_for_dropdown_area" style="margin-top:8px; display:none;">
			<p class="lang">{t key="db.display_fields_for_dropdown"}:</p>
			<input type="text" name="display_fields_for_dropdown" value="{$display_fields_value}">
			<div id="display_fields_for_dropdown_candidates" style="margin-top:6px;line-height:1.8; display:none;">
				{foreach $dropdown_display_field_candidates as $table_name => $field_candidates}
					<div class="display-fields-candidate-group" data-table-name="{$table_name|escape}" style="display:none;">
						{foreach $field_candidates as $field_name => $field_label}
							<a href="#" class="display-fields-token" data-token="{$field_name|escape}" style="margin-right:8px;">{$field_label|escape}</a>
						{/foreach}
					</div>
				{/foreach}
			</div>
			<p class="error_message lang error_display_fields_for_dropdown"></p>
		</div>

		{literal}
		<script>
			(function(){
				function should_show_display_fields(val){
					if(!val){ return false; }
					if(val.indexOf("table/") !== 0){ return false; }
					return val.split("/").length === 2;
				}
				function get_table_name(val){
					if(!should_show_display_fields(val)){ return ""; }
					return val.substring(6);
				}
				var $select = $('select[name="constant_array_name"]');
				if($select.length === 0){ return; }
				var $displayFieldsArea = $("#display_fields_for_dropdown_area");
				var $candidateArea = $("#display_fields_for_dropdown_candidates");
				var $input = $('input[name="display_fields_for_dropdown"]');
				var toggle = function(){
					var v = $select.val() || "";
					if(should_show_display_fields(v)){
						$displayFieldsArea.show();
						var tableName = get_table_name(v);
						var $groups = $(".display-fields-candidate-group");
						$groups.hide();
						var $group = $('.display-fields-candidate-group[data-table-name="' + tableName + '"]');
						if($group.length > 0){
							$candidateArea.show();
							$group.show();
						}else{
							$candidateArea.hide();
						}
					}else{
						$displayFieldsArea.hide();
						$candidateArea.hide();
						$(".display-fields-candidate-group").hide();
					}
				};
				$select.on("change", toggle);
				$(".display-fields-token").on("click", function(e){
					e.preventDefault();
					var token = "{" + "$" + $(this).data("token") + "}";
					$input.val(($input.val() || "") + token);
					$input.trigger("focus");
				});
				toggle();
			})();
		</script>
		{/literal}
