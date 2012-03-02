<?php

function bp_gom_autojoin_maybe_accept_invite( $user_id )
{
	// get all matching group ids for the user
	$user_groups_meta = bp_gom_matching_groups_meta( $user_id, false );

	// loop all matched groups
	foreach ( $user_groups_meta->get_groups() as $user_group_meta ) {
		// auto join this group?
		if ( $user_group_meta->auto ) {
			// force accept invite
			groups_accept_invite( $user_id, $user_group_meta->group_id );
		}
	}
}
add_action( 'bp_core_signup_user', 'bp_gom_autojoin_maybe_accept_invite', 11 );
add_action( 'xprofile_updated_profile', 'bp_gom_autojoin_maybe_accept_invite', 11 );

?>
