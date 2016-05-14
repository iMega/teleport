use teleport;

update `wp_options`
  set `option_name` = 'active_plugins', `option_value` = 'a:2:{i:0;s:33:\"imega-teleport/imega-teleport.php\";i:1;s:27:\"woocommerce/woocommerce.php\";}', `autoload` = 'yes'
  where `option_id` = 33;

replace wp_options (option_name, option_value, autoload) value ('stylesheet', 'shopping', 'yes');
replace wp_options (option_name, option_value, autoload) value ('current_theme', 'Shopping', 'yes');
