<?php
/**
 * Phprojekt CSS Compiler
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 2.1 as published by the Free Software Foundation
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * @copyright  Copyright (c) 2008 Mayflower GmbH (http://www.mayflower.de)
 * @version    $Id
 * @license    LGPL 2.1 (See LICENSE file)
 * @author     Mariano La Penna <mariano.lapenna@mayflower.de>
 * @since      File available since Release 6.0
 */


header("Content-type: text/css");

$allCss = "";

//For every item in the current directory
foreach (scandir(".") as $item) {

    if (is_dir($item)) {
        //Is it a subdirectory
        
        if ($item != '.svn' && $item != '.' && $item != '..') {
            //It is a CSS folder
            
            foreach (scandir($item) as $subItem) { //Iterate on every file
                $subItemPath = $item . DIRECTORY_SEPARATOR . $subItem;
                if (!is_dir($subItemPath) && substr($subItem, -4) == '.css') {
                    $allCss .= file_get_contents($subItemPath);
                }
            }
        }
    } else {
        //It is a file
        if (substr($item, -4) == '.css') {
            $allCss .= file_get_contents($item);
        }
    }
}


$allCss = str_replace("../images", "images", $allCss);

$allCss .= ":focus { -moz-outline-style: none; }";

echo $allCss;