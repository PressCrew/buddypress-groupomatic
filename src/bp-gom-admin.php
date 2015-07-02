<?php

function bp_gom_admin_page_loader()
{
	require_once( BP_GOM_PLUGIN_DIR . '/admin/general.php' );
}

function bp_gom_admin_setup_menu()
{
	add_menu_page(
		__( 'Group-O-Matic', 'buddypress-groupomatic' ),
		__( 'Group-O-Matic', 'buddypress-groupomatic' ),
		'manage_options',
		'bp-groupomatic-general',
		'bp_gom_admin_page_loader',
		BP_GOM_PLUGIN_URL . '/assets/images/logo_16.png'
	);
}
add_action( bp_core_admin_hook(), 'bp_gom_admin_setup_menu' );

function bp_gom_admin_xprofile_save_options( $field )
{
	// grab field id
	$field_id = $field->id;

	// field id is empty on initial saves
	if ( !is_numeric( $field_id ) ) {
		$field_id = BP_XProfile_Field::get_id_from_name( $field->name );
	}

	//
	$meta = new BP_Gom_Field_Meta( $field_id );

	// group matching method
	if ( isset( $_POST[ BP_Gom_Field_Meta::KEY_METHOD ] ) ) {
		$meta->method = (string) $_POST[ BP_Gom_Field_Meta::KEY_METHOD ];
	}

	// group matching pattern
	if ( isset( $_POST[ BP_Gom_Field_Meta::KEY_OPERATOR ] ) ) {
		$meta->operator = $_POST[ BP_Gom_Field_Meta::KEY_OPERATOR ];
	}
	
	// group matching pattern
	if ( isset( $_POST[ BP_Gom_Field_Meta::KEY_PATTERN ] ) ) {
		$meta->pattern = $_POST[ BP_Gom_Field_Meta::KEY_PATTERN ];
	}

	// auto join option
	if ( isset( $_POST[ BP_Gom_Field_Meta::KEY_AUTO ] ) ) {
		$meta->auto = $_POST[ BP_Gom_Field_Meta::KEY_AUTO ];
	}
	
	// pre-save action
	do_action( 'bp_gom_admin_xprofile_before_save_options', $meta );

	// save the meta data
	$meta->update();

	// after save action
	do_action( 'bp_gom_admin_xprofile_after_save_options', $meta );
}
add_action( 'xprofile_fields_saved_field', 'bp_gom_admin_xprofile_save_options', 10, 1 );

function bp_gom_admin_assets()
{
	wp_enqueue_style(
		'bp-groupomatic-admin',
		BP_GOM_PLUGIN_URL . '/assets/css/admin.css'
	);

	do_action( 'bp_gom_admin_assets' );
}
add_action( 'load-toplevel_page_bp-groupomatic-general', 'bp_gom_admin_assets' );

function bp_gom_admin_xprofile_assets()
{
	wp_enqueue_style(
		'bp-groupomatic-admin-xprofile',
		BP_GOM_PLUGIN_URL . '/assets/css/admin-xprofile.css',
		array( 'xprofile-admin-css' )
	);

	do_action( 'bp_gom_admin_xprofile_assets' );
}
if ( version_compare( BP_VERSION, '1.6', '>=' ) ) {
	add_action( 'load-users_page_bp-profile-setup', 'bp_gom_admin_xprofile_assets' );
} else {
	add_action( 'load-buddypress_page_bp-profile-setup', 'bp_gom_admin_xprofile_assets' );
}

function bp_gom_admin_render_options( $field )
{
	$meta = new BP_Gom_Field_Meta( $field->id );

	// field sets ?>
	<div id="buddypress-groupomatic">
		<fieldset id="buddypress-groupomatic-options">
			<legend><?php _e( 'Group-O-Matic Options', 'buddypress-groupomatic' ) ?></legend>
			<?php do_action( 'bp_gom_admin_render_options_fieldset', $meta ); ?>
		</fieldset>
		<fieldset id="buddypress-groupomatic-help">
			<legend><?php _e( 'Group-O-Matic Help', 'buddypress-groupomatic' ) ?></legend>
			<h2><a href="#">Overview</a></h2>
			<div style="display: block;">
				Group-O-Matic can automatically join members to a group based on a
				rule which you define using the <strong>Match to Group</strong> option.
				The group(s) must already exist, and be open to the public for membership.
				Answers supplied by the <strong>member</strong> when they complete
				the field by creating/editing their profile can be used as part of the
				matching decision.
			</div>
			<?php do_action( 'bp_gom_admin_render_options_help', $meta ); ?>
			<div style="display: block; text-align: right;">
				<sup>1</sup> Pro version only
			</div>
		</fieldset>
		<div style="clear: left;"></div>
	</div>

	<script type="text/javascript">
		jQuery(document).ready(function() {
			// move container to end of form
			jQuery('div#buddypress-groupomatic')
				.siblings( 'p.submit' )
				.first()
				.before( function(){
					return jQuery('div#buddypress-groupomatic').detach();
				});
			// handle help click events
			jQuery('fieldset#buddypress-groupomatic-help a')
				.click(function(e){
					jQuery(this).parent().next('div').toggle();
					e.preventDefault()
				});
		});
	</script><?php
}
add_action( 'xprofile_field_additional_options', 'bp_gom_admin_render_options', 10, 1 );

