<?php

class Cornerstone_Styling extends Cornerstone_Plugin_Component {

  public $dependencies = array( 'Font_Manager', 'Color_Manager' );
  public $styles = array();
  public $minify = array();
  public $count = 0;

  public function add_styles($name, $css, $minify = true ) {
    $this->styles[$name] = $css;
    $this->minify[$name] = $minify;
  }

  public function get_generated_styles() {

    $styles = '/* ';

    // Use a custom error handler to wrap PHP errors in CSS comments
    set_error_handler( array( $this, 'error_handler' ) );

    foreach ($this->styles as $key => $style) {
      $styles .= ++$this->count ." start: $key*/";
      $styles .= $this->post_process( $style, $this->minify[$key] );
      $styles .= "/*end:$key|";
    }

    $styles .= '*/';
    restore_error_handler();

    $this->plugin->component( 'Font_Manager' )->load_queued_fonts();
    return $styles;

  }

  public function error_handler( $errno, $errstr, $errfile, $errline) {

    if ( ! ( error_reporting() & $errno ) ) {
      return false;
    }

    $title = "Unknown Error ";
    switch ($errno) {
      case E_USER_WARNING:
        $title = "PHP Warning [$errno] ";
        break;

      case E_USER_NOTICE:
        $title = "PHP Notice [$errno] ";
        break;
    }

    echo '/*' . $title . str_replace('/*', '/\*', str_replace('*/', '*\/', $errstr ) ) . '*/';
    return true;
  }

  public function external_post_process( $css, $minify = false) {
    set_error_handler( array( $this, 'error_handler' ) );
    $buffer = $this->post_process( $css, $minify );
    restore_error_handler();
    return $buffer;
  }

  public function post_process( $css, $minify = true ) {
    $output = preg_replace_callback('/%%post ([\w-:]+?)%%(.*?)%%\/post%%/', array( $this, 'post_process_replacer' ), $css );
    return ( $minify ) ? $this->clean_css( $output ) : $output;
  }

	public function post_process_replacer( $matches ) {
    return apply_filters('cornerstone_css_post_process_' . $matches[1], $matches[2]);
	}

  public function clean_css( $css ) {
    //
    // 1. Remove comments.
    // 2. Remove whitespace.
    // 3. Remove starting whitespace.
    //
    $css = preg_replace( '#/\*.*?\*/#s', '', $css );         // 1
	  $css = preg_replace( '/\s*([{}|:;,])\s+/', '$1', $css ); // 2
	  return preg_replace( '/\s\s+(.*)/', '$1', $css );        // 3
  }
}
