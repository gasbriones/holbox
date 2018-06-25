<?php
/*
This file is part of Custom Banners.

Custom Banners is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Custom Banners is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Custom Banners.  If not, see <http://www.gnu.org/licenses/>.

Shout out to http://www.makeuseof.com/tag/how-to-create-wordpress-widgets/ for the help
*/

class rotatingBannerWidget extends WP_Widget
{
	function __construct() {
		$widget_ops = array('classname' => 'rotatingBannerWidget', 'description' => 'Displays a rotating banner.' );
		parent::__construct('rotatingBannerWidget', 'Rotating Banner Widget', $widget_ops);
	}

	// PHP4 style constructor for backwards compatibility
	function rotatingBannerWidget() {
		$this->__construct();
	}

	function form($instance)
	{
		$defaults = array( 
			'title' => '',
			'num_banners' => '',
			'timer' => '4000',
			'caption_position' => 'bottom',
			'use_image_tag' => true,
			'transition' => 'none',
			'group' => '',
			'show_pager_icons' => false,
			'pause_on_hover' => false,
			'link_entire_banner' => false,
			'open_link_in_new_window' => false,
			'show_caption' => true,
			'show_cta_button' => true,
			'banner_height' => 'auto',
			'banner_height_px' => '',
			'banner_width' => '100_percent',
			'banner_width_px' => '',
			'auto_height' => '',
			'prev_next' => '',
			'paused' => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title = $instance['title'];
		$num_banners = $instance['num_banners'];
		$transition = $instance['transition'];
		$group = $instance['group'];
		$caption_position = $instance['caption_position'];
		$use_image_tag = $instance['use_image_tag'];
		$show_pager_icons = $instance['show_pager_icons'];
		$timer = $instance['timer'];
		$pause_on_hover = $instance['pause_on_hover'];
		
		$use_image_tag = $instance['use_image_tag'];
		$link_entire_banner = $instance['link_entire_banner'];
		$open_link_in_new_window = $instance['open_link_in_new_window'];
		$show_caption = $instance['show_caption'];
		$show_cta_button = $instance['show_cta_button'];
		$default_banner_width	= get_option('custom_banners_default_width', '');
		$default_banner_height 	= get_option('custom_banners_default_height', '');
		$banner_height = $instance['banner_height'];
		$banner_height_px= intval($instance['banner_height_px']) > 0 ? intval($instance['banner_height_px']) : $default_banner_height;
		$banner_width = $instance['banner_width'];
		$banner_width_px = intval($instance['banner_width_px']) > 0 ? intval($instance['banner_width_px']) : $default_banner_width;
		$auto_height = isset($instance['auto_height']) ? $instance['auto_height'] : '';
		$prev_next = isset($instance['prev_next']) ? $instance['prev_next'] : '';
		$paused = isset($instance['paused']) ? $instance['paused'] : '';
		
		$cycle_transitions = $this->get_cycle_transitions();
		
		
		$ip = isValidCBKey();
		if(!isValidCBKey()) {
			//echo '<p><a href="http://goldplugins.com/our-plugins/custom-banners/" target="_blank">Upgrade</a> to Custom Banners Pro today to unlock this widget and more cool features!</p>';
		}
		?>
		<div class="gp_widget_form_wrapper">
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>">Widget Title:</label><br />
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
			</p>
						
						
			<p>
				<label for="<?php echo $this->get_field_id('caption_position'); ?>">Caption Position:</label>
				<select id="<?php echo $this->get_field_id('caption_position'); ?>" name="<?php echo $this->get_field_name('caption_position'); ?>">
					<option value="left"  <?php if($caption_position == "left"): ?> selected="SELECTED" <?php endif; ?>>Left</option>
					<option value="right"  <?php if($caption_position == "right"): ?> selected="SELECTED" <?php endif; ?>>Right</option>
					<option value="top"  <?php if($caption_position == "top"): ?> selected="SELECTED" <?php endif; ?>>Top</option>
					<option value="bottom"  <?php if($caption_position == "bottom"): ?> selected="SELECTED" <?php endif; ?>>Bottom</option>
				</select>
			</p>
					
			<fieldset class="radio_text_input">
				<legend>Height:</legend> &nbsp;
				<div class="bikeshed bikeshed_radio">								
					<div class="radio_wrapper">
						<p class="radio_option"><label><input class="tog" name="<?php echo $this->get_field_name('banner_height'); ?>" type="radio" value="auto" <?php if($banner_height=='auto'){ ?>checked <?php } ?>> Auto</label></p>
						<p class="radio_option"><label><input class="tog" name="<?php echo $this->get_field_name('banner_height'); ?>" type="radio" value="specify" <?php if($banner_height=='specify'){ ?>checked="checked" <?php } ?>> Specify: <input name="<?php echo $this->get_field_name('banner_height_px'); ?>" type="text" value="<?php echo $banner_height_px; ?>"></label></p>
					</div>
				</div>
			</fieldset>

			<fieldset class="radio_text_input">
				<legend>Width:</legend> &nbsp;
				<div class="bikeshed bikeshed_radio">								
					<div class="radio_wrapper">
						<p class="radio_option"><label><input class="tog" name="<?php echo $this->get_field_name('banner_width'); ?>" type="radio" value="auto" <?php if($banner_width=='auto'){ ?>checked="checked"<?php } ?> > Auto</label></p>
						<p class="radio_option"><label><input class="tog" name="<?php echo $this->get_field_name('banner_width'); ?>" type="radio" value="100_percent" <?php if($banner_width=='100_percent'){ ?>checked="checked"<?php } ?> > 100%</label></p>
						<p class="radio_option"><label><input class="tog" name="<?php echo $this->get_field_name('banner_width'); ?>" type="radio" value="specify" <?php if($banner_width=='specify'){ ?>checked="checked"<?php } ?> > Specify: <input name="<?php echo $this->get_field_name('banner_width_px'); ?>" type="text" value="<?php echo $banner_width_px; ?>"></label></p>
					</div>
				</div>
			</fieldset>
									
			
					
			<fieldset class="radio_text_input">
				<legend>Select Banners To Display:</legend> &nbsp;
				<div class="bikeshed_radio">		
					<p>
						<label for="<?php echo $this->get_field_id('group'); ?>">Banner Group:</label>
						<select id="<?php echo $this->get_field_id('group'); ?>" name="<?php echo $this->get_field_name('group'); ?>">			
							<?php
							$categories = get_terms('banner_groups'); 
							foreach($categories as $category):				
							?>
							<option value="<?php echo $category->slug; ?>" <?php if($group == $category->slug): ?> selected="SELECTED" <?php endif; ?>><?php echo $category->name; ?></option>
							<?php endforeach; ?>
							<option value="" <?php if($group == ""): ?> selected="SELECTED" <?php endif; ?>>All Categories</option>
						</select>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id('num_banners'); ?>">Number of Banners To Display:</label><input class="widefat" id="<?php echo $this->get_field_id('num_banners'); ?>" name="<?php echo $this->get_field_name('num_banners'); ?>" type="text" value="<?php echo esc_attr($num_banners); ?>" />
					</p>
					<p class="description">Tip: leave this field empty to display all of your banners</p>
					
				</div>
			</fieldset>
						
			<fieldset class="radio_text_input">
				<legend>Slideshow Options:</legend> &nbsp;
				<div class="bikeshed_radio">
					<p>
						<label for="<?php echo $this->get_field_id('transition'); ?>">Transition:</label>
						<select name="<?php echo $this->get_field_name('transition'); ?>" id="<?php echo $this->get_field_id('transition'); ?>">	
							<optgroup label="Free Options">
							<?php foreach($cycle_transitions as $key => $transition_options): ?>
								<?php if ($transition_options['pro'] == true) { continue; } ?> 
								<option value="<?php echo $key ?>" <?php if($transition == $key): echo 'selected="SELECTED"'; endif; ?>><?php echo htmlentities($transition_options['label']); ?></option>
							<?php endforeach; ?>
							</optgroup>
							<?php if ($ip): ?>
							<optgroup label="Pro Options">
							<?php else: ?>
							<optgroup label="Pro Options (Upgrade To Unlock!)" disabled="disabled">
							<?php endif; ?>
							<?php foreach($cycle_transitions as $key => $transition_options): ?>
								<?php if ($transition_options['pro'] == false) { continue; } ?> 
								<option value="<?php echo $key ?>" <?php if($transition == $key): echo 'selected="SELECTED"'; endif; ?> <?php if(!$ip): echo 'disabled="disabled"'; endif; ?>><?php echo htmlentities($transition_options['label']); ?></option>
							<?php endforeach; ?>
							</optgroup>
						</select>
						<?php if (!$ip): ?>
						<br />
						<em><a target="_blank" href="http://goldplugins.com/our-plugins/custom-banners/upgrade-to-custom-banners-pro/?utm_source=wp_widgets&utm_campaign=widget_transitions">Upgrade To Unlock All Transitions!</a></em>
						<?php endif; ?>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id('timer'); ?>">Time Between Transitions:</label>
						<select id="<?php echo $this->get_field_id('timer'); ?>" name="<?php echo $this->get_field_name('timer'); ?>">
							<?php foreach(range(1, 60) as $t): ?>
							<?php $my_val = $t . '000'; ?>
							<option value="<?php echo $my_val?>"  <?php if($timer == $my_val): ?> selected="SELECTED" <?php endif; ?>><?php echo $t?> second<?php if ($t > 1): echo 's'; endif; ?></option>
							<?php endforeach; ?>
						</select>
					</p>					
					<!--<p class="description">The time between transitions.  Please Note: 1000 = 1 second.</p>-->
					<p>
						<input class="widefat" id="<?php echo $this->get_field_id('pause_on_hover'); ?>" name="<?php echo $this->get_field_name('pause_on_hover'); ?>" type="checkbox" value="true" <?php if($pause_on_hover){ ?>checked="CHECKED"<?php } ?>/>
						<label for="<?php echo $this->get_field_id('pause_on_hover'); ?>">Pause Slideshow on Hover</label>
					</p>
					<p>
						<input class="widefat" id="<?php echo $this->get_field_id('auto_height'); ?>" name="<?php echo $this->get_field_name('auto_height'); ?>" type="checkbox" value="1" <?php if($auto_height){ ?>checked="CHECKED"<?php } ?>/>
						<label for="<?php echo $this->get_field_id('auto_height'); ?>">Auto-Adjust Height To Fit Each Banner</label>
						<br/>
					</p>	
					<p class="description after_checkbox"><em>If checked, the height of the slideshow will be adjusted to the height of each slide.</em></p>
					<p>
						<input class="widefat" id="<?php echo $this->get_field_id('show_pager_icons'); ?>" name="<?php echo $this->get_field_name('show_pager_icons'); ?>" type="checkbox" value="1" <?php if($show_pager_icons){ ?>checked="CHECKED"<?php } ?>/>
						<label for="<?php echo $this->get_field_id('show_pager_icons'); ?>">Show Pager Icons</label>
					</p>	
					<p>
						<input class="widefat" id="<?php echo $this->get_field_id('prev_next'); ?>" name="<?php echo $this->get_field_name('prev_next'); ?>" type="checkbox" value="1" <?php if($prev_next){ ?>checked="CHECKED"<?php } ?>/>
						<label for="<?php echo $this->get_field_id('prev_next'); ?>">Show Previous and Next Buttons</label>
					</p>
					<p>
						<input class="widefat" id="<?php echo $this->get_field_id('paused'); ?>" name="<?php echo $this->get_field_name('paused'); ?>" type="checkbox" value="1" <?php if($paused){ ?>checked="CHECKED"<?php } ?>/>
						<label for="<?php echo $this->get_field_id('paused'); ?>">Disable Auto Transition</label>
					</p>	
					<p class="description after_checkbox"><em>If checked, the slideshow will only transition to the next slide when manually triggered (i.e., via the Pager Icons or Previous/Next buttons)</em></p>			
				</div>
			</fieldset>
			<fieldset class="radio_text_input">
				<legend>Advanced Options:</legend> &nbsp;
				<div class="bikeshed bikeshed_radio">
					<p>
						<input class="widefat" id="<?php echo $this->get_field_id('show_caption'); ?>" name="<?php echo $this->get_field_name('show_caption'); ?>" type="checkbox" value="1" <?php if($show_caption){ ?>checked="checked"<?php } ?>/>
						<label for="<?php echo $this->get_field_id('show_caption'); ?>">Show Caption Box</label>
					</p>
					<p>
						<input class="widefat" id="<?php echo $this->get_field_id('show_cta_button'); ?>" name="<?php echo $this->get_field_name('show_cta_button'); ?>" type="checkbox" value="1" <?php if($show_cta_button){ ?>checked="checked"<?php } ?>/>
						<label for="<?php echo $this->get_field_id('show_cta_button'); ?>">Show Button</label>
					</p>
					<p>
						<input class="widefat" id="<?php echo $this->get_field_id('link_entire_banner'); ?>" name="<?php echo $this->get_field_name('link_entire_banner'); ?>" type="checkbox" value="1" <?php if($link_entire_banner){ ?>checked="checked"<?php } ?>/>
						<label for="<?php echo $this->get_field_id('link_entire_banner'); ?>">Link Entire Banner</label>
					</p>
					<p>
						<input class="widefat" id="<?php echo $this->get_field_id('open_link_in_new_window'); ?>" name="<?php echo $this->get_field_name('open_link_in_new_window'); ?>" type="checkbox" value="1" <?php if($open_link_in_new_window){ ?>checked="checked"<?php } ?>/>
						<label for="<?php echo $this->get_field_id('open_link_in_new_window'); ?>">Open Links In New Window</label>
					</p>
					<p>
						<input class="widefat" id="<?php echo $this->get_field_id('use_image_tag'); ?>" name="<?php echo $this->get_field_name('use_image_tag'); ?>" type="checkbox" value="1" <?php if($use_image_tag){ ?>checked="checked"<?php } ?>/>
						<label for="<?php echo $this->get_field_id('use_image_tag'); ?>">Use Image Tag</label>
					</p>
				</div>
			</fieldset>
		</div>
		<?php
	}

	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['num_banners'] = $new_instance['num_banners'];
		$instance['caption_position'] = $new_instance['caption_position'];
		$instance['use_image_tag'] = $new_instance['use_image_tag'];
		$instance['show_pager_icons'] = $new_instance['show_pager_icons'];
		$instance['transition'] = $new_instance['transition'];
		$instance['group'] = $new_instance['group'];
		$instance['timer'] = $new_instance['timer'];
		$instance['pause_on_hover'] = $new_instance['pause_on_hover'];
		$instance['link_entire_banner'] = $new_instance['link_entire_banner'];
		$instance['open_link_in_new_window'] = $new_instance['open_link_in_new_window'];
		$instance['show_caption'] = $new_instance['show_caption'];
		$instance['show_cta_button'] = $new_instance['show_cta_button'];
		$instance['banner_height'] = $new_instance['banner_height'];
		$instance['banner_height_px'] = $new_instance['banner_height_px'];
		$instance['banner_width'] = $new_instance['banner_width'];
		$instance['banner_width_px'] = $new_instance['banner_width_px'];		
		
		$instance['auto_height'] = $new_instance['auto_height'];
		$instance['prev_next'] = $new_instance['prev_next'];
		$instance['paused'] = $new_instance['paused'];
		
		return $instance;
	}

