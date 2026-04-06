<form onsubmit="return false;" id="orders_picker_test_form">
	<p style="margin:0 0 12px 0;color:#475569;">{t key="orders_picker_test.description"}</p>

	<div style="margin-bottom:14px;">
		<p style="font-weight:bold;margin:0 0 6px 0;">{t key="orders_picker_test.target"}</p>
		<select name="picker_target" id="orders_picker_target">
			<option value="">{t key="orders_picker_test.select_placeholder"}</option>
			<option value="date">Datepicker</option>
			<option value="time">Timepicker</option>
			<option value="year_month">Year Month Picker</option>
			<option value="all">{t key="orders_picker_test.show_all"}</option>
		</select>
	</div>

	<div class="orders_picker_test_field orders_picker_test_date" style="display:none;margin-bottom:14px;">
		<p style="font-weight:bold;margin:0 0 6px 0;">Datepicker</p>
		<input type="text" name="test_date" class="datepicker" placeholder="日付を選択">
	</div>

	<div class="orders_picker_test_field orders_picker_test_time" style="display:none;margin-bottom:14px;">
		<p style="font-weight:bold;margin:0 0 6px 0;">Timepicker</p>
		<input type="text" name="test_time" class="timepicker" placeholder="時間を選択">
	</div>

	<div class="orders_picker_test_field orders_picker_test_year_month" style="display:none;margin-bottom:14px;">
		<p style="font-weight:bold;margin:0 0 6px 0;">Year Month Picker</p>
		<input type="text" name="test_year_month" class="year_month_picker" placeholder="年月を選択">
	</div>

	<div style="display:flex;justify-content:flex-end;">
		<button type="button" class="ajax-link" invoke-function="close_dialog">{t key="common.close"}</button>
	</div>
</form>