function bp_gom_admin_render_matching_option( BP_Gom_Field_Meta $meta )
{
	// render field ?>
	<div id="titlediv">
		<h3><label for="buddypress-groupomatic-method"><?php _e( "Match to group when", 'buddypress-groupomatic' ); ?>:</label></h3>
		<table border="0">
			<thead>
				<tr>
					<th><?php _e( "Attribute", 'buddypress-groupomatic' ); ?></th>
					<th><?php _e( "Operation", 'buddypress-groupomatic' ); ?></th>
					<th><?php _e( "Pattern", 'buddypress-groupomatic' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<select name="<?php print BP_Gom_Field_Meta::KEY_METHOD ?>" id="buddypress-groupomatic-method">
							<option value=""><?php _e( 'Select Type', 'buddypress-groupomatic' ); ?></option>
							<option value="name"<?php if ( 'name' == $meta->method ) { ?> selected="selected"<?php } ?>><?php _e( 'Group Name', 'buddypress-groupomatic' ); ?></option>
							<option value="slug"<?php if ( 'slug' == $meta->method ) { ?> selected="selected"<?php } ?>><?php _e( 'Group Slug', 'buddypress-groupomatic' ); ?></option>
							<option value="id"<?php if ( 'id' == $meta->method ) { ?> selected="selected"<?php } ?>><?php _e( 'Group ID', 'buddypress-groupomatic' ); ?></option>
						</select>
					</td>
					<td>
						<select name="<?php print BP_Gom_Field_Meta::KEY_OPERATOR ?>" id="buddypress-groupomatic-operator">
							<option value="equals"<?php if ( empty( $operator ) || 'equals' == $meta->operator ) { ?> selected="selected"<?php } ?>><?php _e( 'Equals', 'buddypress-groupomatic' ); ?></option>
							<?php do_action( 'bp_gom_admin_render_matching_option_operators', $meta ); ?>
						</select>
					</td>
					<td>
						<input type="text" name="<?php print BP_Gom_Field_Meta::KEY_PATTERN ?>" id="buddypress-groupomatic-pattern" value="<?php print esc_attr( $meta->pattern ) ?>">
					</td>
				</tr>
			</tbody>
		</table>
	</div><?php
}
add_action( 'bp_gom_admin_render_options_fieldset', 'bp_gom_admin_render_matching_option' );

function bp_gom_admin_render_autojoin_option( BP_Gom_Field_Meta $meta )
{
	// get meta value
	$enabled = $meta->auto;

	// render field ?>
	<div id="titlediv">
		<h3><label for="buddypress-groupomatic-autojoin"><?php _e( "Automatically join matching group?", 'buddypress-groupomatic' ); ?></label></h3>
		<select name="<?php print BP_Gom_Field_Meta::KEY_AUTO ?>" id="buddypress-groupomatic-autojoin" style="width: 30%">
			<option value="1"<?php if ( $enabled ) { ?> selected="selected"<?php } ?>><?php _e( 'Yes', 'buddypress-groupomatic' ); ?></option>
			<option value="0"<?php if ( !$enabled ) { ?> selected="selected"<?php } ?>><?php _e( 'No', 'buddypress-groupomatic' ); ?></option>
		</select>
	</div><?php
}
add_action( 'bp_gom_admin_render_options_fieldset', 'bp_gom_admin_render_autojoin_option' );

function bp_gom_admin_render_matching_help( BP_Gom_Field_Meta $meta )
{
	// matching help ?>
	<h2><a href="#">Match to Group</a></h2>
	<div>
		<h2>Attributes</h2>
		<p>An attribute is the group meta-data which will be used for matching.</p>

		<h4>Group Name</h4>
		<p>The name of a group, for example: <strong>News</strong></p>

		<h4>Group Slug</h4>
		<p>The slug created for a group, for example: <strong>news</strong></p>

		<h4>Group ID</h4>
		<p>The numeric ID assigned to a group, for example: <strong>37</strong></p>

		<h2>Operation</h2>
		<p>An operation is the algorithm (decision making logic) to use when matching.</p>

		<h4>Equals</h4>
		<p>
			A match occurs when the <em>Pattern</em> exactly matches the <em>Attribute</em>
			of a group.
		</p>

		<h4>Matches<sup>1</sup></h4>
		<p>
			A match occurs when the <em>Pattern</em> matches the <em>Attribute</em>
			of a group using basic wildcard style matching.
		</p>
		<p>
			The following meta-characters are supported:
			<ul>
				<li><strong>*</strong> - Match zero or more characters ( boo* matches books )</li>
				<li><strong>?</strong> - Match exactly one character ( foo?s matches fools)</li>
				<li><strong>[abc]</strong> - Match only listed characters ( coo[kl]s matches cooks and cools)</li>
				<li><strong>[!abc]</strong> - Match only unlisted characters ( coo[!p]s matches cooks and cools but not coops)</li>
			</ul>
		</p>

		<h4>Matches PCRE<sup>1</sup></h4>
		<p>
			A match occurs when the <em>Pattern</em> matches the <em>Attribute</em>
			of a group using a Perl Compatible Regular Expression.
		</p>
		<p>
			PCRE syntax is outside the scope of this documentation. However it is important
			to note that you <strong>should not</strong> provide the leading and trailing forward
			slashes in the pattern ( correct: fooba[rz] incorrect: /fooba[rz]/  ).
		</p>
		<p>
			PCRE is very powerful and can solve the common problem of group names or slugs
			which aren't normalized.
		</p>
		<p>
			Here are some basic examples:
			<ul>
				<li><strong>(sports|nba)-%answer%</strong> - matches group slugs sports-celtics and nba-lakers</li>
				<li><strong>(nba-)?%answer%</strong> - matches group slugs nba-celtics and lakers</li>
			</ul>
		</p>

		<h2>Pattern</h2>
		<p>
			This is the text and optional operation syntax used to match the <em>Attribute</em>.
		</p>
		<p>
			The value (or answer) which is entered or selected by the member will replace all
			occurrences of the <strong>%answer%</strong> token.
		</p>
		<p>
			If the selected <em>Attribute</em> is <strong>Group Slug</strong>, then the answer
			is converted to all lowercase characters before being replaced into the pattern.
		</p>

		<h2>Putting it Together</h2>

		<h4>Answer Matching</h4>
		<p>
			The values you enter for the extended profile field options must exactly or partially<sup>1</sup>
			match the group Name, Slug, or ID of the possible groups to which members will be joined,
			so that when the answer replaces the %answer% token(s), the <em>Pattern</em> will match
			the applicable group <em>Attribute</em> of one existing group.
		</p>
		<p>
			Keep in mind that once the answer has been replaced into the <em>Pattern</em>
			<strong>the answer becomes part of the <em>Pattern</em>.</strong> For this reason
			you should avoid any extended profile field option values which contain special characters
			that will be interpreted as a meta-character.
		</p>

		<h4>Simple Matching</h4>
		<p>
			This type of matching applies when you <strong><u>do not</u> use the %answer% token</strong>
			and enter a specific group Name, Slug, or ID into the <em>Pattern</em>.
			The member will be joined to the matching group if
			<strong>any non-empty answer</strong> is entered or selected by the member.
		</p>

	</div>
	<?php
}
add_action( 'bp_gom_admin_render_options_help', 'bp_gom_admin_render_matching_help' );

function bp_gom_admin_render_autojoin_help( BP_Gom_Field_Meta $meta )
{
	// autojoin help ?>
	<h2><a href="#">Automatic Join</a></h2>
	<div>
		When <strong>Automatic Join</strong> is enabled, the member will be joined to the
		matching group automatically. This option should almost always be enabled, and is
		mostly a placeholder for future functionality.
	</div>
	<?php
}
add_action( 'bp_gom_admin_render_options_help', 'bp_gom_admin_render_autojoin_help' );

function bp_gom_admin_render_blocking_help( BP_Gom_Field_Meta $meta )
{
	// blocking help ?>
	<h2><a href="#">Force Edit Profile</a><sup>1</sup></h2>
	<div>
			When <strong>Force Edit Profile</strong> is enabled, the member will be prevented
			from viewing any pages on the site until they have completed this
			extended profile field. Any attempt to navigate the site while logged in will result
			in redirection to their profile edit screen, where a friendly message will
			prompt them to complete their profile.
			<br><br>
			This option does not have any effect unless the extended profile
			field is <strong>required</strong>.
	</div>
	<?php
}
add_action( 'bp_gom_admin_render_options_help', 'bp_gom_admin_render_blocking_help' );

function bp_gom_admin_render_activity_help( BP_Gom_Field_Meta $meta )
{
	// activity help ?>
	<h2><a href="#">Group Activity Tab</a><sup>1</sup></h2>
	<div>
		When <strong>Group Activity Tab</strong> is enabled, an additional tab will be
		added to the main activity stream under which only activity for the matching
		group is displayed.
	</div>
	<?php
}
add_action( 'bp_gom_admin_render_options_help', 'bp_gom_admin_render_activity_help' );

?>
