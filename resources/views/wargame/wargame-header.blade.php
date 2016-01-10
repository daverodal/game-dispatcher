<?php
/**
 *
 * Copyright 2011-2015 David Rodal
 *
 *  This program is free software; you can redistribute it
 *  and/or modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico" type="image/icon">
    <!--    <link href="--><?//=base_url("js/jquery-ui.css");?><!--" rel="stylesheet" type="text/css"/>-->
    <script src="<?=url("js/jquery.js");?>"></script>
    <script src="<?=url("js/jquery-ui.js");?>"></script>
    <!--    <script src="--><?//=base_url("js/jquery.ui.touch-punch.min.js");?><!--"></script>-->
    <script src="<?=url("js/jquery.panzoom/dist/jquery.panzoom.js");?>"></script>
    <script src="<?=url("js/jquery.panzoom/test/libs/jquery.mousewheel.js");?>"></script>
    <script src="<?=url("js/sync.js");?>"></script>
    <?php \Wargame\Battle::getHeader($gameName, $playerData, $arg);?>
</head>
<?php
\Wargame\Battle::getView($gameName,$mapUrl,$player,$arg,$scenarioArray[0],false, $units);
?>