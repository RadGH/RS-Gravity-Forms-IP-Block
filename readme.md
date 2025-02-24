# RS Gravity Forms IP Block (WordPress Plugin)

_Block IP addresses from submitting Gravity Forms._

To use this plugin, add this code to functions.php and replace the IP list with your own:

```php
// Specify custom block list
function theme_get_gf_ip_block_list( $ip_list ) {
	return array(
		'xxx.xxx.xxx.xxx',
	);
}
add_filter( 'rsgf/get_ip_block_list', 'theme_get_gf_ip_block_list' );
```

To instantly delete blocked entries add: 

```php
add_filter( 'rsgf/delete_blocked_entries', '__return_true' );
```

The IP address is based on these server variables:

* PHP (Default): `$_SERVER['REMOTE_ADDR']`
* Cloudflare: `$_SERVER['HTTP_CF_CONNECTING_IP']`

## Changelog

### 1.0.1
* Specified ip "block" list in filters and function names
* Renamed function in readme example

### 1.0.0
* Initial release