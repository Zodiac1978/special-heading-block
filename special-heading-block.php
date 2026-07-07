<?php
/**
 * Plugin Name: Special Heading Block
 * Description: A PHP-only heading block with gradient and outline highlighting.
 * Plugin URI:  https://torstenlandsiedel.de
 * Version:     1.0.0
 * Author:      Torsten Landsiedel
 * Author URI:  https://torstenlandsiedel.de
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 7.0
 * Requires PHP: 7.4
 *
 * @package Special_Heading_Block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SPECIAL_HEADING_BLOCK_VERSION', '1.0.0' );
define( 'SPECIAL_HEADING_BLOCK_NAME', 'special-heading-block/special-heading' );
define( 'SPECIAL_HEADING_BLOCK_MIN_GRADIENT_ANGLE', 0 );
define( 'SPECIAL_HEADING_BLOCK_MAX_GRADIENT_ANGLE', 360 );
define( 'SPECIAL_HEADING_BLOCK_DEFAULT_GRADIENT_ANGLE', 135 );

/**
 * Register the PHP-only block and its frontend stylesheet.
 */
function special_heading_register_block() {
	special_heading_register_block_type();

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
 * Register the Special Heading block type.
 */
function special_heading_register_block_type() {
	register_block_type(
		SPECIAL_HEADING_BLOCK_NAME,
		array(
			'title'       => __( 'Special Heading', 'special-heading-block' ),
			'description' => __( 'A heading with an optional gradient or outline highlight.', 'special-heading-block' ),
			'category'    => 'text',
			'icon'        => 'heading',
			'keywords'    => array(
				__( 'heading', 'special-heading-block' ),
				__( 'gradient', 'special-heading-block' ),
				__( 'highlight', 'special-heading-block' ),
				__( 'outline', 'special-heading-block' ),
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
				'gradientAngle' => array(
					'type'    => 'integer',
					'default' => SPECIAL_HEADING_BLOCK_DEFAULT_GRADIENT_ANGLE,
					'label'   => __( 'Gradient angle', 'special-heading-block' ),
				),
			),

			'supports'   => array(
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
	$gradient_style  = special_heading_get_gradient_style( $attributes );

	if ( $is_outline ) {
		$heading_classes .= ' is-outline';
	}

	$parts = array();

	if ( '' !== $text_before ) {
		$parts[] = '<span class="special-heading-block__text">' . esc_html( $text_before ) . '</span>';
	}

	if ( '' !== $highlighted ) {
		$parts[] = sprintf(
			'<span class="special-heading-block__highlight">%s</span>',
			esc_html( $highlighted )
		);
	}

	if ( '' !== $text_after ) {
		$parts[] = '<span class="special-heading-block__text">' . esc_html( $text_after ) . '</span>';
	}

	$extra_attributes = array( 'class' => $heading_classes );

	if ( '' !== $gradient_style ) {
		$extra_attributes['style'] = $gradient_style;
	}

	$wrapper_attributes = get_block_wrapper_attributes(
		$extra_attributes
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
	$gradient_angle = special_heading_get_gradient_angle( $attributes );
	$style          = array(
		'--special-heading-gradient-angle:' . $gradient_angle . 'deg',
	);

	if ( ! empty( $attributes['gradient'] ) ) {
		$slug     = sanitize_title( $attributes['gradient'] );
		$gradient = special_heading_get_gradient_preset( $slug );

		if ( '' !== $gradient ) {
			$style[] = '--special-heading-gradient:' . special_heading_set_gradient_angle( $gradient, $gradient_angle );
		} else {
			$style[] = '--special-heading-gradient:var(--wp--preset--gradient--' . $slug . ')';
		}

		return safecss_filter_attr( implode( ';', $style ) );
	}

	$custom_gradient = $attributes['style']['color']['gradient'] ?? '';

	if ( ! is_string( $custom_gradient ) || '' === trim( $custom_gradient ) ) {
		return safecss_filter_attr( implode( ';', $style ) );
	}

	$style[] = '--special-heading-gradient:' . special_heading_set_gradient_angle( $custom_gradient, $gradient_angle );

	return safecss_filter_attr( implode( ';', $style ) );
}

/**
 * Get a normalized gradient angle.
 *
 * @param array $attributes Block attributes.
 * @return int
 */
function special_heading_get_gradient_angle( $attributes ) {
	$gradient_angle = isset( $attributes['gradientAngle'] )
		? (int) $attributes['gradientAngle']
		: SPECIAL_HEADING_BLOCK_DEFAULT_GRADIENT_ANGLE;

	return min(
		SPECIAL_HEADING_BLOCK_MAX_GRADIENT_ANGLE,
		max( SPECIAL_HEADING_BLOCK_MIN_GRADIENT_ANGLE, $gradient_angle )
	);
}

/**
 * Get a gradient preset by slug from global WordPress settings.
 *
 * @param string $slug Gradient preset slug.
 * @return string
 */
function special_heading_get_gradient_preset( $slug ) {
	if ( ! function_exists( 'wp_get_global_settings' ) ) {
		return '';
	}

	$gradients = wp_get_global_settings( array( 'color', 'gradients' ) );

	return special_heading_find_gradient_preset( $gradients, $slug );
}

/**
 * Recursively find a gradient preset in a global settings array.
 *
 * @param mixed  $value Global settings value.
 * @param string $slug Gradient preset slug.
 * @return string
 */
function special_heading_find_gradient_preset( $value, $slug ) {
	if ( ! is_array( $value ) ) {
		return '';
	}

	if (
		isset( $value['slug'], $value['gradient'] )
		&& $slug === $value['slug']
		&& is_string( $value['gradient'] )
	) {
		return $value['gradient'];
	}

	foreach ( $value as $child ) {
		$gradient = special_heading_find_gradient_preset( $child, $slug );

		if ( '' !== $gradient ) {
			return $gradient;
		}
	}

	return '';
}

/**
 * Apply an angle to a linear gradient.
 *
 * @param string $gradient Gradient CSS value.
 * @param int    $angle Gradient angle.
 * @return string
 */
function special_heading_set_gradient_angle( $gradient, $angle ) {
	$gradient = trim( $gradient );
	$pattern  = '/linear-gradient\(\s*('
		. '(?:-?\d*\.?\d+deg)'
		. '|(?:to\s+(?:top|bottom|left|right)(?:\s+(?:top|bottom|left|right))?)'
		. ')\s*,/i';

	if ( ! preg_match( '/^\s*linear-gradient\s*\(/i', $gradient ) ) {
		return $gradient;
	}

	if ( preg_match( $pattern, $gradient ) ) {
		$updated_gradient = preg_replace(
			$pattern,
			'linear-gradient(' . $angle . 'deg,',
			$gradient,
			1
		);

		return is_string( $updated_gradient ) ? $updated_gradient : $gradient;
	}

	$updated_gradient = preg_replace( '/linear-gradient\(\s*/i', 'linear-gradient(' . $angle . 'deg, ', $gradient, 1 );

	return is_string( $updated_gradient ) ? $updated_gradient : $gradient;
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
