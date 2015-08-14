<?php

namespace Creative {

defined( 'ABSPATH' ) or die;

#### NOTE ######## NOTE ######## NOTE ####
/*
	The content below gets pretty messy due to      <?php ?> tags.
	PHP complained a lot about newlines between the <?php ?> tags.
*/
#### NOTE ######## NOTE ######## NOTE ####

function getPostTypeContents() {
	global $SETTINGS;
	$info_icon = plugins_url('/assets/info-icon.png', __FILE__);

	$used_post_types = getUsedPostTypes();
	foreach($used_post_types as $post_type):
		global $NAME;

		$prefix = $SETTINGS . "[{$post_type}]";

		$enabled = getSetting($post_type, "enabled");
		$enabled_name  = $prefix . "[enabled]";
		$enabled_checked = checked($enabled == "on", true, false);

		$column_name  = $prefix . "[column]";
		$column = getSetting($post_type, "column");
		$column_checked = checked($column == "on", true, false);

		//$delimiter_name = $prefix . "[delimiter]";
		//$delimiter = getSetting($post_type, "delimiter");
?>
		<div id="<?php echo $post_type; ?>" class="post_type panel" valign="top">
			<div class="header">
				<h1 class="title"><?php echo ucwords($post_type); ?></h1>
			</div>

			<hr />

			<div class="fields">
				<div class="enabled">
					<div class="tag">Enabled</div>
					<div class="field">
						<input type="checkbox" name="<?php echo $enabled_name; ?>" <?php echo $enabled_checked; ?> />
					</div>
					<div class="tooltip tooltip-right" data-tooltip="if the post type is sorted">
						<img src="<?php echo $info_icon; ?>" />
					</div>
				</div>

				<div class="column">
					<div class="tag">Column</div>
					<div class="field">
						<input type="checkbox" name="<?php echo $column_name; ?>" <?php echo $column_checked; ?> />
					</div>
					<div class="tooltip tooltip-right" data-tooltip="if the '<?php echo $NAME; ?>' column is shown in the posts page">
						<img src="<?php echo $info_icon; ?>" />
					</div>
				</div>

				<div class="language">
					<div class="tag">Language</div>
					<div class="field">
						<?php getLanguageDropdown($post_type); ?>
					</div>
					<div class="tooltip tooltip-right" data-tooltip="what language to sort posts by">
						<img src="<?php echo $info_icon; ?>" />
					</div>
				</div>
			</div>
		</div>
<?php endforeach; } ?>
<?php
function getLanguageDropdown($post_type) {
	global $SETTINGS;
	$option_name = $SETTINGS . "[{$post_type}][language]";
	$all = Sort::getLanguageOptions();
	$selected = getSetting($post_type, 'language');

	getDropdown($all, $selected, $option_name, false);
}
?>
<?php
#function for generating a dropdown from an array of strings
#communicates via $name in the options wpdb
#the $selected variable is the string that is currently selected
function getDropdown($strings, $selected, $name, $include_none=true) { ?>
	<select name='<?php echo $name; ?>'>
		<!-- default option -->
		<?php if($include_none): ?>
			<option value=''> <?php echo 'none'; ?> </option>
		<?php endif; ?>
		<!-- default option -->

		<!-- iterate through strings -->
		<?php foreach ( $strings as $string ) {
			if($string == $selected)
				$is_selected = " selected";
			else
				$is_selected = "";
		?>
			<option value=<?php echo "\"{$string}\""; echo $is_selected; ?> >
				<?php echo $string; ?>
			</option>
		<?php } ?>
		<!-- iterate through strings -->

	</select>
<?php } ?>
<?php
function getPage() {
	if(isset($_POST['reset'])) resetSettings();

	global $NAME;
	global $SLUG;
	global $SETTINGS;
	$icon = plugins_url('/assets/icon_300.png', __FILE__);
?>

	<div id="header">
		<img src='<?PHP echo $icon; ?>' class="right"/>
		<div class="center">
			<h1><?PHP echo $NAME; ?> Settings</h1>
			<h3>Easily Sort Posts by Greek Phonetics</h3>
			<div>get support on the <a href="">Wordpress Plugin page</a></div>
		</div>
	</div>
	<div class="clear"></div>

	<div class="wrap">
		<form method="post" action="options.php">
			<?php settings_fields($SETTINGS); ?>
			<?php do_settings_sections($SETTINGS); ?>

			<div id="general">
				<div class="body">
					<?php getPostTypeContents(); ?>
				</div>
			</div>

			<input name="select-all" class="button flat" type="button" value="enable all" onclick="c_select('.enabled', true)" >
			<input name="select-all" class="button flat" type="button" value="enable none" onclick="c_select('.enabled', false)" >

			<input type="submit" name="submit" id="submit" class="button button-primary right" value="Save Changes">
		</form>
		
		<div class="right">
		<form method="post">
			<input name="reset" class="button button-secondary" type="submit" value="Reset" >
		</form>
		</div>

	</div>
<?php } ?>
<?php
#<!-- settings -->



#<!-- enqueues -->
function enqueue_styles() {
	$path = plugins_url('/css/styles.css', __FILE__);
    wp_enqueue_style('page-styles', $path);

	$path = plugins_url('/css/tooltips.css', __FILE__);
    wp_enqueue_style('tooltips', $path);

	$path = plugins_url('/js/jquery-1.11.3.min.js', __FILE__);
	wp_enqueue_script('jquery', $path);

	$path = plugins_url('/js/functionality.js', __FILE__);
	wp_enqueue_script('page-js', $path);
}
add_action( 'admin_init', 'Creative\enqueue_styles' );
#<!-- enqueues -->



} #<!-- namespace -->