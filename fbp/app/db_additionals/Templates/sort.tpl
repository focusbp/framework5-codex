
<p style="margin-bottom:20px;">{t key="db_additionals.sort_help"}</p>

<div id="button_sort">
	{foreach $additionals as $a}
		<div class="ajax-link lang sort_button" id="{$a.id}" data-class="{$a.class_name}" data-function="{$a.function_name}" >{$a.button_title}</div>
	{/foreach}
</div>


<script>


	$("#button_sort").sortable({
		axis: "y",
  distance: 0,              // すぐ反応
  delay: 0,                 // 念のため
  tolerance: 'pointer',     // 体感が軽い
  helper: 'clone',          // 元DOMを触らず軽い
		update: function () {
			var log = $(this).sortable("toArray");
			var fd = new FormData();
			fd.append("class", "db_additionals");
			fd.append("function", "button_sort_exe");
			fd.append("tb_name","{$tb_name|escape}");
			fd.append("target_area","{$target_area|escape}");
			fd.append("reload_db_id","{$reload_db_id}");
			fd.append("place","{$place}")
			fd.append("log", log);
			appcon("app.php", fd);
		}
	});
</script>
