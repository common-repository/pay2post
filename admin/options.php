<?php

$GLOBALS['pay_options'] = new scbOptions( 'pay_options', false, array(
	'currency_code' => 'USD',
	'currency_identifier' => 'symbol',
	'currency_position' => 'left', 
	'thousands_separator' => ',',
	'decimal_separator' => '.',
	'price_per_post' => '5',
	'gateways' => array(
		'enabled' => array(),
	),
) );
