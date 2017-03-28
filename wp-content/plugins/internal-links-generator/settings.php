<?php
if(!class_exists('Internal_Links_Generator_Settings')){
    
    class Internal_Links_Generator_Settings{
       
        public function __construct(){
            global $wpdb;
            $this->wpdb = $wpdb;
            $this->settings_tabs = array(
                'keywords' => __('Keywords','ilgen'),
                'links'    => __('URLs','ilgen'),
                'grab'     => __('Grab Links','ilgen'),
                'impex'    => __('Import/Export','ilgen'),
                'asearch'  => __('Search Anchors','ilgen'),
                'settings' => __('Settings','ilgen')
            );
            $this->tab = (isset( $_GET['tab'] )) ? $_GET['tab'] : key($this->settings_tabs);
            $this->options = get_option('ilgen_options');
            // register actions
            add_action('admin_init', array(&$this, 'init'));
            add_action('admin_menu', array(&$this, 'menu'));
            add_action('admin_enqueue_scripts', array(&$this, 'enqueue_scripts'));
        }
		
        /**
         * hook into WP's admin_init action hook
         */
        public function init(){
            wp_register_style('ilgen-style', plugins_url( 'css/style.css', plugin_basename( __FILE__ ) ));
            wp_enqueue_style('ilgen-style');
        }
        
        public function enqueue_scripts(){
            wp_register_script('ilgen-scripts', plugins_url( 'js/scripts.js', plugin_basename( __FILE__ ) ));
            wp_register_script('ilgen-userinc', plugins_url( 'js/userincr.min.js', plugin_basename( __FILE__ ) ));
            wp_enqueue_script('ilgen-scripts');
            wp_enqueue_script('ilgen-userinc');
        }
                
        public function menu(){
            add_options_page(
                'Internal Links Generator Settings', 
                'Internal Links Generator', 
                'manage_options', 
                'internal_links_generator', 
                array(&$this, 'plugin_settings_page')
            );
        }
		
        public function plugin_settings_page(){
            if(!current_user_can('manage_options')){
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
            
            if(isset($_REQUEST['action'])){
                $action = sanitize_title($_REQUEST['action']);
                $nonce = "internal_link_generator-$action";
                if(isset($_REQUEST['_wpnonce']) && !wp_verify_nonce($_REQUEST['_wpnonce'], $nonce)){
                    die( 'Security check failure!' ); 
                }
                else{
                    if((function_exists ('check_admin_referer')))
                        check_admin_referer($nonce);
                }
                $this->$action();
            }
            
            $template_data = array(
                'options' => get_option('ilgen_options') 
            );
            
            switch($this->tab){
                case 'keywords':
                    $template_data['keywords'] = $this->wpdb->get_results(
                        "SELECT * FROM `".$this->wpdb->prefix."internalinks` ORDER BY keyword ASC"
                    );
                break;
                case 'links':
                    $template_data['targets'] = $this->ilgen_get_targets();
                break;
                case 'grab':
                    $template_data['grabbs'] = $this->ilgen_grabb_links();
                break;
                case 'asearch':
                    $template_data['asearch'] = $this->asearch();
                break;
            }
            
            $this->ilgen_get_page($template_data);
        }
        
        /* action function */
        
        public function simple_import($param = 'keyword'){
            if(!empty($_POST['import_string'])){
                $values = array();
                $values = str_replace("\r\n", ',', $_POST['import_string']);
                $values = explode(',', $values);
                $values = array_map('trim', @array_filter($values));
                
                if(isset($_POST['param'])) 
                    $param = (string)$_POST['param'];
                
                if(isset($_POST['target'])) 
                    $target = esc_url($_POST['target']);
                else $target = null;
                
                foreach($values as $value){
                    $this->{"ilgen_insert_$param"}($value, $target);
                }
                
                $this->ilgen_messages(1, 'updated');
            }
        }
        
        public function advanced_import(){
            if(!empty($_POST['import_string'])){
                $rows = @array_filter(explode(';', $_POST['import_string']));
                if(!empty($rows)){
                    foreach($rows as $row){
                        $row = @array_map('trim', @array_filter(explode('|', $row)));
                        $this->ilgen_insert_keyword($row[0], $row[1], $row[2]);
                    }
                    $this->ilgen_messages(2, 'updated');
                }
            }
        }
        
        public function export(){
            $string = '';
            $file_url = sprintf("%s/keywords.txt", dirname(__FILE__));
            $rows = $this->wpdb->get_results(
                "SELECT * FROM `".$this->wpdb->prefix."internalinks`"
            );
            if(!empty($rows)){
                $string .= __('Keyword', 'ligen') . "\t" . __('Target URL', 'ligen') . "\t" . __('Limit', 'ligen') . "\n";
                foreach($rows as $row){
                    $string .= html_entity_decode($row->keyword) . "\t" . $row->target . "\t" . $row->limit . "\n";
                }
            }
            if($this->ilgen_is_writable($file_url) && '' != $string) {
                if(file_put_contents($file_url, $string)) {
                    $this->ilgen_messages(6, 'updated');
                }
            }
        }
        
        public function update($id = 0){
            if($id > 0){
                if($this->ilgen_from_table('target', $id) != esc_url($_POST['targets'][$id])){
                    $this->unlinking($id);
                }
                $result = $this->ilgen_insert_keyword( 'keyword', 
                    $_POST['targets'][$id], $_POST['limits'][$id], $_POST['tags'][$id], $id
                );
            }
            return $result;
        }
        
        public function delete($id = 0){
            if($id > 0){
                $result = $this->wpdb->delete( 
                    $this->wpdb->prefix.'internalinks', 
                    array( 'id' => $id ) 
                );
            }
            return $result;
        }
        
        public function bulk(){
            if(!empty($_POST['ids'])):
                foreach($_POST['ids'] as $id):
                    switch($_POST['bulk_action']){
                        case 'update': $this->update($id); break;
                        case 'recount': $this->recount($id); break;
                        case 'linking': $this->linking($id); break;
                        case 'unlinking': $this->unlinking($id); break;
                        case 'delete': $this->unlinking($id); $this->delete($id); break;
                        case 'asearch_add': $this->ilgen_insert_keyword($_POST['formed'][$id], $_POST['target'][$id]); break;
                    }
                endforeach;
                $this->ilgen_messages(3, 'updated');
            else:
                $this->ilgen_messages(3, 'warning');
            endif;
        }
        
        public function linking($id){
            $row = $this->wpdb->get_row( 
                sprintf("SELECT * FROM `".$this->wpdb->prefix."internalinks` WHERE id = '%d' LIMIT 1", $id)
            );
            $linked_posts = (array)unserialize($row->posts);
            $keyword      = html_entity_decode($row->keyword);
            $linked_limit = $row->limit;
            $target       = $row->target;
            $qty          = $row->linked;
            $tag_open     = ($row->tag) ? "<$row->tag>" : '';
            $tag_close    = ($row->tag) ? "</$row->tag>" : '';
            
            if($keyword && '' != $target){
                $exclude_tags = implode('|', array('a', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'pre', 'code', 'kbd'));
                $search_regex = "/(?<!\p{L})($keyword)(?!\p{L})(?!(?:(?!<\/?(?:$exclude_tags).*?>).)*<\/(?:$exclude_tags).*?>)(?![^<>]*>)/ui";
                $url_regex = sprintf("/<a href=\"%s\" class=\"ilgen\">(.*)<\/a>/iu", preg_quote($target, '/'));
                                               
                foreach(get_post_types(array('public' => true), 'names') as $post_type){
                    if(empty($this->options['allowed_pt']) || in_array($post_type, $this->options['allowed_pt'])){
                        $posts = $this->wpdb->get_results( "SELECT ID, post_content FROM `".$this->wpdb->prefix."posts` WHERE post_type = '$post_type' AND post_status = 'publish'" );
                        if(!empty($posts)){
                            foreach($posts as $p){
                                $this->ilgen_numlinks($p->post_content);
                                $permalink = get_the_permalink($p->ID);
                                
                                if(!in_array($p->ID, $linked_posts) 
                                  && ($qty < $linked_limit || 0 == $linked_limit) 
                                  && stristr($p->post_content, $keyword) 
                                  && $this->ilgen_numlinks($p->post_content)
                                  && $target != $permalink){

                                    @preg_match($url_regex, $p->post_content, $match);
                                    if(empty($match)){
                                        $content = preg_replace($search_regex, '<a href="'.$target.'" class="ilgen">'.$tag_open.'$0'.$tag_close.'</a>', $p->post_content, 1, $count);
                                        if($count > 0){
                                            if($this->wpdb->query($this->wpdb->prepare(
                                            "UPDATE `".$this->wpdb->prefix."posts` SET post_content = '%s' WHERE ID = '%d' AND post_status = 'publish'", 
                                            $content, $p->ID))){
                                                $qty += $count;
                                                $linked_posts[] = $p->ID;
                                            }
                                            unset($count, $content); 
                                        }
                                    }
                                    else continue;
                                }
                                else continue;
                            }
                        }
                    }
                }
            }

            $result = $this->wpdb->query($this->wpdb->prepare(
                "UPDATE `".$this->wpdb->prefix."internalinks` SET `linked` = '%d', `posts` = '%s' WHERE id = '%d'",
                $qty, serialize($linked_posts), $id
            ));
            
            return $result;
        }
                
        public function unlinking($id){
            $row = $this->wpdb->get_row( 
                sprintf("SELECT * FROM `".$this->wpdb->prefix."internalinks` WHERE id = '%d' LIMIT 1", $id)
            );
            $linked_posts = (array)unserialize($row->posts);
            $keyword      = html_entity_decode($row->keyword);
            $target       = $row->target;
            $qty          = $row->linked;
            $tag_open     = ($row->tag) ? "<$row->tag>" : '';
            $tag_close    = ($row->tag) ? "<\/$row->tag>" : '';
            
            $url_regex = sprintf("/<a href=\"%s\" class=\"ilgen\">$tag_open(.*)$tag_close<\/a>/iu", preg_quote($target, '/'));
            
            if(!empty($linked_posts)){
                foreach($linked_posts as $k => $pid){
                    $content = get_post_field('post_content', $pid);
                    @preg_match($url_regex, $content, $match);
                    if( mb_convert_case(trim($match[1]), MB_CASE_LOWER, "UTF-8") == $keyword ){
                        $content = preg_replace($url_regex, "$1", $content, 1, $count);
                        if($count > 0 ){
                            if($this->wpdb->query($this->wpdb->prepare("UPDATE `".$this->wpdb->prefix."posts` SET post_content = '%s' WHERE ID = '%d' AND post_status = 'publish'", $content, $pid))){
                                $qty -= $count;
                                unset($linked_posts[$k]);
                            }
                            unset($count, $content); 
                        }
                    }
                }
            }
            $result = $this->wpdb->query($this->wpdb->prepare(
                "UPDATE `".$this->wpdb->prefix."internalinks` SET `linked` = '%d', `posts` = '%s' WHERE id = '%d'",
                $qty, serialize($linked_posts), $id
            ));
            
            return $result;
        }
        
        public function grabb(){
            if(!empty($_POST['ids'])){
                $data = $this->ilgen_grabb_links();
                
                foreach($_POST['ids'] as $gid){
                    
                    $target  = esc_url($data[$gid][2]);
                    $pid     = absint($data[$gid][0]);
                    $tag     = $data[$gid][1];
                    $content = get_post_field('post_content', $pid);
                    
                    $check = $this->ilgen_check_exists(
                        $this->ilgen_prepare_keyword($data[$gid][3])
                    );
                    if(!$check){
                        $this->ilgen_insert_keyword($data[$gid][3], $target);
                        $check_id = $this->wpdb->insert_id;
                    }else{
                        $check_id = $check;
                    }
                    $row = $this->wpdb->get_row(
                        sprintf("SELECT * FROM `".$this->wpdb->prefix."internalinks` WHERE id = '%d' LIMIT 1", $check_id)
                    );
                    
                    $linked_posts = (array)unserialize($row->posts);
                    $target  = ('' != $row->target) ? $row->target : $target;
                    $url_regex = sprintf("/<a href=\"%s\" class=\"ilgen\">(.*)<\/a>/iu", preg_quote($target, '/'));
                    @preg_match($url_regex, $content, $match);
                    
                    if(!in_array($pid, (array)$linked_posts) && empty($match) && '' != $target){
                        $tag_open     = ($row->tag) ? "<$row->tag>" : '';
                        $tag_close    = ($row->tag) ? "</$row->tag>" : '';
            
                        $content = str_replace( $tag, 
                            sprintf( '<a href="%s" class="ilgen">%s%s%s</a>', 
                                $target, $tag_open, $data[$gid][3], $tag_close),
                        $content );
                        $linked_posts[] = $pid;
                        $linked_posts = serialize(array_filter($linked_posts));
                        
                        $this->wpdb->query($this->wpdb->prepare("UPDATE `".$this->wpdb->prefix."internalinks` SET ".
                            "`limit` = '%d', `linked` = '%d', `posts` = '%s' WHERE id = '%d'", 
                            $row->limit + 1, $row->linked + 1, $linked_posts, $row->id
                        ));
                    }
                    else{
                        $content = str_replace( $data[$gid][1], $data[$gid][3], $content );
                    }
                    $this->wpdb->query($this->wpdb->prepare(
                        "UPDATE `".$this->wpdb->prefix."posts` SET post_content = '%s' WHERE ID = '%d' AND post_status = 'publish'",
                        $content, $data[$gid][0]
                    ));
                }
                $this->ilgen_messages(7, 'updated');
            }
            else $this->ilgen_messages(7, 'warning');
        }
        
        public function recount($id){
            $qty = 0;
            if($keyword = $this->ilgen_from_table('keyword', $id)){
                $keyword = html_entity_decode($keyword);
                
                foreach(get_post_types(array('public' => true), 'names') as $post_type){
                    if(empty($this->options['allowed_pt']) || in_array($post_type, $this->options['allowed_pt'])){
                        $posts = $this->wpdb->get_results( "SELECT ID, post_content FROM `".$this->wpdb->prefix."posts` WHERE post_type = '$post_type' AND post_status = 'publish'" );
                        if(!empty($posts)){
                            foreach($posts as $p){
                                if(@preg_match_all('/(?<!\p{L})'.$keyword.'(?!\p{L})(?!([^<]+)?>)/iu', $p->post_content, $matches)){
                                    $qty += count($matches[0]);
                                }
                            }
                        }
                    }
                }
            }
            if($qty > 0){
                $result = $this->wpdb->query($this->wpdb->prepare(
                    "UPDATE `".$this->wpdb->prefix."internalinks` SET `count` = '%d' WHERE id = '%d'",
                    $qty, $id
                ));
            }
            return $qty;
        }
        
        public function asearch(){
            $data = array();
            if($keyword = sanitize_text_field($_POST['keyword'])){
                $limits = array('before' => absint($_POST['before']), 'after' => absint($_POST['after']));
                
                if($key_phrases = $this->ilgen_search_anchor($keyword, $limits)){
                    foreach($key_phrases as $ind => $phrase){
                        $words = explode(" ", $phrase);
                        foreach($words as $k => $word){
                            $key_class = (stristr($keyword, $word)) ? 'ilgen-keyword' : '';
                            $words[$k] = sprintf(
                                '<a href="#" class="ilgen-found notin ' . $key_class 
                                    . '" id="formed_%1$d_set_%3$d" data-id="formed_%1$d" data-num="%3$d">%2$s</a>',
                                $ind, $word, $k
                            ); 
                        }
                        $data[$ind] = array('words' => implode(" ", $words)); 
                    }
                }
            }
            return $data;
        }
        
		public function targets_edit(){
			
			if($_POST['target_old'] && $_POST['target_new']){
				
				$new = esc_url($_POST['target_new']);
				if($data = $this->ilgen_get_targets(array((object)array('target' => $_POST['target_old'])))){
					foreach($data as $dt){
						if($dt->keywords){
							foreach($dt->keywords as $k){
								$this->unlinking($k->id);
								$this->ilgen_insert_keyword($k->keyword, $new, $k->limit, $k->tag, $k->id, $k->count);
								$this->linking($k->id);
							}
						}
					}
					$this->ilgen_messages(11, 'updated');
				}else{
					$this->ilgen_messages(11, 'warning');
				}
			}else{
				$this->ilgen_messages(11, 'warning');
			}
		}
		
        public function settings(){
            if( update_option('ilgen_options', array(
                'numlinks'   => absint($_POST['numlinks']),
                'allowed_pt' => array_map('sanitize_title', $_POST['allowed_pt'])))){
                
                $this->ilgen_messages(10, 'updated');
            }else{
                $this->ilgen_messages(10, 'warning');
            }
        }
        
        public function ajax(){
            switch($_POST['type']){
                case 'asearch_add':
                    $this->ilgen_insert_keyword($_POST['keyword'], $_POST['target']);
                break;
                case 'keywords_del':
                    $id = absint($_POST['id']);
                    $this->unlinking($id);
                    $this->delete($id);
                break;
                default: wp_die();
            }
        }
                
        /* support functions */
        
        public function ilgen_check_exists($value, $column = 'keyword'){
            
            $row = $this->wpdb->get_row( sprintf(
                "SELECT id FROM `".$this->wpdb->prefix."internalinks` WHERE %s = '%s' LIMIT 1", $column, $value
            ));
            
            if(is_null($row)) return false;
            else return $row->id;
        }
        
        public function ilgen_insert_keyword($keyword, $target = '', $limit = 1, $tag = '', $id = null, $count = null){
            if(is_null($id)){
                $keyword = $this->ilgen_prepare_keyword($keyword);
                $check_id = $this->ilgen_check_exists($keyword);
                
                if(!$check_id && $keyword){
                    $query = $this->wpdb->prepare(
                        "INSERT INTO `".$this->wpdb->prefix."internalinks` (`keyword`, `target`, `limit`) ".
                        "VALUES ('%s', '%s', '%d')", $keyword, esc_url($target), absint($limit)
                    );
                }
            }
            else{
                $query = $this->wpdb->prepare("UPDATE `".$this->wpdb->prefix."internalinks` SET ".
                    "`target` = '%s', `limit` = '%d', `tag` = '%s' WHERE id = '%d'", 
                    esc_url($target), absint($limit), $tag, absint($id)
                );
            }
            
            $result = $this->wpdb->query($query);
            return $result;
        }
        
        public function ilgen_insert_target($target){
            $target = esc_url($target);
            $check_id = $this->ilgen_check_exists($target, 'target');
            if(!$check_id){
                $query = $this->wpdb->prepare(
                    "INSERT INTO `".$this->wpdb->prefix."internalinks` (`target`) ".
                    "VALUES ('%s')", $target
                );
            }
            
            $result = $this->wpdb->query($query);
            return $result;
        }
        
        public function ilgen_get_targets($targets = array()){
            
			$data = array();
			
            if(empty($targets)){
				$targets = $this->wpdb->get_results(
					"SELECT DISTINCT target FROM `".$this->wpdb->prefix."internalinks`"
				);
			}
            if(!empty($targets)){
                foreach($targets as $t){
                    $t->keywords = $this->wpdb->get_results(
                        "SELECT * FROM `".$this->wpdb->prefix."internalinks` WHERE target = '$t->target' ORDER BY keyword ASC"
                    );
                    $data[] = $t;
                }
            }
            
            return $data;
        }
        
        public function ilgen_get_page($template_data = array()){?>
            <div class="ilgen wrap">
				<div class="ilgen-donate">
					<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GM7YSW282RC4L" target="_blank">
						<img src="<?= plugins_url( 'images/donate.png', plugin_basename( __FILE__ ) )?>">
					</a>
				</div>
				<h2><?php _e('Internal Links Generator', 'ilgen')?></h2>
                <h3 class="nav-tab-wrapper">
                <?php foreach ( $this->settings_tabs as $tab_key => $tab_caption ):
                    $active = ($this->tab == $tab_key) ? 'nav-tab-active' : '';?>
                    <a class="nav-tab <?= $active?>" href="options-general.php?page=internal_links_generator&tab=<?= $tab_key?>"><?= $tab_caption ?></a>
                    <?php endforeach;?>
                </h3>
                <?php @include(sprintf("%s/templates/%s.php", dirname(__FILE__), $this->tab));?>
            </div>
            
        <?php }
               
        public function ilgen_from_table($column, $id){
            $row = $this->wpdb->get_row( 
                sprintf("SELECT `%s` FROM `".$this->wpdb->prefix."internalinks` WHERE id = '%d' LIMIT 1", $column, $id), ARRAY_N
            );
            
            if(is_null($row)) return false;
            else return $row[0];
        }
        
        public function ilgen_grabb_links(){
            $data = array();
            foreach(get_post_types(array('public' => true), 'names') as $post_type){
                if(empty($this->options['allowed_pt']) || in_array($post_type, $this->options['allowed_pt'])){
                    $posts = $this->wpdb->get_results( "SELECT ID, post_content FROM `".$this->wpdb->prefix."posts` WHERE post_type = '$post_type' AND post_status = 'publish'" );
                    if(!empty($posts)){
                        foreach($posts as $p){
                            @preg_match_all("/<a.*?href=[\"|'](.*?)[\"|'].*?>(.*?)<\/a>/iu", $p->post_content, $matches);
                            if(!empty($matches[0])){
                                for($i=0; $i<count($matches[0]); $i++){
                                    if(!strpos($matches[0][$i], 'ilgen')){
                                        $data[] = array( $p->ID, $matches[0][$i], $matches[1][$i], strip_tags($matches[2][$i]) );
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $data;
        }
        
        public function ilgen_search_anchor($keyword, $limits){
            $data = array();
            
            for($i=0; $i<$limits['before']; $i++){
                $before .= '(?:[\w+]+)\s';
            }
            for($j=0; $j<$limits['after']; $j++){
                $after .= '\s(?:[\w-]+)';
            }
            
            if(strpos($keyword, '*')){
                $keyword = str_replace('*', '', $keyword);
                $pattern = '/%s(?<!\p{L})%s([^\W|\s]+)%s/iu';
            }else{
                $pattern = '/%s(?<!\p{L})%s(?!\p{L})%s/iu';
            }
            
            foreach(get_post_types(array('public' => true), 'names') as $post_type){
                if(empty($this->options['allowed_pt']) || in_array($post_type, $this->options['allowed_pt'])){
                    $posts = $this->wpdb->get_results( "SELECT ID, post_content FROM `".$this->wpdb->prefix."posts` WHERE post_type = '$post_type' AND post_status = 'publish'" );
                    if(!empty($posts)){
                        foreach($posts as $p){
                            preg_match_all( sprintf($pattern, $before, $keyword, $after), $p->post_content, $matches );
                            if($matches[0]){
                                $data[] = mb_convert_case(trim($matches[0][0]), MB_CASE_LOWER, "UTF-8" );
                            }
                            unset($matches);
                        }
                    }
                }
            }
            
            return array_unique($data);
        }
        
        public function ilgen_numlinks($content = ''){
            $check = true;
            if($this->options['numlinks'] > 0){
                @preg_match_all("/class=\"ilgen\"/iu", $content, $matches);
                if(sizeof($matches, 1) - 1 >= $this->options['numlinks']) $check = false;
            }
            return $check;
        }
        
        public function ilgen_prepare_keyword($keyword){
            
            $keyword = mb_convert_case($keyword, MB_CASE_LOWER, "UTF-8");
            $keyword = sanitize_text_field($keyword);
            $keyword = htmlentities($keyword);
            
            return $keyword;
        }
        
        public function ilgen_messages($num, $type = ''){
            if('updated' === $type){
                switch($num){
                    case 1: $details = __('Keywords imported!', 'ilgen'); break;
                    case 2: $details = __('Keywords imported!', 'ilgen'); break;
                    default: $details = '';
                }
                $message = sprintf(__('Operation is successfull! %s', 'ilgen'), $details);
            }
            else{
                switch($num){
                    case 1: $details = __('Keywords not imported!', 'ilgen'); break;
                    case 2: $details = __('Keywords not imported!', 'ilgen'); break;
                    default: $details = '';
                }
                $message = sprintf(__('Operation currupted! %s', 'ilgen'), $details);
            }
            echo '<div id="message" class="' . $type . '" notice is-dismissible"><p>' . $message . '</p></div>';
        }
        
        public function ilgen_is_writable($filename) {
            if(!is_writable($filename)) {
                if(!@chmod($filename, 0666)) {
                    $pathtofilename = dirname($filename);
                    if(!is_writable($pathtofilename)) {
                        if(!@chmod($pathtoffilename, 0666)) {
                            return false;
                        }
                    }
                }
            }
            return true;
        }
    }
}
