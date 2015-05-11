<?php

/**
 * General formatting functions.
 *
 * @group formatting
 * @since 0.1
 */
class Format_General extends PHPUnit_Framework_TestCase {

    /**
     * Data to serialize
     */
    function serialize_data() {
        return array(
            array( null ),
            array( true ),
            array( false ),
            array( -25 ),
            array( 25 ),
            array( 1.1 ),
            array( 'this string will be serialized' ),
            array( "a\nb" ),
            array( array() ),
            array( array(1,1,2,3,5,8,13) ),
            array( (object)array('test' => true, '3', 4) ),
        );
    }

    /**
     * Unserialized data
     */
    function not_serialized_data() {
        return array(
            array( 'a string' ),
            array( 'garbage:a:0:garbage;' ),
            // array( 'b:4;' ), // this test fails in WP test suite, not sure if intentional or what...
            array( 's:4:test;' ),
        );
    }

	/**
	 * Check that yourls_is_serialized detects serialized data
	 *
     * @dataProvider serialize_data
	 * @since 0.1
	 */
	public function test_is_serialized( $data ) {
		$this->assertTrue( yourls_is_serialized( serialize( $data ) ) );
	}
	
	/**
	 * Check that yourls_is_serialized doesn't assume garbage is serialized
	 *
     * @dataProvider not_serialized_data
	 * @since 0.1
	 */
	public function test_is_not_serialized( $data ) {
        $this->assertFalse( yourls_is_serialized( $data ) );
	}
	
	/**
	 * Integer (1337) to string (3jk) to integer
	 *
	 * @since 0.1
	 */
	public function test_int_to_string_to_int() {
		// 10 random integers
		$rnd = array();
		for( $i=0; $i<10; $i++ ) {
			$rnd[]= mt_rand( 1, 1000000 );
		}
	
		foreach( $rnd as $integer ) {
			$this->assertEquals( $integer, yourls_string2int( yourls_int2string( $integer ) ) );
		}
	
	}

	/**
	 * String (3jk) to integer (1337) to string
	 *
	 * @since 0.1
	 */
	public function test_string_to_int_to_string() {
		// 10 random strings that do not start with a zero
		$rnd = array();
		$i = 0;
		while( $i < 10 ) {
			if( $notempty = ltrim( rand_str( mt_rand( 2, 10 ) ), '0' ) ) {
				$rnd[]= $notempty;
				$i++;
			}
		}
	
		foreach( $rnd as $string ) {
			$this->assertEquals( $string, yourls_int2string( yourls_string2int( $string ) ) );
		}
	}

	/**
	 * Generating valid regexp from the allowed charset
	 *
	 * @since 0.1
	 */
    function test_valid_regexp() {
        $pattern = yourls_make_regexp_pattern( yourls_get_shorturl_charset() );
        
        /* To validate a RegExp just run it against null.
           If it returns explicit false (=== false), it's broken. Otherwise it's valid.
           From: http://stackoverflow.com/a/12941133/36850
           Cool to know :)
           
           We're testing it as used in yourls_sanitize_string()
           TODO: more random char strings to test?           
        */
    
        $this->assertFalse( preg_match( '![^' . $pattern . ']!', null ) === false );
    }
    
	/**
	 * Trim long strings
	 *
	 * @since 0.1
	 */
    function test_trim_long_strings() {
        $long = "The Plague That Makes Your Booty Move... It's The Infectious Grooves";
        $trim = "The Plague That Makes Your Booty Move... It's The Infec[...]";
        $this->assertSame( $trim, yourls_trim_long_string( $long ) );

        $long = "The Plague That Makes Your Booty Move... It's The Infectious Grooves";
        $trim = "The Plague That Makes Your Booty[...]";
        $this->assertSame( $trim, yourls_trim_long_string( $long, 37 ) );

        $long = "The Plague That Makes Your Booty Move... It's The Infectious Grooves";
        $trim = "The Plague That Makes Your Booty Mo..";
        $this->assertSame( $trim, yourls_trim_long_string( $long, 37, '..' ) );
    }
 
}
