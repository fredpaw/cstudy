<?php


class BSL_Admin
{
	public function __construct(){	
		
		$this->init();
		
		
	}
	
	
	public function init(){
		
		
		//after_delete_post
		//deleted_post
		//edit_post
		//save_post
		
		
		if(is_admin()){
			
			register_activation_hook(BSL_BASE_FILE, array($this,'plugin_activate'));	
			register_deactivation_hook(BSL_BASE_FILE, array($this,'plugin_deactivate'));
			
			//注册相关动作
			
			//放垃圾
			add_action('trashed_post',array($this,'bsl_trashed_post'));
			
			//垃圾恢复
			add_action('untrashed_post',array($this,'bsl_untrashed_post'));
			
			//更新
			add_action('post_updated',array($this,'bsl_post_updated'),10,3);
			
			//删除
			//add_action('deleted_post',array($this,'bsl_deleted_post'));
			
			add_action( 'admin_menu', array($this,'admin_menu') );
			
			add_action( 'admin_init', array($this,'admin_init') );
			//插件设置连接
			add_filter( 'plugin_action_links', array($this,'actionLinks'), 10, 2 );
		}
		
		//增加定时计划
		add_filter('cron_schedules', array($this,'bsl_cron_schedules'));
		
		
		//定时
		add_action('bsl_cron_action', array($this,'bsl_push_batch'));
		//wp_schedule_single_event();
		
		//注册插件初始化
		add_action('init',array($this,'plugin_init'));
		
		
	}
	
	function actionLinks( $links, $file ) {
		
		if ( $file != plugin_basename(BSL_BASE_FILE) )
			return $links;
	
		$settings_link = '<a href="'.menu_page_url( BSL_Common::$name, false ).'">设置</a>';
	
		array_unshift( $links, $settings_link );
	
		return $links;
	}
	
	function admin_menu(){
		add_options_page(
					'Baidu Submit Url 设置',
					'Baidu Submit Url',
					'manage_options',
					BSL_Common::$name,
					array($this,'admin_settings')
				);
	}
	function admin_settings(){
		$setting_field = BSL_Common::$settingField;
		$option_name = BSL_Common::$optionName;
		$op_sets = get_option( $option_name );
		
		include_once( BSL_PATH.'/settings.php' );
	}
	
