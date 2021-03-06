<?php 

function etivite_bp_activity_block_admin_unique_types( ) {
	global $bp, $wpdb;
	
	$count = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT a.type FROM {$bp->activity->table_name} a ORDER BY a.date_recorded DESC" ) );
	
	return $count;
}

function etivite_bp_activity_block_admin() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) && check_admin_referer('etivite_bp_activity_block_admin') ) {
	
		if( isset($_POST['ab_activity_block_types'] ) && !empty($_POST['ab_activity_block_types']) ) {
			
			$unfiltered_types = explode( ',', $_POST['ab_activity_block_types'] );

			foreach( (array) $unfiltered_types as $type ) {
				if ( !empty( $type ) )
					$types[] = trim( $type );
			}
			
			if ($types) update_option( 'bp_activity_block_denied_activity_types', $types );
			
		} else {
			update_option( 'bp_activity_block_denied_activity_types', '' );
		}
		
		$updated = true;
	}
	
?>	
	<div class="wrap">
		<h2><?php _e( 'Activity Block Admin', 'bp-activity-block' ); ?></h2>

		<?php if ( isset($updated) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-activity-block' ) . "</p></div>"; endif; ?>
		
		<div id="message" class="updated">WARNING: Using this plugin will block activity stream entries defined by their types from being saved to the database. There is no recovery or reverting. You have been warned. :-) It is advised NOT to block activity_comment and activity_update activities (will cause errors in trendr)</div>

		<form action="<?php echo network_admin_url('/admin.php?page=bp-activity-block-settings') ?>" name="bp-activity-block-settings-form" id="bp-activity-block-settings-form" method="post">

			<h5><?php _e( 'Activity Types Found', 'bp-activity-block' ); ?></h5>
			<p class="description">This list is pull from the activity table database (previously logged activity) - so you may need to find other types (in bp and plugins)</p>

				<p><?php

				$currenttypes = (array) get_option( 'bp_activity_block_denied_activity_types');
				
				$uniquetypes = etivite_bp_activity_block_admin_unique_types();

				foreach ($uniquetypes as $types) {
					if ($types->type != 'activity_comment' && $types->type != 'activity_update' ) echo $types->type .'<br/>';
				} ?></p>

			<h5><?php _e( 'Activity Types to Exclude', 'bp-activity-block' ); ?></h5>
	
			<table class="form-table">
				<th><label for="ab_activity_block_types"><?php _e( "Blocked Activity Types:", 'bp-activity-block' ) ?></label> </th>
				<td><textarea rows="5" cols="50" name="ab_activity_block_types" id="ab_activity_block_types"><?php echo esc_attr( implode( ', ', $currenttypes ) ) ?></textarea><br/><?php _e( "Seperate types with commas.", 'bp-activity-block' ) ?></td>
			</table>
			
			<?php wp_nonce_field( 'etivite_bp_activity_block_admin' ); ?>
			
			<p class="submit"><input type="submit" name="submit" value="Save Settings"/></p>
			
		</form>

<?php
}

?>
