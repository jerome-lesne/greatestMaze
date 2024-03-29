<?php

session_start();

function randArrGen($min, $max)
{
    // Generate the size of the map
    $arrX = rand($min, $max);
    $arrY = rand($min, $max);
    // Fill the map
    $arr = array_fill(0, $arrY, array_fill(0, $arrX, "W"));
    // Add mouse
    $arr[rand(0, $arrY - 1)][rand(0, $arrX - 1)] = "M";
    // Add cat check that the rand position is not Mouse
    $x = rand(0, $arrX - 1);
    $y = rand(0, $arrY - 1);

    while ($arr[$y][$x] === "M") {
        $x = rand(0, $arrX - 1);
        $y = rand(0, $arrY - 1);
    }
    $xCat = $x;
    $yCat = $y;
    $arr[$yCat][$xCat] = "E";

    while (true) {
        $randMove = rand(1, 4);
        if($y != 0 && $randMove == 1) {
            if($arr[$y - 1][$x] == "M") {
                return [$arr, $arrX, $arrY, $xCat, $yCat];
            }
            $arr[$y - 1][$x] =  "E";
            $y--;
        }
        if($x != count($arr[$y]) - 1 && $randMove == 2) {
            if($arr[$y][$x + 1] == "M") {
                return [$arr, $arrX, $arrY, $xCat, $yCat];
            }
            $arr[$y][$x + 1] = "E";
            $x++;
        }
        if($y != count($arr) - 1 && $randMove == 3) {
            if ($arr[$y + 1][$x] == "M") {
                return [$arr, $arrX, $arrY, $xCat, $yCat];
            }
            $arr[$y + 1][$x] = "E";
            $y++;
        }
        if($x != 0 && $randMove == 4) {
            if ($arr[$y][$x - 1] == "M") {
                return [$arr, $arrX, $arrY, $xCat, $yCat];
            }
            $arr[$y][$x - 1] = "E";
            $x--;
        }
    }
}

function showCell($cell)
{
    switch ($cell) {
        case 'W':
            echo "<img src='./assets/images/wall.png' width='96'>";
            break;
        case 'F':
            echo "<img src='./assets/images/fog.png' width='96'>";
            break;
        case 'C':
            echo "<img src='./assets/images/cat.png' width='96'>";
            break;
        case 'M':
            echo "<img src='./assets/images/mouse.png' width='96'>";
            break;
        case 'E':
            echo "";
            break;
    }
}

function enlight($array, $arrayDisp, $catPos)
{
    $x = $catPos[0];
    $y = $catPos[1];
    $arrayDisp[$y][$x] = "C";
    if($y != 0) {
        $arrayDisp[$y - 1][$x] = $array[$y - 1][$x];
    }
    if($x != count($array[$y]) - 1) {
        $arrayDisp[$y][$x + 1] = $array[$y][$x + 1];
    }
    if($y != count($array) - 1) {
        $arrayDisp[$y + 1][$x] = $array[$y + 1][$x];
    }
    if($x != 0) {
        $arrayDisp[$y][$x - 1] = $array[$y][$x - 1];
    }
    return $arrayDisp;
}

function drawMaze($array)
{
    echo "<div id='mazeContainer'>";
    foreach ($array as $y => $row) {
        echo "<div class='row'>";
        foreach($row as $x => $cell) {
            echo "<div class='cell' width='96' height='96'>";
            showCell($cell);
            echo "</div>";
        }
        echo "</div>";
    }
    echo "</div>";
}

function move($arr, $arrDisp, $catPos)
{
    $x = $catPos[0];
    $y = $catPos[1];
    if(isset($_POST["up"])) {
        if($y != 0 && $arr[$y - 1][$x] != "W") {
            $arrDisp[$y - 1][$x] = "C";
            $_SESSION["catPos"][1]--;
            return $arrDisp;
        }
    }
    if(isset($_POST["right"])) {
        if($x != count($arr[$y]) - 1 && $arr[$y][$x + 1] != "W") {
            $arrDisp[$y][$x + 1] = "C";
            $_SESSION["catPos"][0]++;
            return $arrDisp;
        }
    };
    if(isset($_POST["down"])) {
        if($y != count($arr) - 1 && $arr[$y + 1][$x] != "W") {
            $arrDisp[$y + 1][$x] = "C";
            $_SESSION["catPos"][1]++;
            return $arrDisp;
        }
    };
    if(isset($_POST["left"])) {
        if($x != 0 && $arr[$y][$x - 1] != "W") {
            $arrDisp[$y][$x - 1] = "C";
            $_SESSION["catPos"][0]--;
            return $arrDisp;
        }
    }
    return $arrDisp;
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>The Maze</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="./assets/css/style.css" rel="stylesheet" />
</head>


<body>
    <header>
    <h1>THE GREATEST MAZE</h1>
    </header>
<main>

<?php

//Reloading button
if (isset($_POST["reload"])) {
    session_destroy();
    header("refresh:0");
}
//Chose a random maze
if (!isset($_SESSION["maze"])) {
    $randGen = randArrGen(4, 6);
    $_SESSION["maze"] = $randGen[0];
    $_SESSION["arrX"] = $randGen[1];
    $_SESSION["arrY"] = $randGen[2];
}

$fogArr = array_fill(0, $_SESSION["arrY"], array_fill(0, $_SESSION["arrX"], "F"));

// Placing cat for first launch
if (!isset($_SESSION["catPos"])) {
    $_SESSION["catPos"] = [];
    $_SESSION["catPos"][0] = $randGen[3];
    $_SESSION["catPos"][1] = $randGen[4];
}

// Checking move input
$fogArr = move($_SESSION["maze"], $fogArr, $_SESSION["catPos"]);
// Removing fog
$fogArr = enlight($_SESSION["maze"], $fogArr, $_SESSION["catPos"]);
// Draw the maze
drawMaze($fogArr);

?>

<form method='POST'>
    <div id='arrowContainer'>
        <div>
            <input id='up' class='arrow' type='submit' name='up' value=''>
        </div>
        <div>
            <input id='left' class='arrow' type='submit' name='left' value=''>
            <input id='right' class='arrow' type='submit' name='right' value=''>
        </div>
        <div>
            <input id='down' class='arrow' type='submit' name='down' value=''>
        </div>
    </div>
</form>
<form method='POST'>
    <div>
        <input id='reload' type='submit' name='reload' value='Reload'>
    </div>
</form>


</main>
</body>
