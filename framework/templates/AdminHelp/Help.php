<h1>Help</h1>

<h2>Setup</h2>
<p>Each page of the plugin allows you to setup each stage of what SCRM Hub requires, and each page has inline help to make it simpler and explain what each item does. The stages are:</p>

<ol>
	<li>
		<p>
			<strong><a href="admin.php?page=scrmhub">Site Settings</a></strong><br>
			This is where you configure your Application ID and Secret which are used to communicate with SCRM Hub APIs. 
		</p>
	</li>
	<li>
		<p>
			<strong><a href="admin.php?page=scrmhub-location-token">Location Token</a></strong><br>
			Everywhere a scrmhub application lives, has a unique token which is used for tracking, API calls and allows us to provide additonal security checks on the location.
		</p>
	</li>
	<li>
		<p>
			<strong><a href="admin.php?page=scrmhub-sharingsettings">Sharing Settings</a></strong><br>
			We provide several sharing options which can be enabled and configured here.
		</p>
	</li>
	<li>
		<p>
			<strong><a href="admin.php?page=scrmhub-connectsettings">Connect Settings</a></strong><br>
			We provide several connect options which can be enabled and configured here.			
		</p>
	</li>
	<li>
		<p>
			<strong><a href="admin.php?page=scrmhub-activitysettings">Activity Settings</a></strong><br>
			Turn our activity / page tracking on or off and configure settings.
		</p>
	</li>
</ol>

<?php
include(realpath(__dir__).'/SiteSettingsHelp.htm');
include(realpath(__dir__).'/LocationTokenHelp.htm');
include(realpath(__dir__).'/SharingSettingsHelp.htm');
include(realpath(__dir__).'/ConnectSettingsHelp.htm');
include(realpath(__dir__).'/ActivitySettingsHelp.htm');
?>