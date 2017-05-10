# Example Connect Function calls #

## Automatically render the login buttons or logout button ##

```
#!php

<?php do_action( 'scrmhub_connect' ); ?>
```


## Render a single Connect button ##

```
#!php

<?php do_action( 'scrmhub_login_button', array('network' => 'facebook', 'label' => 'Connect with Facebook')); ?>
```


## Render all enabled conect networks ##

```
#!php

<?php do_action( 'scrmhub_connect_login' ); ?>
```


## Render the logout button ##

```
#!php

<?php do_action( 'scrmhub_connect_logout' ); ?>
```


## Get the connect URL for a network ##
This is useful if you want to ad the link into a menu for example

```
#!php

<?php do_action( 'scrmhub_connect_login_url' ); ?>
```





# Example Share Function calls #
## Render all share buttons ##

```
#!php

<?php do_action( 'scrmhub_share' ); ?>
```


## Render a single share button ##

```
#!php

<?php do_action( 'scrmhub_share_button', array('network' => 'twitter', 'label' => 'Share to Twitter')); ?>
```


## Get the URL for sharing to a network ##

```
#!php

<?php do_action( 'scrmhub_share_url', 'twitter'); ?>
```