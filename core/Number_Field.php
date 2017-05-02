<?php

namespace Carbon_Field_Number;

use Carbon_Fields\Field\Field;

class Number_Field extends Field {

	/**
	 * Minimum value
	 *
	 * @var null|float
	 */
	protected $min = null;

	/**
	 * Maximum value
	 *
	 * @var null|float
	 */
	protected $max = null;

	/**
	 * Step/interval between allowed values
	 *
	 * @var null|float
	 */
	protected $step = null;

	/**
	 * Load the field value from an input array based on it's name
	 *
	 * @param array $input Array of field names and values.
	 */
	public function set_value_from_input( $input ) {
		parent::set_value_from_input( $input );

		$value = $this->get_value();
		if ( $value === '' ) {
			return;
		}

		$value = floatval( $value );

		if ( $this->min !== null ) {
			$value = max( $value, $this->min );
		}

		if ( $this->max !== null ) {
			$value = min( $value, $this->max );
		}

		if ( $this->step !== null ) {
			$step_base = ( $this->min !== null ) ? $this->min : 0;
			$is_valid_step_value = ( $value - $step_base ) % $this->step === 0;
			if ( ! $is_valid_step_value ) {
				$value = $step_base; // value is not valid - reset it to a base value
			}
		}

		$this->set_value( $value );
	}

	/**
	 * Enqueue admin scripts.
	 * Called once per field type.
	 */
	static function admin_enqueue_scripts() {
		$template_dir = get_template_directory_uri();

		// Get the current url for the carbon-fields-number, regardless of the location
		$template_dir .= str_replace( wp_normalize_path( get_template_directory() ), '', wp_normalize_path( \Carbon_Field_Number\DIR ) );

		# Enqueue JS
		wp_enqueue_script( 'carbon-field-number', $template_dir . '/assets/js/bundle.js', array( 'carbon-fields-boot' ) );

		# Enqueue CSS
		wp_enqueue_style( 'carbon-field-number', $template_dir . '/assets/css/field.css' );
	}

	/**
	 * Returns an array that holds the field data, suitable for JSON representation.
	 *
	 * @param bool $load  Should the value be loaded from the database or use the value from the current instance.
	 * @return array
	 */
	public function to_json( $load ) {
		$field_data = parent::to_json( $load );

		$field_data = array_merge( $field_data, array(
			'min' => $this->min,
			'max' => $this->max,
			'step' => $this->step,
		) );

		return $field_data;
	}

	/**
	 * Set field minimum value. Default: null
	 *
	 * @param null|float $min
	 * @return Field $this
	 */
	function set_min( $min ) {
		$this->min = floatval( $min );
		return $this;
	}

	/**
	 * Set field maximum value. Default: null
	 *
	 * @param null|float $max
	 * @return Field $this
	 */
	function set_max( $max ) {
		$this->max = floatval( $max );
		return $this;
	}

	/**
	 * Set field step value. Default: null
	 *
	 * @param null|float $step
	 * @return Field $this
	 */
	function set_step( $step ) {
		$this->step = floatval( $step );
		return $this;
	}
}
