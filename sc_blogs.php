<?php
//Script made by BlueaKaKev //
$qry = safe_query("SELECT * FROM ".PREFIX."user_blog WHERE blogID!=0 ORDER BY blogID DESC LIMIT 0,5");
$anz = mysqli_num_rows($qry);
if ($anz) {
	echo '<ul class="list-group">';

	while($blog = mysqli_fetch_array($qry)) {
		$blogID = $blog['blogID'];
		$blogh = $blog['headline'];
		$visits = $blog['visits'];
		$date = date("d.m.y", $blog['date']);

		$data_array = array();
		$data_array['$date'] = $date;
		$data_array['$visits'] = $visits;
		$data_array['$blogh'] = shortenText($blogh, 18);
		$data_array['$blogID'] = $blogID;
		$sc_blog = $GLOBALS["_template"]->replaceTemplate("sc_blogs", $data_array);
		echo $sc_blog;

	}
	echo '</ul>';
}
else {
	echo 'Keine Blogeinträge gefunden';
}
?>