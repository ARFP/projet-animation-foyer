<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly

$app = 'sh4-rest';

if( isset($_POST[$app . '_submit']) ){
	if( isset($_POST[$app]) ){
		foreach( (array)$_POST[$app] as $key => $value ){
			$option_name = $app . '_' . $key;
			$value = sanitize_text_field( $value );
			update_option( $option_name, $value );
		}
	}

	if( ! isset($_POST[$app]['enabled']) ){
		$k = $app . '_enabled';
		$value = 0;
		update_option( $k, $value );
	}
}

// $this->initOption();

$current = array();
$current['enabled'] = get_option( $app . '_enabled', 1 );
$current['auth_code'] = get_option( $app . '_auth_code', '' );

$startUrl = '/shiftcontroller/v4/';

// spaghetti starts here
?>

<form method="post" action="">
	<?php settings_fields( $app ); ?>
	<?php //do_settings_sections( $this->app ); ?>

	<label>
		<?php $checked = $current['enabled'] ? ' checked' : ''; ?>
		<input type="checkbox" name="<?php echo $app; ?>[enabled]" value="1" <?php echo $checked; ?>/>
		Enable
	</label>
	</br/>

	<label>
		AuthCode<br>
		<input type="text" name="<?php echo $app; ?>[auth_code]" value="<?php echo esc_attr( $current['auth_code'] ); ?>" />
	</label>
	</br/>

	<p>
		<input name="<?php echo $app; ?>_submit" type="submit" class="button-primary" value="Save" />
	</p>
</form>

<?php if( ! $current['enabled'] ) return; ?>


<style>
.sh4-code {
display: block; padding: 1em; border: #999 1px solid; margin-bottom: 1em;
}
</style>

<h3 class="hc-underline">Get Shifts</h3>
<?php
$url = $startUrl . 'shifts';
$fullUrl = get_rest_url( NULL, $url );
?>

<p>
<strong>
GET <?php echo $url; ?>
</strong>
</p>

<p>
<strong>__Headers__</strong>
</p>

<p>
X-WP-ShiftController-AuthCode: <?php echo $current['auth_code']; ?>
</p>

<p>
<strong>__Arguments__</strong>
</p>

<p>
<strong>calendar_id</strong> (__Calendar Id__)<br>
<strong>employee_id</strong> (__Employee Id__)<br>
<strong>from</strong> (__From Date__, YYYYMMDD / __From Date Time__, YYYYMMDDHHMM)<br>
<strong>to</strong> (__To Date__, YYYYMMDD / __To Date Time__, YYYYMMDDHHMM)<br>
<strong>status_id</strong> (publish, draft)<br>
</p>

<?php if( class_exists('SH4_CFields_Model') ) : ?>
<?php
$f = ShiftController4::$instance->root();
$cfieldsQuery = $f->make('SH4_CFields_Query');
$cfields = $cfieldsQuery->read();
?>
<?php if( $cfields ) : ?>
<p>
<strong>__Optional Arguments__</strong>
</p>

<p>
<?php foreach( $cfields as $cfield ) : ?>
<em><?php echo $cfield->getName(); ?></em> (<?php echo $cfield->getLabel(); ?>)<br>
<?php endforeach; ?>
</p>
<?php endif; ?>
<?php endif; ?>

<?php
$today = date( 'Ymd' );
?>

<p>
<strong>__Examples__</strong>
</p>

<pre class="sh4-code">
GET <?php echo $fullUrl; ?>
</pre>

<pre class="sh4-code">
GET <?php echo $fullUrl; ?>?calendar_id=11&from=<?php echo $today; ?>
</pre>

<pre class="sh4-code">
GET <?php echo $fullUrl; ?>?from=<?php echo $today; ?>0900&to=<?php echo $today; ?>1430&status_id=draft
</pre>


<h3 class="hc-underline">__Get Shift__</h3>
<?php
$url = $startUrl . 'shifts/&lt;id&gt;';
$fullUrl = get_rest_url( NULL, $url );

$url2 = $startUrl . 'shifts';
$fullUrl2 = get_rest_url( NULL, $url2 );
?>

<p>
<strong>
GET <?php echo $url; ?>
</strong>
</p>

<p>
<strong>__Headers__</strong>
</p>

<p>
X-WP-ShiftController-AuthCode: <?php echo $current['auth_code']; ?>
</p>

<p>
<strong>__Examples__</strong>
</p>

<pre class="sh4-code">
GET <?php echo $fullUrl2; ?>/123
</pre>

<h3 class="hc-underline">__Delete Shift__</h3>
<?php
$url = $startUrl . 'shifts/&lt;id&gt;';
$fullUrl = get_rest_url( NULL, $url );

$url2 = $startUrl . 'shifts';
$fullUrl2 = get_rest_url( NULL, $url2 );
?>

<p>
<strong>
DELETE <?php echo $url; ?>
</strong>
</p>

<p>
<strong>__Headers__</strong>
</p>

<p>
X-WP-ShiftController-AuthCode: <?php echo $current['auth_code']; ?>
</p>

<p>
<strong>__Examples__</strong>
</p>

<pre class="sh4-code">
DELETE <?php echo $fullUrl2; ?>/123
</pre>