	function widget($args, $instance){
		global $ebp;
		
		//defaults
		$atts = array(	
			'title' => '',
			'num_banners' => '',
			'timer' => '4000',
			'caption_position' => 'bottom',
			'use_image_tag' => false,
			'transition' => 'scrollHorz',
			'group' => '',
			'show_pager_icons' => false,
			'pause_on_hover' => false,
			'link_entire_banner' => false,
			'open_link_in_new_window' => false,
			'show_caption' => true,
			'show_cta_button' => true,
			'banner_height' => 'auto',
			'banner_height_px' => '',
			'banner_width' => '100_percent',
			'banner_width_px' => '',
			'auto_height' => '',
			'prev_next' => '',
			'paused' => '');
		
		extract($args, EXTR_SKIP);
		
		$default_banner_width	= get_option('custom_banners_default_width', '');
		$default_banner_height 	= get_option('custom_banners_default_height', '');

		$title 	= empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		$atts['caption_position']	= empty($instance['caption_position']) ? 'bottom' : $instance['caption_position'];
		$atts['use_image_tag']		= empty($instance['use_image_tag']) ? null : $instance['use_image_tag'];
		$atts['show_pager_icons']	= empty($instance['show_pager_icons']) ? null : $instance['show_pager_icons'];
		$atts['count']				= empty($instance['num_banners']) ? '-1' : $instance['num_banners'];
		$atts['transition'] 		= empty($instance['transition']) ? 'scrollHorz' : $instance['transition'];
		$atts['group'] 				= empty($instance['group']) ? '' : $instance['group'];
		$atts['timer'] 				= empty($instance['timer']) ? '4000' : $instance['timer'];
		$atts['pause_on_hover'] 	= empty($instance['pause_on_hover']) ? false : $instance['pause_on_hover'];
		$atts['auto_height'] 		= empty($instance['auto_height']) ? null : $instance['auto_height'];
		$atts['prev_next'] 			= empty($instance['prev_next']) ? null : $instance['prev_next'];
		$atts['paused'] 			= empty($instance['paused']) ? null : $instance['paused'];
		$atts['link_entire_banner'] = empty($instance['link_entire_banner']) ? 0 : $instance['link_entire_banner'];
		$atts['open_link_in_new_window'] = empty($instance['open_link_in_new_window']) ? 0 : $instance['open_link_in_new_window'];
		$atts['show_caption'] 		= empty($instance['show_caption']) ? true : $instance['show_caption'];
		$atts['show_cta_button'] 	= empty($instance['show_cta_button']) ? 0 : $instance['show_cta_button'];
		$atts['banner_height']		= empty($instance['banner_height']) ? 'auto' : $instance['banner_height'];
		$atts['banner_height_px']	= empty($instance['banner_height_px']) ? $default_banner_height : $instance['banner_height_px'];
		$atts['banner_width']		= empty($instance['banner_width']) ? '100_percent' : $instance['banner_width'];
		$atts['banner_width_px']	= empty($instance['banner_width_px']) ? $default_banner_width : $instance['banner_width_px'];
		
		echo $before_widget;
		
		if (!empty($title)){
			echo $before_title . $title . $after_title;;
		}
			
		echo $ebp->banner_shortcode($atts);

		echo $after_widget;
	}
	
	function get_cycle_transitions()
	{
		return array(
			'scrollHorz' => 
				array(
					'label' => 	'Horizontal Scroll',
					'pro'	=>	false
				),
			'scrollVert' => 
				array(
					'label' => 	'Vertical Scroll',
					'pro'	=>	true
				),
			'fade' => 
				array(
					'label' => 	'Fade',
					'pro'	=>	false
				),
			'fadeout' => 
				array(
					'label' => 	'Fade Out',
					'pro'	=>	true
				),
			'carousel' => 
				array(
					'label' => 	'Carousel',
					'pro'	=>	true
				),
			'flipHorz' => 
				array(
					'label' => 	'Horizontal Flip',
					'pro'	=>	true
				),
			'flipVert' => 
				array(
					'label' => 	'Vertical Flip',
					'pro'	=>	true
				),
			'tileslide' => 
				array(
					'label' => 	'Tile Slide',
					'pro'	=>	true
				)
		);
	}
}
?>