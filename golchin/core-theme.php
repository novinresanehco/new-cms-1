<?php

/* * *************************************************************************
 *                                  CMS CMS
 *                          -------------------
 *   copyright            : (C) 2009 The samaneh  $Team = "www.samaneh.com";
 *   email                : info@samaneh.com
 *   email                : samaneh@gmail.com
 *   programmer           : Reza Shahrokhian
 * ************************************************************************* */
//	 Security
if ( !defined( 'news_security' ) )
{
    die( "You are not allowed to access this page directly!" );
}

class samaneh
{

    var $tags          = array();
    var $required_tags = array();
    var $blocks        = array();
    var $tpl           = '';
    var $parsed_tpl    = '';

    function load( $tpl = '' )
    {
        if ( !empty( $tpl ) )
        {
            //check if requested file is a plugin theme
            if ( strpos( $tpl, "plugins/" ) !== false )
            {
                //check if a local theme is exists
                $fname = explode( '/', $tpl );
                $fname = end( $fname );

                if ( strpos( $fname, '.php' ) === false && strpos( '.htaccess', $fname ) === false && strpos( '.pl', $fname ) === false )
                {
                    //get plugin Name
                    $matches = null;
                    if ( preg_match( '#plugins\/([a-zA-Z0-9]+)\/#', $tpl, $matches ) )
                    {
                        $matches         = $matches[1];
                        $pluginThemeFile = ThemeDir . 'plugins/' . $matches . '_' . $fname;
                        if ( $matches == 'recentpostbycat' )
                        {
                            if ( preg_match( '#([0-9]+)\_(.+)#i', $fname, $fname ) )
                            {
                                $catid                 = $fname[1];
                                $fname                 = $fname[2];
                                $secondpluginThemeFile = ThemeDir . 'plugins/' . $matches . '_' . $catid . '_' . $fname;
                                $pluginThemeFile       = ThemeDir . 'plugins/' . $matches . '_' . $fname;
                                if ( file_exists( $secondpluginThemeFile ) )
                                {
                                    $tpl = $secondpluginThemeFile;
                                }
                                else
                                if ( file_exists( $pluginThemeFile ) )
                                {

                                    $tpl = $pluginThemeFile;
                                }
                            }
                        }
                        else
                        if ( file_exists( $pluginThemeFile ) )
                        {
                            $tpl = $pluginThemeFile;
                        }
                    }
                }
            }
            if ( !file_exists( $tpl ) )
            {
                $this->tplerror( 'Could not find the theme file <b>' . $tpl . "</b>" );
                return FALSE;
            }
            $tplfile   = file_get_contents( $tpl );
            $tplfile   = $tplfile ? $tplfile : $this->tplerror( 'Could not read the theme file! : ' . htmlspecialchars( $tpl ) );
            $this->tpl = $tplfile;
        }
    }

    function tplerror( $error )
    {
        echo $error;
        return FALSE;
    }

    function raw()
    {
        return $this->tpl;
    }

    function assign( $input, $value = '', $required = FALSE )
    {
        if ( is_array( $input ) )
        {
            foreach ( $input as $tag => $value )
            {
                if ( empty( $tag ) )
                    $this->tplerror( 'S A M A N E H::The tag name shouldnt be empty.' );
                if ( $required == TRUE )
                {
                    $this->required_tags[$tag] = $value;
                }
                else
                {
                    $this->tags[$tag] = $value;
                }
            }
        }
        elseif ( is_string( $input ) )
        {
            if ( empty( $input ) )
                $this->tplerror( 'S A M A N E H::The tag name shouldnt be empty.' );
            else
            {
                if ( $required == TRUE )
                {
                    $this->required_tags[$input] = $value;
                }
                else
                {
                    $this->tags[$input] = $value;
                }
            }
        }
        else
        {
            return FALSE;
        }
        return TRUE;
    }

    function resetBlock( $block_name )
    {
        if ( !is_string( $block_name ) || empty( $block_name ) )
        {
            $this->tplerror( 'S A M A N E H::Block name is not a string or is empty!' );
        }
        else
        {
            $this->blocks[$block_name] = array();
        }
    }

    /* ----------------------------------------------------------------- */

