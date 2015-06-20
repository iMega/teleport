use teleport;

update `wp_options`
  set `option_name` = 'active_plugins', `option_value` = 'a:1:{i:0;s:33:\"imega-teleport/imega-teleport.php\";}', `autoload` = 'yes'
  where `option_id` = 33;
