<?php

function bp_gom_matching_token_to_value( $pattern, $value )
{
	return str_replace( BP_GOM_TOKEN_ANSWER, $value, $pattern );
}

function bp_gom_matching_all_fields()
{
	global $wpdb, $bp;

	// fields to return
	$fields = array();

	// prep statement
	$sql = "SELECT id FROM " . $bp->profile->table_name_fields;

	// get all ids as array
	$field_ids = $wpdb->get_col( $sql );

	// loop all ids and create object
	foreach ( $field_ids as $field_id ) {
		$fields[] = new BP_XProfile_Field( $field_id );
	}

	return $fields;
}

function bp_gom_matching_group_lookup( BP_XProfile_Field $field, $user_id )
{
	// get group-o-matic field meta
	$field_meta = new BP_Gom_Field_Meta( $field->id, true );

	// must have a method and pattern
	switch ( true ) {
		case ( empty( $field_meta->method ) ):
		case ( empty( $field_meta->operator ) ):
		case ( empty( $field_meta->pattern ) ):
			// matching NOT possible
			return false;
	}

	// get user's value entered
	$field_data = $field->get_field_data( $user_id );
	$field_value = null;

	// do we have data?
	if ( $field_data instanceof BP_XProfile_ProfileData ) {
		// yep, get value
		$field_value = maybe_unserialize( $field_data->value );
	}

	// id to return is empty array by default
	$group_ids = array();

	// null value means impossible to match
	if ( null !== $field_value ) {
		if ( is_array( $field_value ) ) {
			foreach( $field_value as $this_value ) {
				$this_id = bp_gom_matching_group_lookup_for_value( $this_value, $field_meta );
				if ( $this_id ) {
					$group_ids[] = $this_id;
				}
			}
		} else {
			$group_ids[] = bp_gom_matching_group_lookup_for_value( $field_value, $field_meta );
		}
	}

	return $group_ids;
}

function bp_gom_matching_group_lookup_for_value( $field_value, $field_meta )
{
	global $bp, $wpdb;

	// if method is slug, lower case the value
	if ( $field_meta->method == 'slug' ) {
		$field_value = strtolower( $field_value );
	}

	// replace tokens
	$pattern_value = bp_gom_matching_token_to_value( $field_meta->pattern, $field_value );

	// default sql vars
	$column = apply_filters( 'bp_gom_matching_group_lookup_column', $field_meta->method, $field_meta );;
	$pattern = apply_filters( 'bp_gom_matching_group_lookup_pattern', $pattern_value, $field_meta );
	$operator = apply_filters( 'bp_gom_matching_group_lookup_operator', '=', $field_meta );

	if ( is_numeric( $pattern ) ) {
		$pattern_sql = $wpdb->prepare( '%d', $pattern );
	} else {
		$pattern_sql = $wpdb->prepare( '%s', $pattern );
	}

	$sql = "SELECT id FROM {$bp->groups->table_name} WHERE $column $operator $pattern_sql LIMIT 1";

	return $wpdb->get_var( $sql );
}

function bp_gom_matching_groups_meta( $user_id, $field_id = null, $use_cache = true )
{
	// load data from cache
	$user_groups_meta = new BP_Gom_User_Groups_Meta( $user_id );

	// use cache data as is?
	if ( $use_cache ) {
		// return stored values
		return $user_groups_meta;
	}

	// reset the groups
	$user_groups_meta->reset();
	
	// array of fields to loop
	$the_fields = array();

	// get matches for one specific field?
	if ( is_numeric( $field_id ) ) {
		// new field object
		$field_obj = new BP_XProfile_Field( (integer) $field_id );
		// append to the fields array
		$the_fields[] = $field_obj;
	} else {
		// call all fields helper to get ALL fields
		$the_fields = bp_gom_matching_all_fields();
	}

	// loop all fields
	foreach ( $the_fields as $field ) {
		// try to get matching groups
		$group_ids = bp_gom_matching_group_lookup( $field, $user_id );
		// get a group?
		if ( $group_ids && is_array( $group_ids ) ) {
			// create field meta instance
			$field_meta = new BP_Gom_Field_Meta( $field->id );
			// loop em
			foreach ( $group_ids as $group_id ) {
				// add each group
				$user_groups_meta->add_group( $group_id, $field_meta );
			}
		}
	}

	// update cache
	$user_groups_meta->update();

	return $user_groups_meta;
}

?>
