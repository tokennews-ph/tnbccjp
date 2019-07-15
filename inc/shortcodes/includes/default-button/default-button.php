<?php
/**
 * Default button shortcode.
 *
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'DT_Shortcode_Default_Button', false ) ) {

	class DT_Shortcode_Default_Button extends DT_Shortcode_With_Inline_Css {
		public static $instance;

		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {
			$this->sc_name = 'dt_default_button';
			$this->unique_class_base = 'default-btn';
			$this->default_atts = array(
				'size'                 => 'small',
				'font_size'            => '14px',
				'button_padding'       => '12px 18px 12px 18px',
				'border_radius'        => '1px',
				'link'                 => '',
				'default_btn_bg_color' => '',
				'bg_hover_color'       => '',
				'text_color'           => '',
				'text_hover_color'     => '',
				'animation'            => 'none',
				'icon_type'            => 'html',
				'icon'                 => '',
				'icon_picker'          => '',
				'icon_size'            => '11px',
				'icon_align'           => 'left',
				'button_alignment'     => 'btn_inline_left',
				'smooth_scroll'        => 'n',
				'btn_width'            => 'btn_auto_width',
				'custom_btn_width'     => '200px',
				'el_class'             => '',
				'css'                  => '',
			);
			parent::__construct();
		}

		protected function do_shortcode( $atts, $content = '' ) {
			$content = trim( preg_replace( '/<\/?p\>/', '', $content ) );

			$icon_html = '';
			$icon_type = $this->atts['icon_type'];
			if ( $icon_type !== 'none' ) {
				if ( 'html' === $icon_type ) {
					if ( preg_match( '/^fa[a-z]*\s/', $this->atts['icon'] ) ) {
						$icon_html = '<i class="' . esc_attr( $this->atts['icon'] ) . '"></i>';
					} else {
						$icon_html = wp_kses( rawurldecode( base64_decode( $this->atts['icon'] ) ), array( 'i' => array( 'class' => array() ) ) );
					}
				} elseif ( ! empty( $this->atts["icon_{$icon_type}"] ) ) {
					$icon_html = '<i class="' . esc_attr( $this->atts["icon_{$icon_type}"] ) . '"></i>';
				}
			}

			$after_title = $before_title = '';
			if ( 'right' === $this->atts['icon_align'] ) {
				$after_title = $icon_html;
			} else {
				$before_title = $icon_html;
			}

			$btn_width = '';
			if ('btn_fixed_width' ===  $this->atts['btn_width'] ) {
				$btn_width .= ' style="width:' . absint( $this->atts['custom_btn_width'] ) . 'px;"' ;
			}

			$url = $this->atts['link'] ? $this->atts['link'] : '#';
			$link_title = $target = $rel = '';
			if ( function_exists( 'vc_build_link' ) ) {
				$link = vc_build_link( $this->atts['link'] );
				if ( ! empty( $link['url'] ) ) {
					$url = $link['url'];
					$target = ( empty( $link['target'] ) ? '' : sprintf( ' target="%s"', trim( $link['target'] ) ) );
					$link_title = ( empty( $link['title'] ) ? '' : sprintf( ' title="%s"', $link['title'] ) );
					$rel = ( empty( $link['rel'] ) ? '' : sprintf( ' rel="%s"', $link['rel'] ) );
				}
			}

			// get button html
			$button_html = presscore_get_button_html( array(
				'before_title'	=> $before_title,
				'after_title'	=> $after_title,
				'href'			=> esc_attr( $url ),
				'title'			=> $content,
				'target'		=> $target,
				'class'			=> $this->get_html_class(),
				'atts'			=> ' id="' . $this->get_unique_class() . '"'  . $btn_width  . $link_title . $rel ,
			) );

			switch ( $this->atts['button_alignment'] ) {
				case 'btn_left':
					$button_html = '<div class="btn-align-left">' . $button_html . '</div>';
					break;
				case 'btn_center':
					$button_html = '<div class="btn-align-center">' . $button_html . '</div>';
					break;
				case 'btn_right':
					$button_html = '<div class="btn-align-right">' . $button_html . '</div>';
					break;
			}

			echo $button_html;
		}

		protected function get_html_class() {
			// static classes
			$classes = array( 'default-btn-shortcode dt-btn' );
			switch ( $this->atts['size'] ) {
				case 'small':
					$classes[] = 'dt-btn-s';
					break;
				case 'medium':
					$classes[] = 'dt-btn-m';
					break;
				case 'big':
					$classes[] = 'dt-btn-l';
					break;
			};
			// animation
			if ( presscore_shortcode_animation_on( $this->atts['animation'] ) ) {
				$classes[] = presscore_get_shortcode_animation_html_class( $this->atts['animation'] );
				$classes[] = 'animation-builder';
			}

			$icon_type = $this->atts['icon_type'];
			if ( 'html' === $icon_type ) {
				$there_is_an_icon = ! empty( $this->atts['icon'] );
			} else {
				$there_is_an_icon = ! empty( $this->atts["icon_{$icon_type}"] );
			}

			// icon alignment
			if ( $there_is_an_icon && 'right' === $this->atts['icon_align'] ) {
				$classes[] = 'ico-right-side';
			}

			// smooth scroll
			if ( $this->get_flag( 'smooth_scroll' ) ) {
				$classes[] = 'anchor-link';
			}

			// custom class
			if ( $this->atts['el_class'] ) {
				$classes[] = $this->atts['el_class'];
			}

			if ('btn_full_width' ===  $this->atts['btn_width'] ) {
				$classes[] = 'full-width-btn';
			}

			switch ( $this->atts['button_alignment'] ) {
				case 'btn_inline_left':
					$classes[] = 'btn-inline-left';
					break;
				case 'btn_inline_right':
					$classes[] = 'btn-inline-right';
					break;
			}

			if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
				$classes[] = vc_shortcode_custom_css_class( $this->atts['css'], ' ' );
			}

			return  esc_attr( implode( ' ', $classes ) );
		}

		/**
		 * Setup theme config for shortcode.
		 */
		protected function setup_config() {
		}

		/**
		 * Return array of prepared less vars to insert to less file.
		 *
		 * @return array
		 */
		protected function get_less_vars() {
			$less_vars = the7_get_new_shortcode_less_vars_manager();

			$less_vars->add_keyword( 'unique-shortcode-class-name',  $this->get_unique_class(), '~"%s"' );

			$less_vars->add_keyword( 'default-btn-bg', $this->get_att( 'default_btn_bg_color', '~""') );
			$less_vars->add_keyword( 'default-btn-bg-hover', $this->get_att( 'bg_hover_color', '~""' ) );

			$less_vars->add_keyword( 'default-btn-color', $this->get_att( 'text_color', '~""' ) );
			$less_vars->add_keyword( 'default-btn-color-hover', $this->get_att( 'text_hover_color', '~""' ) );
			$less_vars->add_pixel_number( 'default-btn-icon-size', $this->get_att( 'icon_size' ) );

			if ( $this->get_att( 'size' ) === 'custom' ) {
				$less_vars->add_pixel_number( 'default-btn-font-size', $this->get_att( 'font_size' ) );
				$less_vars->add_pixel_number( 'default-btn-border-radius', $this->get_att( 'border_radius' ) );
				$less_vars->add_paddings( array(
					'default-btn-padding-top',
					'default-btn-padding-right',
					'default-btn-padding-bottom',
					'default-btn-padding-left',
				), $this->get_att( 'button_padding' ) );
			}

			return $less_vars->get_vars();
		}

		protected function get_less_file_name() {
			// @TODO: Remove in production.
			$less_file_name = 'default-buttons';
			$less_file_path = trailingslashit( get_template_directory() ) . "css/dynamic-less/shortcodes/{$less_file_name}.less";

			return $less_file_path;
		}

		/**
		 * Return dummy html for VC inline editor.
		 *
		 * @return string
		 */
		protected function get_vc_inline_html() {
            return false;
		}

	}
	DT_Shortcode_Default_Button::get_instance()->add_shortcode();
}
