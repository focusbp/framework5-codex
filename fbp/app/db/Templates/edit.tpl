<div id="tabs">
  <ul>
    <li><a href="#tabs-1">{t key="db.fields_settings"}</a></li>
    <li><a href="#tabs-2">{t key="db.table_settings"}</a></li>
    <li><a href="#tabs-3">{t key="db.edit_tab.button_sort"}</a></li>
  </ul>
  <div id="tabs-1">
	  {include file="_edit_field.tpl"}
  </div>
	
  <div id="tabs-2">
	  {include file="_edit_table.tpl"}
  </div>
  <div id="tabs-3">
	  <div id="db_additionals_area"></div>
  </div>

</div>

<script>
  $(function () {

    var STORAGE_KEY = 'tabs_active_index_table_settings'; // ページごとにユニークなキーに

    // 常に Fields Settings タブ（index 0）を初期表示にする
    var activeIndex = 0;

    // タブ初期化（active に保存値を指定）
    $("#tabs").tabs({
      active: activeIndex,
      activate: function (event, ui) {
        // 現在アクティブなタブindexを保存
        var current = $("#tabs").tabs("option", "active");
        window.localStorage.setItem(STORAGE_KEY, String(current));
		
		$("select").each(function (index, element) {
			ensure_original_searchable_select($(this));
		});
		
      }
    });

    // タブ内リンク（.open-tab）から切り替えた場合も保存
    $("body").on("click", ".open-tab", function (e) {
      e.preventDefault();
      var idx = parseInt($(this).data("tab"), 10); // 0-based index
      if (!isNaN(idx)) {
        $("#tabs").tabs("option", "active", idx);
        window.localStorage.setItem(STORAGE_KEY, String(idx));
      }
    });

  });
</script>
