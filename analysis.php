<h1>YEG restaurants we think you'll like</h1>

<?php
include("db.php");
// validate form completion
$food = [];
$address = $_POST["adress"];
$waitTime = $_POST["time"];

$burgers;
$pizza;
$pasta;
$shakes;
$numRestaurants = 0;
if (trim($address) == '') {
    echo "adress is empty, <a href='survey.php'>click here to redo the survey</a>";
}
if ($waitTime < 20) {
    echo "be more patient change your wait time to at least 20mins, <a href='survey.php'>click here to redo the survey</a>";
}
if (trim($address) == '' || $waitTime < 20) {
    die;
}

if (isset($_POST["burgers"])) {
    global $burgers;
    $burgers = "on";
    array_push($food, "Burgers");
} else {
    global $burgers;
    $burgers = "off";
}

if (isset($_POST["pizza"])) {
    global $pizza;
    $pizza = "on";
    array_push($food, "Pizza");
} else {
    global $pizza;
    $pizza = "off";
}

if (isset($_POST["pasta"])) {
    global $pasta;
    $pasta = "on";
    array_push($food, "Pasta");
} else {
    global $pasta;
    $pasta = "off";
}
if (isset($_POST["shake"])) {
    global $shakes;
    $shakes = "on";
} else {
    global $shakes;
    $shakes = "off";
}

if ($burgers == "off" && $pasta = "off" && $pizza == "off") {
    echo "How are we gonna find you food if youre not hungry for anything we offer? <a href='survey.php'>click here to redo the survey</a>";
    die;
}

// valiadte adress  https://blackandwhiteshades.wordpress.com/2016/05/12/google-maps-api-simple-example-to-validate-address-using-php/

$url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&sensor=false' . '&key=AIzaSyA_jmIt2_1H9sMdzcvcsiK9NTvJv9tqNmg';
$url = preg_replace("/ /", "%20", $url);
$geocode = file_get_contents($url);
$results = json_decode($geocode, true);
// print_r($results);


if (count($results['results']) == 1) {
    if (isset($results['results'][0]['partial_match'])) {
        if ($results['results'][0]['partial_match']) {
            echo "This is a partially right address <a href='survey.php'>click here to redo the survey</a>";
            die;
        }
    } else {
    }
} else {
    echo "Invalid address, <a href='survey.php'>click here to redo the survey</a>";
    die;
}

// acesses db

$sql = "SELECT * FROM restaurants";
$result = mysqli_query($conn, $sql);
$info = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_array($result)) {
        $rowInfo = [$row["name"], $row["adress"], $row["types"], $row["milkshakes"]];
        array_push($info, $rowInfo);
    }
}

//run form data with the db data
if ($shakes == "on") {
    loopShake();
}
if ($shakes == "off") {
    loopNoShake();
}
if ($numRestaurants == 0) {
    echo "We are terribly, we can't find any restaurants you'd be interested in";
}

function loopShake()
{
    global $info;
    global $address;
    global $waitTime;
    global $food;
    global $numRestaurants;
    for ($i = 0; $i < count($info); $i++) {


        $count = 3;
        
        if (findDuration($address, $info[$i][1]) > ($waitTime * 60)) {
            $count--;
        }
        if (array_intersect($food, str_word_count($info[$i][2], 1)) == []) {
            $count--;
        }
        if ($info[$i][3] == "no") {
            $count--;
        }
        if ($count == 3) {
            echo $info[$i][0] . " @ " . $info[$i][1] . floor(findDuration($address, $info[$i][1])/60) . " minite wait.<br>";
            $numRestaurants++;
        }
    }
}

function loopNoShake()
{
    global $info;
    global $address;
    global $waitTime;
    global $food;
    global $numRestaurants;
    for ($i = 0; $i < count($info); $i++) {

        $count = 2;
        if (findDuration($address, $info[$i][1]) > ($waitTime) * 60) {
            $count--;
        }
        if (array_intersect($food, str_word_count($info[$i][2], 1)) == []) {
            $count--;
        }

        if ($count == 2) {
            echo $info[$i][0] . " @ " . $info[$i][1] . " " . floor(findDuration($address, $info[$i][1])/60) . " minite wait.<br>";;
            $numRestaurants++;
        }
    }
}


function findDuration($origin, $destination)
{

    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=" . $origin . "&destinations=" . $destination . "&key=AIzaSyCuX786mBASNbzVl-dAcYWivSxkQM6-6R4";
    $url = preg_replace("/ /", "%20", $url);
    $api = file_get_contents($url);
    $data = json_decode($api);
    
    return $data->rows[0]->elements[0]->duration->value;
}
