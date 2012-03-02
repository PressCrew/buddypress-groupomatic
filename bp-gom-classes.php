<?php

/**
 * @property-write boolean $auto
 * @property-write boolean $activity
 * @property-write boolean $blocking
 * @property-write string $method
 * @property-write string $operator
 * @property-write string $pattern
 */
class BP_Gom_Field_Meta
{
	const KEY_ACTIVITY = 'bp_gom_activity';
	const KEY_AUTO = 'bp_gom_auto';
	const KEY_BLOCKING = 'bp_gom_blocking';
	const KEY_METHOD = 'bp_gom_method';
	const KEY_OPERATOR = 'bp_gom_operator';
	const KEY_PATTERN = 'bp_gom_pattern';

	private $field_id;
	private $refreshed;

	private $activity;
	private $auto;
	private $blocking;
	private $method;
	private $operator;
	private $pattern;

	public function __construct( $field_id, $populate = true )
	{
		// populate field var
		$this->field_id = $field_id;

		// populate all meta items?
		if ( $populate === true ) {
			$this->refresh();
		}
	}

	public function __get( $name )
	{
		switch ( $name ) {
			case 'activity':
				return $this->get_activity();
			case 'auto':
				return $this->get_auto();
			case 'blocking':
				return $this->get_blocking();
			case 'method':
				return $this->get_method();
			case 'operator':
				return $this->get_operator();
			case 'pattern':
				return $this->get_pattern();
		}
	}

	public function __set( $name, $value )
	{
		switch ( $name ) {
			case 'activity':
			case 'auto':
			case 'blocking':
				$this->$name = (boolean) $value;
				return;
			case 'method':
				switch ( $value ) {
					case null:
					case '':
					case 'id':
					case 'slug':
					case 'name':
						$this->method = (string) $value;
				}
				return;
			case 'operator':
				switch ( $value ) {
					case null:
					case '':
					case 'equals':
					case 'matches':
					case 'pcre':
						$this->operator = (string) $value;
				}
				return;
			case 'pattern':
				$this->pattern = substr( $value, 0, 255 );
				return;
		}
	}

	public function __isset( $name )
	{
		return isset( $this->$name );
	}

	private function get_meta( $key )
	{
		return bp_xprofile_get_meta( $this->field_id, 'field', $key );
	}

	private function update_meta( $key, $value )
	{
		return bp_xprofile_update_field_meta( $this->field_id, $key, $value );
	}

	public function refresh()
	{
		$this->get_blocking();
		$this->get_auto();
		$this->get_activity();
		$this->get_method();
		$this->get_operator();
		$this->get_pattern();

		$this->refreshed = true;
	}

	public function update()
	{
		if ( $this->refreshed === true ) {
			$this->update_meta( self::KEY_ACTIVITY, $this->activity );
			$this->update_meta( self::KEY_AUTO, $this->auto );
			$this->update_meta( self::KEY_BLOCKING, $this->blocking );
			$this->update_meta( self::KEY_METHOD, $this->method );
			$this->update_meta( self::KEY_OPERATOR, $this->operator );
			$this->update_meta( self::KEY_PATTERN, $this->pattern );

			// flush user groups meta data
			BP_Gom_User_Groups_Meta::delete_all();
		}
	}

	private function get_activity()
	{
		if ( is_null( $this->activity ) ) {
			$this->activity = (boolean) $this->get_meta( self::KEY_ACTIVITY );
		}

		return $this->activity;
	}

	private function get_auto()
	{
		if ( is_null( $this->auto ) ) {
			$this->auto = (boolean) $this->get_meta( self::KEY_AUTO );
		}

		return $this->auto;
	}
	
	private function get_blocking()
	{
		if ( is_null( $this->blocking ) ) {
			$this->blocking = (boolean) $this->get_meta( self::KEY_BLOCKING );
		}

		return $this->blocking;
	}

