<?php
/*
 * Name: FT-NONCE-LIB
 * Created By: Full Throttle Development, LLC (http://fullthrottledevelopment.com)
 * Created On: July 2009
 * Last Modified On: August 12, 2009
 * Last Modified By: Glenn Ansley (glenn@fullthrottledevelopment.com)
 * Version: 0.2
 */

/*
Copyright 2009 Full Throttle Development, LLC

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
define( 'NONCE_UNIQUE_KEY' , '517873fc1ed97' );
define( 'NONCE_DURATION' , 36000); // 36000 makes link or form good for 600 minutes (10 hours) from time of generation
define( 'NONCE_KEY' , '_nonce' );

/** This method creates a key / value pair for a url string */
function nonce_create_query_string( $action = '' , $user = '' ){
  return NONCE_KEY."=".nonce_create( $action , $user );
}

/** This method creates an nonce for a form field */
function nonce_create_form_input( $action = '' , $user='' ){
  echo "<input type='hidden' name='".NONCE_KEY."' value='".nonce_create( $action . $user )."' />";
}

/**
 * This method creates an nonce. It should be called by one of the previous two functions.
 * @access private
 */
function nonce_create( $action = '' , $user='' ){
  return substr( nonce_generate_hash( $action . $user ), -12, 10);
}

/** This method validates an nonce*/
function nonce_is_valid( $nonce , $action = '' , $user='' ){
  // Nonce generated 0-12 hours ago
  if ( substr(nonce_generate_hash( $action . $user ), -12, 10) == $nonce ){
    return true;
  }
  return false;
}

/** This method generates the nonce timestamp */
function nonce_generate_hash( $action='' , $user='' ){
  $i = ceil( time() / ( NONCE_DURATION / 2 ) );
  return md5( $i . $action . $user . $action );
}

if ( NONCE_UNIQUE_KEY == '' ){ die( 'You must enter a unique key to use this library.'); }