    function block( $block_name, $block_array )
    {
        if ( !is_string( $block_name ) || empty( $block_name ) )
            $this->tplerror( 'S A M A N E H::Block name is not a string or is empty!' );

        if ( !is_array( $block_array ) )
            $this->tplerror( 'S A M A N E H::Block array is not an array!' );
        $this->blocks[$block_name][] = $block_array;
    }

    /* ----------------------------------------------------------------- */

    function parse()
    {
        if ( empty( $this->tpl ) )
        {
            return;
        }
        if ( !isset( $d ) )
        {
            global $d;
            $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='menumaker' LIMIT 1", true );
        }
        /* assign [config_name] values */
        if ( !defined( 'admin' ) )
        {
            global $config;
            preg_match_all( '#\[config\_(.+)\]#iU', $this->tpl, $matches );
            for ( $i = 0, $c = count( $matches[1] ); $i < $c; $i++ )
            {
                if ( isset( $config[$matches[1][$i]] ) )
                {
                    $this->tpl = str_ireplace( '[config_' . $matches[1][$i] . ']', $config[$matches[1][$i]], $this->tpl );
                }
            }
        }

        # blocks
        $tmp_blocknames = array();
        foreach ( $this->blocks as $block_name => $block_arrays )
        {
            if ( $anzahl = preg_match_all( '/<tag:' . preg_quote( $block_name, '/' ) . '>(.*)<\/tag:' . preg_quote( $block_name, '/' ) . '>/sU', $this->tpl, $matches ) )
            {
                for ( $i = 0; $i < $anzahl; $i++ )
                {
                    $block_plus_definition = $matches[0][$i];
                    $block                 = $matches[1][$i];
                    if ( is_int( strpos( $block, '<!-- IF' ) ) )
                    {
                        $parse_control_structures = TRUE;
                    }

                    $parsed_block = '';
                    foreach ( $block_arrays as $block_array )
                    {
                        $tmp = $block;
                        if ( isset( $parse_control_structures ) )
                        {
                            $tmp = $this->_parse_control_structures( $tmp, array_merge( $block_array, $this->tags, $this->required_tags ) );
                        }
                        foreach ( $block_array as $tag_name => $tag_value )
                        {
                            $tmp = str_replace( '[' . $tag_name . ']', $tag_value, $tmp );
                        }
                        $parsed_block .= $tmp;
                    }
                    $this->tpl        = str_replace( $block_plus_definition, $parsed_block, $this->tpl );
                    $tmp_blocknames[] = $block_name;
                    unset( $parse_control_structures );
                }
            }
        }
        if ( count( $this->blocks ) > 0 )
        {
            $this->tpl = preg_replace( "/<(tag|\/tag):(" . implode( '|', $tmp_blocknames ) . ")>/", '', $this->tpl );
        }
        # unbenutze blcke entfernen
        $this->tpl = preg_replace( "/<tag:([a-zA-Z0-9_-]+)>.*<\/tag:\\1>( |\r|\n)?/msU", '', $this->tpl );
        if ( $q > 0 )
        {
            if ( !defined( 'IN_NSMENU' ) )
            {
                define( 'IN_NSMENU', 1 );
            }
            require( dirname( __FILE__ ) . '/../plugins/menumaker/menu.init.php');
        }
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='form' LIMIT 1", true );
        if ( $q > 0 )
        {
            if ( !defined( 'IN_Form' ) )
            {
                define( 'IN_Form', 1 );
                require_once( dirname( __FILE__ ) . '/../plugins/form/form.init.php');
            }
            $this->tpl = detectForm( $this->tpl );
        }
        # single tags
        foreach ( $this->required_tags as $tag_name => $tag_value )
        {
            if ( !is_int( strpos( $this->tpl, $tag_name ) ) )
                $this->tplerror( 'S A M A N E H::Could not find tag <i>' . $tag_name . '</i> in the theme file!' );
            else
            {
                $this->tpl = str_replace( '[' . $tag_name . ']', $tag_value, $this->tpl );
            }
        }
        foreach ( $this->tags as $tag_name => $tag_value )
        {
            $this->tpl = str_replace( '[' . $tag_name . ']', $tag_value, $this->tpl );
        }

        # if & else
        $this->tpl = $this->_parse_control_structures(
                $this->tpl, array_merge( $this->tags, $this->required_tags ), $this->blocks
        );



        if ( function_exists( 'rssreader_output' ) )
        {
            $this->tpl = rssreaderParser( $this->tpl );
        }
        if ( function_exists( 'recentpostbycat_output' ) )
        {
            $this->tpl = recentpostbycatParser( $this->tpl );
        }
        if ( function_exists( 'weather_output' ) )
        {
            $this->tpl = weatherParser( $this->tpl );
        }
        //remove unauthorized permissions
        preg_match_all( '#<permission:([a-z]+)>(.*)</permission:\1>#iUs', $this->tpl, $matches );
        for ( $i = 0, $c = count( $matches[1] ); $i < $c; $i++ )
        {
            if ( hasPermission( $matches[1][$i] ) )
            {
                $this->tpl = preg_replace( "#<permission:" . $matches[1][$i] . ">(.*)</permission:" . $matches[1][$i] . ">#iUs", '\1', $this->tpl );
            }
        }
        $this->tpl = preg_replace( "#<permission:([a-z]+)>(.*)</permission:\\1>#iUs", "", $this->tpl );
        //end remove unauthorized permissions
        //remove not installed plugin contents
        preg_match_all( '#<plugin:([a-z]+)>(.*)</plugin:\1>#iUs', $this->tpl, $matches );
        for ( $i = 0, $c = count( $matches[1] ); $i < $c; $i++ )
        {
            if ( isActivePlugin( $matches[1][$i] ) )
            {
                $this->tpl = preg_replace( "#<plugin:" . $matches[1][$i] . ">(.*)</plugin:" . $matches[1][$i] . ">#iUs", '\1', $this->tpl );
            }
        }
        $this->tpl = preg_replace( "#<plugin:([a-z]+)>(.*)</plugin:\\1>#iUs", "", $this->tpl );
        //end remove not installed plugin contents

        $this->parsed_tpl = $this->tpl;
        $this->tpl        = '';
    }

