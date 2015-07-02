<?php

function bp_gom_autojoin_maybe_accept_invite( $xprofile_data )
{
	// make sure data is the right type of object
	if ( $xprofile_data instanceof BP_XProfile_ProfileData ) {
		// set vars
		$user_id = $xprofile_data->user_id;
		$field_id = $xprofile_data->field_id;
	} else {
		// nope, bail
		return;
	}
	
	// get all matching group ids for the user and field
	$user_groups_meta = bp_gom_matching_groups_meta( $user_id, $field_id, false );

	// loop all matched groups
	foreach ( $user_groups_meta->get_groups() as $user_group_meta ) {
		// auto join this group?
		if ( $user_group_meta->auto ) {
			// force accept invite
			groups_accept_invite( $user_id, $user_group_meta->group_id );
		}
	}
}
add_action( 'xprofile_data_after_save', 'bp_gom_autojoin_maybe_accept_invite' );

?>
