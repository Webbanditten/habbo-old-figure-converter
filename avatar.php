<?php
function getOldColorFromFigureList($iPart, $iSprite, $iColorIndex) {
    $iColorIndex = (int) ltrim($iColorIndex, "0");
    $string = file_get_contents("./resource/oldfiguredata.json");
    if ($string === false) {
        echo "error";
    }

    $json_a = json_decode($string, true);
    if ($json_a === null) {
        echo "error";
    }
    foreach($json_a["colors"] as $genderIndex =>  $gender) {
        foreach($gender as $parts) {
            foreach($parts as $part => $partDefinition) {
                //echo "input Part: ".$iPart."<br>";
                //echo "Part: ".$part."<br>";
                if($part == $iPart) {
                    foreach($partDefinition as $spriteIndex => $spriteDefinition) {

                        foreach($spriteDefinition as $sprite) {
                            $spriteId = $sprite["s"];
                            $spriteColors = $sprite["c"];

                            if($spriteId == $iSprite) {
                                return $spriteColors[$iColorIndex-1];
                            }
                        }
                    }
                }

            }
        }
    }
}

function convertOldColorToNew($iPart, $iSprite, $iColorIndex) {
    $iColorIndex = (int) ltrim($iColorIndex, "0");
    $string = file_get_contents("./resource/figuredata.json");
    if ($string === false) {
        echo "error";
    }

    $json_a = json_decode($string, true);
    if ($json_a === null) {
        echo "error";
    }
    $color = getOldColorFromFigureList($iPart, $iSprite, $iColorIndex);

    foreach ($json_a["palette"] as $paletteIndex => $paletteValue) {
        foreach ($paletteValue as $colorIndex => $colorValue) {
            if($color == $colorValue["color"]) {

                return $colorIndex;
            }
        }
    }
}

function takeCareOfHats($spriteId, $colorId) {
    switch ($spriteId) {
        // REggae
        case 120:
            return '.ha-1001-0';
        // Cap
        case 525:
        case 140:
            return '.ha-1002-' . $colorId;
        // Comfy beanie
        case 150:
        case 535:
            return '.ha-1003-' . $colorId;
        //Fishing hat
        case 160:
        case 565:
            return '.ha-1004-' . $colorId;
        // Bandana
        case 570:
            return '.ha-1005-' . $colorId;
        // Xmas beanie
        case 585:
        case 175:
            return '.ha-1006-0';
        // Xmas rodolph
        case 580:
        case 176:
            return '.ha-1007-0';
        // Bunny
        case 590:
        case 177:
            return '.ha-1008-0';
        // Hard Hat
        case 595:
        case 178;
            return '.ha-1009-1321';
        // Boring beanie
        case 595:
            return '.ha-1010-' . $colorId;
        // HC Beard hat
        case 801:
            return '.hr-829-' . $colorId.'.fa-1201-62.ha-1011-' . $colorId;

        // HC Beanie
        case 800:
        case 810:
            return '.ha-1012-' . $colorId;
        // HC Cowboy Hat
        case 802:
        case 811:
            return '.ha-1013-' . $colorId;
        default:
            return '.ha-0-' . $colorId;
    }
}

function convertFig($figure) {
    $start = 0;
    $parts = array();
    $increase_start = array(0, 5, 10, 15, 20);

    for($x = 0; $x < 10; $x++) {
        $length = (in_array($start, $increase_start)) ? 3 : 2;
        $parts[$x] = substr($figure, $start, $length);
        $start = $start + $length;
    }

    $buildFigure = 'hr-'.$parts[0].'-'.convertOldColorToNew("hr", $parts[0], $parts[1]);
    $buildFigure .= '.hd-'.$parts[2].'-'.convertOldColorToNew("hd", $parts[2], $parts[3]);
    $buildFigure .= '.ch-'.$parts[8].'-'.convertOldColorToNew("ch", $parts[8], $parts[9]);
    $buildFigure .= '.lg-'.$parts[4].'-'.convertOldColorToNew("lg", $parts[4], $parts[5]);
    $buildFigure .= '.sh-'.$parts[6].'-'.convertOldColorToNew("sh", $parts[6], $parts[7]);
    $buildFigure .= takeCareOfHats($parts[0], convertOldColorToNew("hr", $parts[0], $parts[1]));
    return $buildFigure;
}

header("Content-type: image/png");

$fig = convertfig($_GET["figure"]);

$real_figure = http_build_query(array_merge($_GET, ["figure" => $fig]));
echo file_get_contents('http://www.habbo.com/habbo-imaging/avatarimage?'.$real_figure);