	function plugin_activate()
	{
		global $wpdb;
		$prefix = $wpdb->get_blog_prefix();
		$table = $prefix.'bsl_push';
		//建表
		$sql = 'CREATE TABLE IF NOT EXISTS `'.$prefix.'bsl_push` (
				  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				  `post_id` bigint(20) unsigned NOT NULL,
				  `status` tinyint(4) NOT NULL,
				  `state` tinyint(4) NOT NULL,
				  `resp` varchar(200) DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  KEY `post_id` (`post_id`)
				)';
		$wpdb->query($sql);
	}
	
	function plugin_deactivate()
	{
		
	}
	
	function plugin_init(){
		
		$option_name = BSL_Common::$optionName;
		$op_sets = get_option( $option_name );
		$token = $op_sets['token'];
		$siteurl = get_option('siteurl');
		$parse = parse_url($siteurl);
		
		$site = $parse['host'];
		if(!$token || !$site)
			add_action( 'admin_notices', array($this,'notice'));
			
		
		
		//初始化定时器
		$this->bsl_cron_init();
	}
	
	function admin_init(){
		register_setting(  BSL_Common::$settingField,BSL_Common::$optionName );
	}
	function bsl_trashed_post($post_id){
		$this->bsl_push_post($post_id,3);
	}
	
	function bsl_untrashed_post($post_id){		
		$this->bsl_push_post($post_id);
	}
	
	function bsl_deleted_post($post_id){
		$this->bsl_push_post($post_id,3);
	}
	
	function bsl_cron_schedules(){
		//
		$schedule = array(
			'fiveminu' =>array('interval'=>5 * MINUTE_IN_SECONDS,'display' => 'Five Minutes')
		);
		return $schedule;
	}
	
	function bsl_cron_init(){
		
		//删除定时
		//wp_clear_scheduled_hook('bsl_push_batch')
		/*if($timestamp = wp_next_scheduled('bsl_push_batch')){		
			wp_unschedule_event($timestamp,'bsl_push_batch');
		}*/
		//增加定时
		if(!wp_next_scheduled('bsl_cron_action')){
			wp_schedule_event(time(), 'fiveminu', 'bsl_cron_action');
		}
		
		
		
		return true;
		//add_option();
		//delete_option();
		//update_option();
		//get_option
		//print_r(get_option('cron'));exit();
		/*$last = get_option('bsl_last_cron_run');
		if(!$last){
			$last = time();
			add_option('bsl_last_cron_run',$last,'',false);
		} 
		if($last<time()){
			//bsl_push_batch();
			update_option('bsl_last_cron_run',time() + 60);
		}*/
	}
	function bsl_push_batch(){
		global $wpdb;
		$prefix = $wpdb->get_blog_prefix();
		$table = $prefix.'bsl_push';
		$siteurl = get_option('siteurl');
		
		//new post
		for($i=1;$i<4;$i++){
			$sql = "SELECT a.id,a.status,b.post_name FROM `{$prefix}bsl_push` a,{$prefix}posts b WHERE a.post_id=b.id and a.state=0 and a.status=$i";
			//$wpdb->insert($table,array('post_id'=>1,'data1'=>time()));
			$result = $wpdb->get_results($sql,ARRAY_A);
			$urls = array();
			$ids = array();
			if($result)foreach($result as $r){
				$urls[] = $siteurl.'/'.$r['post_name'];
				$ids[] = $r['id'];
			}
			if($urls){
				$resp = $this->bsl_send_url($urls,$i);
				//$wpdb->prepare()
				$body = '';
				if($resp && $resp->body){
					$body = $resp->body;
				}
				$wpdb->query($wpdb->prepare("UPDATE {$prefix}bsl_push set state=1,resp=%s where ".'id in('.implode(',',$ids).')',$body));//
				//$wpdb->update("{$prefix}bsl_push",array('state'=>1),array('id in('.implode(',',$ids).')'));
			}
		}
		
	}
	function notice(){
		echo '<div class="error"><p>注意：插件:百度链接推送，未设置相关参数，链接将无法推送到百度。</p></div>';
	}
	function bsl_send_url($urls,$type){
		global $wpdb;
		$apis = array(
			1=>'http://data.zz.baidu.com/urls',
			2=>'http://data.zz.baidu.com/update',
			3=>'http://data.zz.baidu.com/del'
		);
		//npoPqMrhkqfZhMwr
		//www.wowrk.com
		$option_name = BSL_Common::$optionName;
		$op_sets = get_option( $option_name );
		$token = $op_sets['token'];
		$siteurl = get_option('siteurl');
		$parse = parse_url($siteurl);
		$site = $parse['host'];
		if(!$site || !$token){
			
			return;
		}
		//
		$api = $apis[$type].'?site='.$site.'&token='.$token;
		$args = array(
			'method'=>'POST',
			'body'=>implode('\n',$urls)	
		);
		$result = wp_remote_post($api,$args);
		
		//$wpdb->insert('wp_test',array('post_id'=>1,'data1'=>json_encode($urls),'data2'=>json_encode($result)));
		return $result;
	}
	//wp_schedule_event(time(),'');
	
	function bsl_post_updated($post_ID, $post_after, $post_before){
		global $wpdb;
		
		$prefix = $wpdb->get_blog_prefix();
		$table = $prefix.'test';
		$post_before->post_modified = null;
		$post_before->post_modified_gmt = null;
		$post_before->comment_count = 0;
		$before_data = json_encode($post_before);

		$post_after->post_modified = null;
		$post_after->post_modified_gmt = null;
		$post_after->comment_count = 0;
		$after_data = json_encode($post_after);
		if(md5($after_data)!=md5($before_data) && $post_after->post_status == 'publish'){
			//$wpdb->insert($table,array('post_id'=>$post_ID,'data1'=>$before_data,'data2'=>$after_data));
			$this->bsl_push_post($post_ID);
		}		
	}
	
	function bsl_push_post($post_id,$status=0){
		global $wpdb;
		$prefix = $wpdb->get_blog_prefix();
		$table = $prefix.'bsl_push';
		//statue 1:new,2:edit,3:del
		//state 0:new,1:succ,2fail,3:del
		if($push = $wpdb->get_row($wpdb->prepare("SELECT id,state,status FROM $table WHERE post_id = %d", $post_id) ,ARRAY_A)){
			//默认为更新操作
			if(!$status)$status = 2;
			$state = 0;
			//未推
			if($push['state'] == 0){
				//新增
				if($push['status'] == 1){
					//删除，未推送
					if($status == 3){
						$state = 3;
					}else{
						//保持相同操作		
						return true;
					}
				}
			}
			$wpdb->update($table,array('status'=>$status,'state'=>$state),array('id'=>$push['id']));
		}else{
			//删除post，不做任何操作
			if($status == 3)return true;
			//新增、更新post 统一为发布
			$status = 1;
			$wpdb->insert($table,array('post_id'=>$post_id,'status'=>$status,'state'=>0));
		}
		
		//$wpdb 相关操作
		//$wpdb->get_results(sql
		//$wpdb->posts
		//$wpdb->update(tb,array(),array())
		//$wpdb->prepare(
		//$wpdb->get_var(sql
		//$wpdb->get_row(sql
		//$wpdb->update(
		//$wpdb->get_col(sql
		//$wpdb->delete( tb,array())
		//$wpdb->insert(tb,array())
	}
	
}