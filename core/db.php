<?php

class dbclass
{

    var $Query;
    var $Connection;
    var $debug = true;

    function mysql( $server, $username, $password, $database )
    {
        @$this->Connection = mysql_connect( $server, $username, $password ) or die( 'error connecting :' . mysql_error() );
        mysql_select_db( $database, $this->Connection ) or die( 'wrong database: ' . mysql_error( $this->Connection ) );
    }

    function close()
    {
        mysql_close( $this->Connection ) or die( 'Error on close : ' . mysql_error( $this->Connection ) );
    }

    function Query( $sql )
    {
        $Q = mysql_query( $sql, $this->Connection );
        if ( !$Q )
            return $this->error( $sql );
        return $Q;
    }

    function error( $sql )
    {
        if ( $this->debug )
        {
            die( 'error:' . mysql_error( $this->Connection ) . '<br /> ' . $sql );
        }
        else
        {
            return mysql_error( $this->Connection );
        }
    }

    function iquery( $table, $data )
    {
        $field = '';
        $value = '';
        foreach ( $data as $filds => $val )
        {
            $field .= "`$filds`, ";
            $value .= "'" . mysql_real_escape_string( $val ) . "', ";
        }
        $insert = $this->Query( "INSERT INTO `" . $table . "`" . "(" . rtrim( $field, ', ' ) . ") VALUES (" . rtrim( $value, ', ' ) . ");" ) or die( mysql_error() );
        return $insert;
    }

    function uquery( $table, $data, $id = '' )
    {
        $foreach = '';
        foreach ( $data as $filds => $value )
        {
            $foreach .= "`$filds`='" . mysql_real_escape_string( $value ) . "' , ";
        }
        $id = ($id == '') ? '' : ' WHERE ' . $id;
        $update = $this->Query( "UPDATE `" . $table . "` SET " . rtrim( $foreach, ', ' ) . $id . ' ;' ) or die( mysql_error() );
        return $update;
    }

    function fetch( $query )
    {
        $re = mysql_fetch_assoc( $query );
        return $re;
    }

    function GetRowValue( $field, $Q, $raw = false )
    {
        $Q = ($raw) ? $this->Query( $Q ) : $Q;
        $row = mysql_fetch_assoc( $Q );
        $row = $row[$field];
        return $row;
    }

    function getrows( $query, $raw = false )
    {
        $query = ($raw) ? $this->Query( $query ) : $query;
        $rows = mysql_num_rows( $query );
        $rows = empty( $rows ) ? 0 : $rows;
        return $rows;
    }

    function getmax( $field, $table )
    {
        $id = $this->Query( "SELECT MAX(`$field`) as id FROM `$table`" );
        $id = $this->fetch( $id );
        $id = $id['id'];
        return $id;
    }

    function affected()
    {
        $r = mysql_affected_rows( $this->Connection );
        return $r;
    }

    function last()
    {
        return mysql_insert_id();
    }

}