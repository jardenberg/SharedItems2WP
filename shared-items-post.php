<?php

/*
Plugin Name: Shared Items Post
Plugin URI: http://www.googletutor.com/shared-items-post/
Description: Scheduled automatic posting of Google Reader Shared Items.
Version: 1.3.0
Author:  Craig Fifield, Google Tutor
Author URI: http://www.googletutor.com/ 
*/

require_once(ABSPATH.WPINC.'/class-snoopy.php');
require_once(ABSPATH.WPINC.'/rss.php');
require_once(dirname(__FILE__).'/simplepie.php');

function gd_compare_items($a, $b) {
    if ($a["entry_time"] == $b["entry_time"]) {
        return 0;
    }
    return ($a["entry_time"] < $b["entry_time"]) ? -1 : 1;
}

if (!class_exists('SharedItemsPost')) {
    class SharedItemsPost
    {
        var $log_file = "c:/log.txt";
		
		var $options_key = 'shared-items-post-options';
		var $google_feed_url = 'http://www.google.com/reader/public/atom/user/%s/state/com.google/broadcast';
		
        var $plugin_url;
        var $plugin_path;
        var $status = "";

        var $o;

        var $default_options = array(
            'revision' => 11,
            'share_url' => '',
			'share_id'	=> '',
            'feed_url' => '',
            'refresh_period' => 'weekly',
            'refresh_time' => '06:00 AM',
            'post_title' => 'Shared Items - %DATE%',
            'post_header_template' => '&lt;ul&gt;',
            'post_footer_template' => '&lt;/ul&gt;',
            'post_item_template' => '&lt;li&gt;&lt;a href=&quot;%LINK%&quot;&gt;%TITLE%&lt;/a&gt;&lt;/li&gt;&lt;br&gt;%DATE% %NOTE%',
            'post_note_template' => '- %CONTENT%',
            'post_category' => 1,
            'post_tags' => '',
            'post_author' => 1,
            'post_comments' => 1,
            'last_crawl' => 0,
            'last_refresh' => 0,
            'last_refresh_feed' => 0
        );
        
        function SharedItemsPost() {
            $this->plugin_path_url();
            $this->install_plugin();
            $this->actions_filters();
        }

        function dump($msg, $object, $mode = "a+") {
            $obj = print_r($object, true);
            $f = fopen($this->log_file, $mode);
            fwrite ($f, sprintf("[%s] : %s\r\n", current_time('mysql'), $msg));
            fwrite ($f, "$obj");
            fwrite ($f, "\r\n");
            fclose($f);
        }

        function plugin_path_url() {
			$this->plugin_path = dirname(__FILE__).'/';
			$this->plugin_url = WP_PLUGIN_URL.'/' . basename(dirname(__FILE__)) . '/';
        }

		function install_plugin() {
			$this->o = get_option($this->options_key);
						
			if (!is_array($this->o) || empty($this->o) ) {
				update_option($this->options_key, $this->default_options);
				$this->o = get_option($this->options_key);
			}
			else {
				$this->o = $this->o + $this->default_options;
				$this->o["revision"] = $this->default_options["revision"];
				update_option( $this->options_key, $this->o);
			}
		}

		function actions_filters() {
			add_action('init', array(&$this, 'init'));
			add_action('admin_menu', array(&$this, 'admin_menu'));
			add_action('admin_head', array(&$this, 'admin_head'));
		}

		function get_feed_url($share_url) {
// 			$file = $this->get_file_contents($share_url);
// 			preg_match('/xml".href="(.+?)"/', $file, $matches);
// 			return $matches[1];
			$share_id = ( isset ( $this->o['share_id'] ) && !empty ( $this->o['share_id']{19} ) ) ? $this->o["share_id"] : $this->get_share_id ( $share_url );
			return sprintf ( $this->google_feed_url, $share_id );
		}

		function get_feed_contents($feed_url) {
			$file = $this->get_file_contents($feed_url);
			$rss = new MagpieRSS($file);
			return $rss;
		}

		function get_file_contents($url) {
			$client = new Snoopy();
			$client->agent = MAGPIE_USER_AGENT;
			$client->read_timeout = MAGPIE_FETCH_TIME_OUT;
			$client->use_gzip = MAGPIE_USE_GZIP;
			$client->fetch($url);
			$file = $client->results;
			return $file;
		}
		
		/**
		* function to return share_id from share_url (instead of parsing it from the page itself...)
		* @param string $share_url
		* @return string|false share_id parsed from share_url
		*/
		
		function get_share_id ( $share_url ) {
		
			preg_match_all ( '/(http|https):\/\/(www.)?google.com\/reader\/shared\/([0-9]+)\/?/i', $share_url, $matches );
			
			if ( isset ( $matches[3][0] ) && !empty ( $matches[3][0]{19} ) )
				return $matches[3][0];
			
			return false;
		}

		function init() {
			if ($_POST['action'] == 'runow') {
			
				check_admin_referer('shared-2');
				$this->generate_post();
				
			}
			else {
				if ($_POST['action'] == 'reset') {
						check_admin_referer('shared-3');
						
					$this->o = $this->default_options;
					update_option("shared-items-post-options", $this->default_options);
				}
				
				if ($_POST['action'] == 'save') {
				
					check_admin_referer('shared-1');
						
					$this->o["post_title"] = $_POST['post_title'];
					$this->o["post_tags"] = $_POST['post_tags'];
					$this->o["post_header_template"] = stripslashes(htmlentities($_POST['post_header_template'], ENT_QUOTES, 'UTF-8'));
					$this->o["post_footer_template"] = stripslashes(htmlentities($_POST['post_footer_template'], ENT_QUOTES, 'UTF-8'));
					$this->o["post_item_template"] = stripslashes(htmlentities($_POST['post_item_template'], ENT_QUOTES, 'UTF-8'));
					$this->o["post_note_template"] = stripslashes(htmlentities($_POST['post_note_template'], ENT_QUOTES, 'UTF-8'));
					$this->o["post_author"] = $_POST['post_author'];
					$this->o["post_category"] = $_POST['post_category'];
					$this->o["post_comments"] = isset($_POST['post_comments']) ? 1 : 0;
					$this->o["refresh_period"] = $_POST['refresh_period'];
					$this->o["refresh_time"] = $_POST['refresh_time'];

					
					update_option("shared-items-post-options", $this->o);

					$share_url = str_replace("https://", "http://", $_POST['share_url']);
					
					if ($share_url != $this->o["share_url"]) {
						$url = $this->get_feed_url($share_url);
						if ($url != "") {
							$this->o["share_url"] = $share_url;
							$this->o["share_id"] = $this->get_share_id ( $share_url );
							$this->o["feed_url"] = $url;
							update_option($this->options_key, $this->o);
							$this->status = "ok";
						}
						else
							$this->status = "Feed URL not found.";
					}
				}

				if ($this->check_refresh()) 
					$this->generate_post();
			}
		}
        
        function generate_post() {
        	 	if ($this->o["feed_url"] == "") return;
        	 
            $rss = new SimplePie();
            $rss->set_feed_url($this->o["feed_url"]);
            $rss->enable_cache(false);
            $rss->enable_order_by_date(false);
            $rss->init();
            
            $updated = $rss->get_channel_tags("http://www.w3.org/2005/Atom", "updated");
            $last_update = strtotime(str_replace(array("T", "Z"), " ", $updated[0]["data"]));
          
            $new_items = array();
            $post_content = "";
            $newtime=0;
            
            if ($this->o["last_refresh_feed"] < $last_update) {
                foreach ($rss->get_items() as $item) {
                	                	                 
 	
                    $entry_time = strtotime($item->get_local_date());
                    $crawl_time = $item->data["attribs"]["http://www.google.com/schemas/reader/atom/"]["crawl-timestamp-msec"];
 							
 										if ($newtime==0)
                	  	$newtime=$crawl_time;
                	  
                	  	
                    if ($this->o["last_crawl"] < $crawl_time) {
                        $new_item["crawl_time"] = $crawl_time;
                        $new_item["entry_time"] = $entry_time;
                        $new_item["title"] = $item->get_title();
                        $new_item["link"] = $item->get_link();
                        $annotation = $item->get_item_tags("http://www.google.com/schemas/reader/atom/", "annotation");
                        if (isset($annotation)) {
                            $note = html_entity_decode($this->o["post_note_template"]);
                            $note = str_replace('%CONTENT%', $annotation[0]["child"]["http://www.w3.org/2005/Atom"]["content"][0]["data"], $note);
                            $note = str_replace('%AUTHOR%', $annotation[0]["child"]["http://www.w3.org/2005/Atom"]["author"][0]["child"]["http://www.w3.org/2005/Atom"]["name"][0]["data"], $note);
                        }
                        else $note = "";
                        $new_item["note"] = $note;
                        
                        $new_items[] = $new_item;
                    }
                }
                
                if (count($new_items) > 0) {
                    foreach ($new_items as $item) {
                        $item_date = date(get_option("date_format"), $item["entry_time"]);
                        $import = html_entity_decode($this->o["post_item_template"]);
                        $import = str_replace('%DATE%', $item_date, $import);
                        $import = str_replace('%NOTE%', $item["note"], $import);
                        $import = str_replace('%TITLE%', $item["title"], $import);
                        $import = str_replace('%LINK%', $item["link"], $import);
                        $post_content.= $import;
                    }
                }
            }
            
            if ($post_content != "") {
                $post_title = html_entity_decode($this->o["post_title"]);
                $post_title = str_replace('%DATE%', date(get_option("date_format")), $post_title);
                $post_header = html_entity_decode($this->o["post_header_template"]);
                $post_footer = html_entity_decode($this->o["post_footer_template"]);
                
                $new_post = array();
                
                $new_post['comment_status'] = $this->o["post_comments"] == 1 ? 'open' : 'closed';
                $new_post['post_author'] = $this->o["post_author"];
                $new_post['post_content'] = $post_header.$post_content.$post_footer;
                $new_post['post_status'] = 'publish';
                $new_post['post_title'] = $post_title;
                $new_post['post_type'] = 'post';
                $new_post['post_category'] = array($this->o["post_category"]);
                $new_post['tags_input'] = $this->o["post_tags"];

                wp_insert_post($new_post);
                $this->o["last_refresh_feed"] = $last_update;
                $this->o["last_refresh"] = mktime();
                $this->o["last_crawl"] =$newtime;
                
                update_option($this->options_key, $this->o);
            }
        }
        
        function check_refresh() {
            if ($this->o["feed_url"] != "") {
                if ($this->o["last_refresh"] == 0) return true;
                $pdate = $this->o["last_refresh"];
                $timeparts = $this->convert_time($this->o["refresh_time"]);
                switch ($this->o["refresh_period"]) {
                    case "monthly":
                        $next = mktime(0 + $timeparts[0], 0 + $timeparts[1], 0, date("m", $pdate) + 1, date("d", $pdate), date("Y", $pdate));
                        break;
                    case "weekly":
                        $next = mktime(0 + $timeparts[0], 0 + $timeparts[1], 0, date("m", $pdate), date("d", $pdate) + 7, date("Y", $pdate));
                        break;
                    case "daily":
                        $next = mktime(0 + $timeparts[0], 0 + $timeparts[1], 0, date("m", $pdate), date("d", $pdate) + 1, date("Y", $pdate));
                        break;
                }
               
                if (mktime() >= $next) return true;
                else return false;
            }
            else return false;
        }
        
        function convert_time($timer) {
            $tp = split(" ", $timer);
            if (count($tp) == 2) {
                if ($tp[1] == "PM") {
                    $tt = split(":", $tp[0]);
                    $tt[0] = $tt[0] + 12;
                    return $tt;
                }
                return split(":", $tp[0]);
            }
            else return split(":", $timer);
        }

        function admin_menu() {
            add_submenu_page('options-general.php','Shared Items Post', 'Shared Items Post', 9, __FILE__, array($this, 'options_panel'));
        }

        function admin_head() {
            echo('<link rel="stylesheet" href="'.$this->plugin_url.'shared-items-post.css" type="text/css" media="screen" />');			
			echo '<script src="' . $this->plugin_url . 'shared-items-panel.js" type="text/javascript"></script>';
        }
        
        function options_panel() {
            $options = $this->o;
            $status = $this->status;
			
			 
            
            include($this->plugin_path.'shared-items-panel.php');
        }
    }
    
    $rssShare = new SharedItemsPost();
}

?>