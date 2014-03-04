<?php
/*
Plugin Name: CM Search Replace
Plugin URI: http://www.cmext.vn/
Description: Plugin to search and replace specific words by different words in the whole front-end page. You can find the setting page of this plugin under "Settings" menu item, the setting page requires jQuery.
Version: 1.0
Author: CMExtension Team
Author http://www.cmext.vn/
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

add_action( 'admin_menu', 'cm_add_menu_item' );

function cm_add_menu_item() {
	add_options_page( __( 'CM Search & Replace', 'cm-search-replace' ), __( 'CM Search & Replace', 'cm-search-replace' ), 'manage_options', 'cm_search_replace', 'cm_generate_option_page' );
}

function cm_generate_option_page() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'cm-search-replace' ) );
	}

	$searchFieldName = 'search[]';
	$replaceFieldName = 'replace[]';

	if( isset( $_POST['search'] ) && isset( $_POST['replace'] ) ) {
		$data = array();

		foreach ( $_POST['search'] as $key => $search ) {
			$data[$search] = $_POST['replace'][$key];
		}

		$json = json_encode( $data );

		update_option( 'data', $json );

		echo '<div class="updated"><p><strong>' . __( 'Settings saved', 'cm-search-replace' ) . '</strong></p></div>';
	}

	$json = get_option( 'data' );
	$data = json_decode( $json );
?>
<style>
#cmCloneMe {
	display: none;
}
</style>
<script>
	jQuery('#cmAdd').live('click', function() {
		var newSearch = jQuery('#cmCloneMe').clone();
		var container = jQuery('#cmFieldContainer');
		newSearch.removeAttr('id').appendTo(container);
	});

	jQuery('.cmRemove').live('click', function() {
		var wrapper = jQuery(this).closest('p');
		wrapper.remove();
	});
</script>
<div class="wrap">
	<h2><?php _e( 'Settings for CM Search & Replace plugin', 'cm-search-replace' ); ?></h2>
	<form method="post" action="">
		<div id="cmFieldContainer">
<?php
if ( !empty( $data )) {
	foreach ( $data as $search => $replace ) {
		$search = htmlspecialchars( stripslashes( $search ), ENT_QUOTES );
		$replace = htmlspecialchars( stripslashes( $replace ), ENT_QUOTES );
?>
	<p>
		<?php _e( 'Search for ', 'cm-search-replace' ); ?>
		<input type="text" name="<?php echo $searchFieldName; ?>" value="<?php echo $search; ?>">
		<?php _e( 'then replace by ', 'cm-search-replace' ); ?>
		<input type="text" name="<?php echo $replaceFieldName; ?>" value="<?php echo $replace; ?>">
		<span class="cmRemove button-primary"><?php _e( 'Remove' , 'cm-search-replace') ?></span>
	</p>
<?php
	}
}
?>
		</div>
		<p>
			<div id="cmAdd" class="button-primary"><?php _e( 'Add' , 'cm-search-replace' ) ?></div>
			<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save' , 'cm-search-replace' ) ?>" />
		</p>
	</form>
</div>
<div id="cmCloneMe">
	<p>
		<?php _e( 'Search for ', 'cm-search-replace' ); ?>
		<input type="text" name="<?php echo $searchFieldName; ?>" value="">
		<?php _e( 'then replace by ', 'cm-search-replace' ); ?>
		<input type="text" name="<?php echo $replaceFieldName; ?>" value="">
		<span class="cmRemove button-primary"><?php _e( 'Remove' , 'cm-search-replace' ) ?></span>
	</p>
</div>
<?php
}

function cm_search_replace( $text ) {
	$json = get_option( 'data' );
	$data = json_decode( $json );

	if ( !empty( $data )) {
		foreach ( $data as $search => $replace ) {
			$search = stripslashes( $search );
			$replace = stripslashes( $replace );
			$text = str_replace( $search, $replace, $text );
		}
	}

	return $text;
}

if ( !is_admin() ) {
	ob_start( 'cm_search_replace' );
}
?>
