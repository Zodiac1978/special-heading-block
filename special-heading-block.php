<?php
/**
 * Plugin Name: Special Heading Block
 * Description: A PHP-only heading block with gradient and outline highlighting.
 * Plugin URI:  https://torstenlandsiedel.de
 * Version:     1.0.0
 * Author:      Torsten Landsiedel
 * Author URI:  https://torstenlandsiedel.de
 * License:     GPL 2
 * License URI: http://opensource.org/licenses/GPL-2.0
 * Requires at least: 7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SPECIAL_HEADING_BLOCK_VERSION', '1.0.0' );
define( 'SPECIAL_HEADING_BLOCK_NAME', 'special-heading-block/special-heading' );

/**
 * Register the PHP-only block and its frontend stylesheet.
 */
function special_heading_register_block() {
	special_heading_register_block_type( SPECIAL_HEADING_BLOCK_NAME );

	wp_enqueue_block_style(
		SPECIAL_HEADING_BLOCK_NAME,
		array(
			'handle' => 'special-heading-block',
			'src'    => plugin_dir_url( __FILE__ ) . 'style.css',
			'path'   => plugin_dir_path( __FILE__ ) . 'style.css',
			'ver'    => SPECIAL_HEADING_BLOCK_VERSION,
		)
	);
}
add_action( 'init', 'special_heading_register_block' );

/**
 * Register a Special Heading block name.
 *
 * @param string $block_name Block name to register.
 * @param array  $supports_override Supports to merge into the default supports.
 */
function special_heading_register_block_type( $block_name, $supports_override = array() ) {
	register_block_type(
		$block_name,
		array(
			'title'       => __( 'Special Heading', 'special-heading-block' ),
			'description' => __( 'A heading with an optional gradient or outline highlight.', 'special-heading-block' ),
			'category'    => 'text',
			'icon'        => 'heading',
			'keywords'    => array(
				__( 'heading', 'special-heading-block' ),
				__( 'gradient', 'special-heading-block' ),
				__( 'highlight', 'special-heading-block' ),
			),

			'attributes' => array(
				'level' => array(
					'type'    => 'integer',
					'enum'    => array( 1, 2, 3, 4, 5, 6 ),
					'default' => 2,
					'label'   => __( 'Heading level', 'special-heading-block' ),
				),
				'textBefore' => array(
					'type'    => 'string',
					'default' => __( 'Create blocks with', 'special-heading-block' ),
					'label'   => __( 'Text before highlight', 'special-heading-block' ),
				),
				'highlightedText' => array(
					'type'    => 'string',
					'default' => __( 'PHP only', 'special-heading-block' ),
					'label'   => __( 'Highlighted text', 'special-heading-block' ),
				),
				'textAfter' => array(
					'type'    => 'string',
					'default' => __( 'in WordPress 7.0', 'special-heading-block' ),
					'label'   => __( 'Text after highlight', 'special-heading-block' ),
				),
				'outline' => array(
					'type'    => 'boolean',
					'default' => false,
					'label'   => __( 'Use outline', 'special-heading-block' ),
				),
			),

			'supports'    => array_merge(
				array(
					'autoRegister' => true,
					'align'        => array( 'wide', 'full' ),
					'color'        => array(
						'text'                            => true,
						'background'                      => true,
						'gradients'                       => true,
						'__experimentalSkipSerialization' => array( 'gradients' ),
					),
					'spacing'      => array(
						'margin'   => true,
						'padding'  => true,
						'blockGap' => true,
					),
					'border'       => array(
						'color'  => true,
						'radius' => true,
						'style'  => true,
						'width'  => true,
					),
					'dimensions'   => array(
						'minHeight' => true,
					),
					'typography'   => array(
						'fontSize'                     => true,
						'lineHeight'                   => true,
						'textAlign'                    => true,
						'textIndent'                   => true,
						'__experimentalFontFamily'     => true,
						'__experimentalFontStyle'      => true,
						'__experimentalFontWeight'     => true,
						'__experimentalLetterSpacing'  => true,
						'__experimentalTextDecoration' => true,
						'__experimentalTextTransform'  => true,
					),
				),
				$supports_override
			),

			'render_callback' => 'special_heading_render_block',
		)
	);
}

/**
 * Render the gradient heading.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function special_heading_render_block( $attributes ) {
	$level = isset( $attributes['level'] ) ? (int) $attributes['level'] : 2;
	$level = min( 6, max( 1, $level ) );

	$text_before     = isset( $attributes['textBefore'] ) ? trim( $attributes['textBefore'] ) : '';
	$highlighted     = isset( $attributes['highlightedText'] ) ? trim( $attributes['highlightedText'] ) : '';
	$text_after      = isset( $attributes['textAfter'] ) ? trim( $attributes['textAfter'] ) : '';
	$is_outline      = ! empty( $attributes['outline'] );
	$heading_classes = 'special-heading-block';

	if ( $is_outline ) {
		$heading_classes .= ' is-outline';
	}

	$parts = array();

	if ( '' !== $text_before ) {
		$parts[] = '<span class="special-heading-block__text">' . esc_html( $text_before ) . '</span>';
	}

	if ( '' !== $highlighted ) {
		$highlight_style = special_heading_get_gradient_style( $attributes );
		$style_attribute = $highlight_style ? ' style="' . esc_attr( $highlight_style ) . '"' : '';

		$parts[] = sprintf(
			'<span class="special-heading-block__highlight"%1$s>%2$s</span>',
			$style_attribute,
			esc_html( $highlighted )
		);
	}

	if ( '' !== $text_after ) {
		$parts[] = '<span class="special-heading-block__text">' . esc_html( $text_after ) . '</span>';
	}

	$wrapper_attributes = get_block_wrapper_attributes(
		array( 'class' => $heading_classes )
	);

	return sprintf(
		'<h%1$d %2$s>%3$s</h%1$d>',
		$level,
		$wrapper_attributes,
		implode( ' ', $parts )
	);
}

/**
 * Convert the native Gradient control value to a CSS custom property.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function special_heading_get_gradient_style( $attributes ) {
	if ( ! empty( $attributes['gradient'] ) ) {
		$slug = sanitize_title( $attributes['gradient'] );

		return '--special-heading-gradient:var(--wp--preset--gradient--' . $slug . ')';
	}

	$custom_gradient = $attributes['style']['color']['gradient'] ?? '';

	if ( ! is_string( $custom_gradient ) || '' === trim( $custom_gradient ) ) {
		return '';
	}

	return safecss_filter_attr( '--special-heading-gradient:' . $custom_gradient );
}

/**
 * Load the same stylesheet in the editor preview.
 */
function special_heading_enqueue_editor_style() {
	wp_enqueue_style(
		'special-heading-block',
		plugin_dir_url( __FILE__ ) . 'style.css',
		array(),
		SPECIAL_HEADING_BLOCK_VERSION
	);
}
add_action( 'enqueue_block_editor_assets', 'special_heading_enqueue_editor_style' );
