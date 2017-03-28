<?php
/*
Plugin Name: Baidu Submit URLs
Plugin URI: http://wordpress.org/plugins/baidu-submit-link/
Description: Baidu Submit URLs是一款适用于站长主动向Baidu提交网站新增、更新和删除内容URL，提升百度搜索引擎收录效率的WP插件。
Author: wbolt team
Version: 0.1.1
Author URI: http://www.wbolt.com/
*/
define('BSL_PATH',dirname(__FILE__));
define('BSL_BASE_FILE',__FILE__);

require_once BSL_PATH.'/classes/common.class.php';
require_once BSL_PATH.'/classes/admin.class.php';

new BSL_Admin();