    /* ----------------------------------------------------------------- */

    function showit()
    {
        if ( !empty( $this->tpl ) )
        {
            $this->parse();
        }

        print $this->parsed_tpl;
    }

    /* ----------------------------------------------------------------- */

    function dontshowit()
    {
        if ( !empty( $this->tpl ) )
        {
            $this->parse();
        }
        return $this->parsed_tpl;
    }

    /* ----------------------------------------------------------------- */

    function reset()
    {
        $this->tpl           = '';
        $this->parsed_tpl    = '';
        $this->tags          = array();
        $this->required_tags = array();
        $this->blocks        = array();
    }

    /* ----------------------------------------------------------------- */

    function _parse_control_structures( $tpl, $vars, $blocks = array() )
    {
        if ( $matchnumber = preg_match_all( '/<!-- IF (!?)((BLOCK )?)([_a-zA-Z0-9\-]+) -->(.*)((<!-- ELSEIF !\(\\1\\2\\4\) -->)(.*))?<!-- ENDIF \\1\\2\\4 -->/msU', $tpl, $matches ) )
        {
            for ( $i = 0; $i < $matchnumber; $i++ )
            {
                //print( $matches[8 ][$i] . '<br />');
                if ( !empty( $matches[2][$i] ) )
                {
                    $code = 'if( ' . $matches[1][$i] . 'isset($blocks[\'' . $matches[4][$i] . '\']) )' . "\n";
                }
                else
                {
                    $code = 'if( ' . $matches[1][$i] . '( isset($vars[\'' . $matches[4][$i] . '\']) ) )' . "\n";
                }
                $code .= '{ $tpl = str_replace( $matches[0][$i], $this->_parse_control_structures( $matches[5][$i], $vars, $blocks ), $tpl ); }' . "\n";
                $code .= ' else ' . "\n";
                $code .= '{ $tpl = str_replace( $matches[0][$i], !empty($matches[7][$i]) ? $this->_parse_control_structures( $matches[8][$i], $vars, $blocks ) : \'\', $tpl ); }';
                eval( $code );
            }
        }
        return $tpl;
    }

}