<h3 class="hc-underline">__Create Shift__</h3>
<?php
$url = $startUrl . 'shifts';
$fullUrl = get_rest_url( NULL, $url );
?>

<p>
<strong>
POST <?php echo $url; ?>
</strong>
</p>

<p>
<strong>__Headers__</strong>
</p>

<p>
X-WP-ShiftController-AuthCode: <?php echo $current['auth_code']; ?>
</p>

<p>
<strong>__Arguments__</strong>
</p>

<p>
<strong>calendar_id</strong> (__Calendar Id__)<br>
<strong>employee_id</strong> (__Employee Id__)<br>
<strong>start</strong> (__Start Date Time__, YYYYMMDDHHMM)<br>
<strong>end</strong> (__End Date Time__, YYYYMMDDHHMM)<br>
<strong>status_id</strong> (publish, draft)<br>
<strong>conflict</strong> (__Set to 1 to allow creation of shifts with conflicts__) <em>__optional__</em><br>
</p>

<p>
<strong>__Examples__</strong>
</p>

<pre class="sh4-code">
POST <?php echo $fullUrl; ?>

calendar_id: 11
employee_id: 22
start: <?php echo $today; ?>0800
end: <?php echo $today; ?>1430
status_id: publish
</pre>

<p>
__If success, it returns the id of the new shift. Otherwise error messages will be given.__
</p>

<h3 class="hc-underline">__Update Shift__</h3>
<?php
$url = $startUrl . 'shifts/&lt;id&gt;';
$fullUrl = get_rest_url( NULL, $url );

$url2 = $startUrl . 'shifts/';
$fullUrl2 = get_rest_url( NULL, $url2 );
if( '/' == substr($fullUrl2, -1) ){
	$fullUrl2 = substr( $fullUrl2, 0, -1 );
}
?>

<p>
<strong>
PUT <?php echo $url; ?>
</strong>
</p>

<p>
<strong>__Headers__</strong>
</p>

<p>
X-WP-ShiftController-AuthCode: <?php echo $current['auth_code']; ?>
</p>

<p>
<strong>__Arguments__</strong>
</p>

<p>
<strong>calendar_id</strong> (__Calendar Id__) <em>__optional__</em><br>
<strong>employee_id</strong> (__Employee Id__) <em>__optional__</em><br>
<strong>start</strong> (__Start Date Time__, YYYYMMDDHHMM) <em>__optional__</em><br>
<strong>end</strong> (__End Date Time__, YYYYMMDDHHMM) <em>__optional__</em><br>
<strong>status_id</strong> (publish, draft) <em>__optional__</em><br>
<strong>conflict</strong> (__Set to 1 to allow creation of shifts with conflicts__) <em>__optional__</em><br>
</p>

<p>
<strong>__Examples__</strong>
</p>

<pre class="sh4-code">
PUT <?php echo $fullUrl2; ?>/123
start: <?php echo $today; ?>0900
end: <?php echo $today; ?>1530
</pre>

<pre class="sh4-code">
PUT <?php echo $fullUrl2; ?>/123
employee_id: 22
</pre>

<pre class="sh4-code">
PUT <?php echo $fullUrl2; ?>/123
status_id: publish
</pre>

<h3 class="hc-underline">__Get Available Employees__</h3>
<?php
$url = $startUrl . 'available-employees';
$fullUrl = get_rest_url( NULL, $url );
?>

<p>
<strong>
GET <?php echo $url; ?>
</strong>
</p>

<p>
<strong>__Headers__</strong>
</p>

<p>
X-WP-ShiftController-AuthCode: <?php echo $current['auth_code']; ?>
</p>

<p>
<strong>__Arguments__</strong>
</p>

<p>
<strong>calendar_id</strong> (__Calendar Id__) <em>__optional__</em><br>
<em>__default value__</em>: __all shift-type calendars__<br>

<strong>from</strong> (__Start Date Time__, YYYYMMDDHHMM) <em>__optional__</em><br>
<em>__default value__</em>: __beginning of today__<br>

<strong>to</strong> (__End Date Time__, YYYYMMDDHHMM) <em>__optional__</em><br>
<em>__default value__</em>: __one day from the start__<br>

</p>

<p>
<strong>__Examples__</strong>
</p>

<pre class="sh4-code">
GET <?php echo $fullUrl; ?>
</pre>

<pre class="sh4-code">
GET <?php echo $fullUrl; ?>?from=<?php echo $today; ?>0900&to=<?php echo $today; ?>1430
</pre>

<pre class="sh4-code">
GET <?php echo $fullUrl; ?>?from=<?php echo $today; ?>&calendar_id=123
</pre>

<pre class="sh4-code">
GET <?php echo $fullUrl; ?>?calendar_id[]=123&calendar_id[]=456
</pre>

<p>
<strong>__Example return__</strong>
</p>

<pre class="sh4-code">
{
  "from": "<?php echo $today; ?>0900",
  "to": "<?php echo $today; ?>1430",
  "employees": [
    {
      "id": "523",
      "title": "Alice",
      "email": "alice@host.local",
      "username": "alice"
    },
    {
      "id": "527",
      "title": "Eve",
      "email": "eve@host.local",
      "username": "eve"
    }
  ]
}
</pre>