	private function get_method()
	{
		if ( is_null( $this->method ) ) {
			$this->method = $this->get_meta( self::KEY_METHOD );
		}

		return $this->method;
	}

	private function get_operator()
	{
		if ( is_null( $this->operator ) ) {
			$this->operator = $this->get_meta( self::KEY_OPERATOR );
		}

		return $this->operator;
	}

	private function get_pattern()
	{
		if ( is_null( $this->pattern ) ) {
			$this->pattern = $this->get_meta( self::KEY_PATTERN );
		}

		return $this->pattern;
	}
}

/**
 * @property-read boolean $auto
 * @property-read boolean $activity
 * @property-read boolean $blocking
 * @property-read integer $user_id
 * @property-read integer $group_id
 */
class BP_Gom_User_Group_Meta
{
	const KEY_ACTIVITY = 'activity';
	const KEY_AUTO = 'auto';
	const KEY_BLOCKING = 'blocking';

	private $activity;
	private $auto;
	private $blocking;

	private $user_id;
	private $group_id;

	public function __construct( $user_id, $group_id, $values = null )
	{
		// populate local props
		$this->user_id = $user_id;
		$this->group_id = $group_id;

		// populate all meta items?
		if ( $values instanceof BP_Gom_Field_Meta ) {
			$this->update( $values );
		} elseif ( is_array( $values ) ) {
			$this->populate( $values );
		}
	}

	public function __get( $name )
	{
		switch ( $name ) {
			case 'activity':
			case 'auto':
			case 'blocking':
			case 'group_id':
			case 'user_id':
				return $this->$name;
		}
	}

	public function __isset( $name )
	{
		return isset( $this->$name );
	}

	public function populate( $values )
	{
		$this->activity = $values[ self::KEY_ACTIVITY ];
		$this->auto = $values[ self::KEY_AUTO ];
		$this->blocking = $values[ self::KEY_BLOCKING ];
	}

	public function update( BP_Gom_Field_Meta $field_meta )
	{
		$this->activity = $field_meta->activity;
		$this->auto = $field_meta->auto;
		$this->blocking = $field_meta->blocking;
	}

	public function to_array()
	{
		return array(
			self::KEY_ACTIVITY => $this->activity,
			self::KEY_AUTO => $this->auto,
			self::KEY_BLOCKING => $this->blocking
		);
	}
}

/**
 * 
 */
class BP_Gom_User_Groups_Meta
{
	private $user_id;
	private $groups = array();

	public function __construct( $user_id )
	{
		// populate field var
		$this->user_id = $user_id;

		// populate group metas
		$this->populate();
	}

	private function get_meta()
	{
		return get_usermeta( $this->user_id, BP_GOM_META_KEY_USER_GROUPS );
	}

	private function update_meta()
	{
		// groups to save
		$groups = null;

		// convert objects to arrays
		foreach ( $this->groups as $user_group_meta ) {
			$groups[ $user_group_meta->group_id ] = $user_group_meta->to_array();
		}

		return update_usermeta(
			$this->user_id,
			BP_GOM_META_KEY_USER_GROUPS,
			$groups
		);
	}

	public function populate()
	{
		$groups = $this->get_meta();

		if ( is_array( $groups ) ) {
			foreach ( $groups as $group_id => $group ) {
				$this->groups[ $group_id ] =
					new BP_Gom_User_Group_Meta( $this->user_id, $group_id, $group );
			}
		}
	}

	public function update()
	{
		$this->update_meta();
	}
	
	public function add_group( $group_id, BP_Gom_Field_Meta $field_meta )
	{
		$this->groups[ $group_id ] = new BP_Gom_User_Group_Meta(
			$this->user_id,
			$group_id,
			$field_meta
		);
	}

	public function get_groups()
	{
		return $this->groups;
	}

	static public function delete_all()
	{
		delete_metadata( 'user', 0, BP_GOM_META_KEY_USER_GROUPS, null, true );
	}
}

?>
