<?php
	if ( defined( 'BP_GOM_PRO_VERSION' ) ) {
		$bp_gom_pro_installed = true;
		$bp_gom_pro_version = BP_GOM_PRO_VERSION;
	} else {
		$bp_gom_pro_installed = false;
		$bp_gom_pro_version = 'Not Installed';
	}
?>
<div class="wrap nosubsub">

	<?php screen_icon( 'bp-groupomatic' ); ?>
	
	<h2><?php _e( 'BuddyPress Group-O-Matic', 'buddypress-groupomatic' ) ?></h2>

	<h3>Thank you for installing BuddyPress Group-O-Matic!</h3>
	
	<table border="0" class="widefat">
		<thead>
			<tr>
				<th colspan="2">
					Version Information
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th>Base:</th>
				<th><?php print BP_GOM_VERSION ?></th>
			</tr>
			<tr>
				<th>Pro Extension:</th>
				<th>
					<?php print $bp_gom_pro_version ?>
					<?php if ( !$bp_gom_pro_installed ): ?>
						<a href="http://shop.presscrew.com/shop/buddypress-groupomatic/" target="_blank" style="margin-left: 10px;">Purchase</a>
					<?php endif; ?>
				</th>
			</tr>
		</tbody>
	</table>

	<h3>Quick Start</h3>
	<p>
		There are no additional installation steps required after plugin activation.
	</p>
	<ol>
		<li>Click on <strong>Profile Fields</strong> under the <strong>Users</strong> menu.</li>
		<li>Click on <strong>Add New Field</strong> or <strong>Edit</strong> an existing field.</li>
		<li>Scroll down to <strong>Group-O-Matic Settings</strong> and set options for the field.</li>
		<li>Save the profile field as you normally would.</li>
		<li>Test your settings!</li>
	</ol>

	<h3>Documentation</h3>
	<p>
		All documentation is located inline, next to the Group-O-Matic options on the
		Extended Profile <strong>Edit Field</strong> screen.
	</p>

	<h3>Support</h3>
	<p>
		There are two levels of support:
	</p>
	<ul>
		<li>For support on the community version, head over to this plugin's <a href="http://buddypress.org/community/groups/buddypress-groupomatic/home/" target="_blank">official group</a> on BuddyPress.org</li>
		<li>For premium support on the community and pro versions, head over to the Press Crew <a href="http://community.presscrew.com/discussion/premium-plugins/" target="_blank">premium plugin forums</a>.</li>
	</ul>

	<h3>Pro Extension</h3>
	<p>
		The pro extension adds the following additional features:
	</p>
	<ul>
		<li><strong>Force Edit Profile</strong><br>Members can't navigate the site unless their profile is complete.</li>
		<li><strong>Group Activity Tab</strong><br>An additional tab is added to the activity stream under which only activity for the matching group is displayed.</li>
		<li><strong>Wildcard Pattern Matching</strong><br>Adds support for using meta-characters like: *, ?, [abc]</li>
		<li><strong>PCRE Pattern Matching</strong><br>For absolute control over the matching algorithm.</li>
		<li><strong>Premium Support</strong><br>Get professional support directly from the plugin's author.</li>
	</ul>
	<p>
		The pro extension is available for purchase in the <a href="http://shop.presscrew.com/shop/buddypress-groupomatic/" target="_blank">Press Crew Shop</a>
	</p>
	
</div>