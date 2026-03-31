<form id="form_{$timestamp}">

	<input type="hidden" name="id" value="{$data.id}">

	<div>
		<p class="lang">{t key="cron.title_help"}</p>
		<input type="text" name="title" value="{$data.title}">
	</div>

	<div>
		<p class="lang">{t key="common.class_name"}</p>
		<input type="text" name="class_name" value="{$data.class_name}">
	</div>

	<div>
		<p class="lang">{t key="cron.handler_function"}</p>
		<input type="text" name="function_name" value="{$data.function_name}">
	</div>
	
				<div>
					
					<p class="lang">{t key="cron.schedule"}</p>
					<div class="cron-templates">
						<span>{t key="cron.sample"} : </span>
						<a href="#" class="cron-template" data-template="daily-7" class="lang">{t key="cron.template_daily"}</a> /
						<a href="#" class="cron-template" data-template="monthly-1-6" class="lang">{t key="cron.template_monthly"}</a> /
						<a href="#" class="cron-template" data-template="weekly-mon-8" class="lang">{t key="cron.template_weekly"}</a>
					</div>
					
					<table>
						<tr>
							<td class="lang cron_table_title">
								{t key="cron.min"}<br>
							</td>
							<td>
								{html_checkboxes name="min" options=$min_opt selected=$data.min separator=" "}
							</td>
						</tr>
						<tr>
							<td class="lang cron_table_title">
								{t key="cron.hour"}<br>
							</td>
							<td>
								{html_checkboxes name="hour" options=$hour_opt selected=$data.hour separator=" "}
							</td>
						</tr>
						<tr>
							<td class="lang cron_table_title">
								{t key="cron.day"}<br>
							</td>
							<td>
								{html_checkboxes name="day" options=$day_opt selected=$data.day separator=" "}
							</td>
						</tr>
						<tr>
							<td class="lang cron_table_title">
								{t key="cron.month"}<br>
							</td>
							<td>
								{html_checkboxes name="month" options=$month_opt selected=$data.month separator=" "}
							</td>
						</tr>
						<tr>
							<td class="lang cron_table_title">
								{t key="cron.weekday"}<br>
							</td>
							<td>
								{html_checkboxes name="weekday" options=$weekday_opt selected=$data.weekday separator=" "}
							</td>
						</tr>
					</table>
				</div>
							


	<div>
		<button class="ajax-link lang" data-form="form_{$timestamp}" data-class="{$class}" data-function="edit_exe">{t key="common.update"}</button>
	</div>
</form>


<script>


	$(function () {


		// ===== 追加：テンプレート適用 =====

		// 汎用：指定したフィールド(min/hour/...)に値をセットする
		function setCronField(name, values) {
			var selector = "input[type='checkbox'][name='" + name + "[]']";
			var $inputs = $(selector);

			// 一旦クリア（未選択＝*）
			$inputs.prop("checked", false);

			// values が null なら何も選ばない（＝* のまま）
			if (!values || !values.length) {
				return;
			}

			// 指定された値だけチェック
			$inputs.each(function () {
				var v = $(this).val();
				if (values.indexOf(v) !== -1) {
					$(this).prop("checked", true);
				}
			});
		}

		// テンプレートリンクのクリックイベント
		$("body").on("click", ".cron-template", function (e) {
			e.preventDefault();

			var tmpl = $(this).data("template");

			if (tmpl === "daily-7") {
				// 毎日 7:00
				// → 0分 / 7時 / day, month, weekday は *
				setCronField("min", ["0"]);
				setCronField("hour", ["7"]);
				setCronField("day", null);
				setCronField("month", null);
				setCronField("weekday", null);

			} else if (tmpl === "monthly-1-6") {
				// 毎月1日 6:00
				// → 0分 / 6時 / day=1 / month, weekday は *
				setCronField("min", ["0"]);
				setCronField("hour", ["6"]);
				setCronField("day", ["1"]);
				setCronField("month", null);
				setCronField("weekday", null);

			} else if (tmpl === "weekly-mon-8") {
				// 毎週月曜日 8:00
				// → 0分 / 8時 / weekday=Mon
				//   weekday の値が 0:Sun,1:Mon,...6:Sat 前提
				setCronField("min", ["0"]);
				setCronField("hour", ["8"]);
				setCronField("day", null);
				setCronField("month", null);
				setCronField("weekday", ["1"]);
			}
		});

	});

</script>
