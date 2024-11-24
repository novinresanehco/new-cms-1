<?php

class NSMenuTree
{

    /**
     * variable to store temporary data to be processed later
     *
     * @var array
     */
    var $data;

    /**
     * Add an item
     *
     * @param int $id 			ID of the item
     * @param int $parent 		parent ID of the item
     * @param string $li_attr 	attributes for <li>
     * @param string $label		text inside <li></li>
     */
    function add_row( $id, $parent, $li_attr, $label )
    {
        $this->data[$parent][] = array( 'id' => $id, 'li_attr' => $li_attr, 'label' => $label );
    }

    /**
     * Generates nested lists
     *
     * @param string $ul_attr
     * @return string
     */
    function generate_list( $ul_attr = '', $style = '', $StyleConfig = array() )
    {
        $return = $this->ul( 0, $ul_attr, $style, $StyleConfig );
        if ( $return instanceof samaneh )
        {
            $return = $return->dontshowit();
        }
        return $return;
    }

    /**
     * Recursive method for generating nested lists
     *
     * @param int $parent
     * @param string $attr
     * @return string
     */
    function ul( $parent = 0, $attr = '', $style = '', $StyleConfig = '' )
    {
        static $i      = 1;
        $indent        = str_repeat( "\t\t", $i );
        static $jarray = array();
        global $config;

        if ( !isset( $jarray[$parent] ) )
        {
            $jarray[$parent] = 1;
        }


        if ( isset( $this->data[$parent] ) )
        {
            if ( $attr )
            {
                $attr = ' ' . $attr;
            }

            $html = '';
            if ( $parent == 0 )
            {
                if ( !empty( $StyleConfig['js'] ) && is_array( $StyleConfig['js'] ) )
                {
                    foreach ( $StyleConfig['js'] as $file )
                    {
                        $html .= '<script src="' . $config['site'] . 'plugins/menumaker/js/' . $file . '"></script>';
                    }
                }
                if ( !empty( $StyleConfig['css'] ) && is_array( $StyleConfig['css'] ) )
                {
                    foreach ( $StyleConfig['css'] as $file )
                    {
                        $html .= '<link rel="stylesheet" href="' . $config['site'] . 'plugins/menumaker/css/' . $file . '">';
                    }
                }
            }
            $html .= "\n$indent";

            if ( $parent == 0 && isset( $StyleConfig['div'] ) && is_array( $StyleConfig['div'] ) )
            {
                $html .= "\n<div";
                if ( !empty( $StyleConfig['div'][0] ) )
                {
                    $html .= ' id="' . htmlspecialchars( $StyleConfig['div'][0] ) . '"';
                }
                if ( !empty( $StyleConfig['div'][1] ) )
                {
                    $html .= ' class="' . htmlspecialchars( $StyleConfig['div'][1] ) . '"';
                }
                $html .= '>';
            }


            $html .= "<ul$attr>";
            $i++;
            foreach ( $this->data[$parent] as $row )
            {
                $child = $this->ul( $row['id'], '', $style, $StyleConfig );
                $html .= "\n\t$indent";
                if ( $parent == 0 && !empty( $StyleConfig['li'][0] ) )
                {
                    $row['li_attr'] .= ' id="' . $StyleConfig['li'][0] . '"';
                }
                if ( $parent == 0 && !empty( $StyleConfig['li'][1] ) )
                {
                    $row['li_attr'] .= ' class="' . $StyleConfig['li'][1] . '"';
                }
                $html .= '<li' . $row['li_attr'] . '>';
                $html .= $row['label'];
                if ( $child )
                {
                    $i--;
                    $html .= $child;
                    $html .= "\n\t$indent";
                }
                $html .= '</li>';
            }
            $html .= "\n$indent</ul>";

            if ( $parent == 0 && isset( $StyleConfig['div'] ) && is_array( $StyleConfig['div'] ) )
            {
                $html .= '</div>';
            }
            return $html;
        }
        return false;
    }

    /**
     * Clear the temporary data
     *
     */
    function clear()
    {
        $this->data = array();
    }

}
