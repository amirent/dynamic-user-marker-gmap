<?php
//The form is submitted to this processor. The marker is added to the database and redirects to the output map

$host = getenv('OPENSHIFT_MYSQL_DB_HOST');
$port = getenv('OPENSHIFT_MYSQL_DB_PORT');
$username = getenv('OPENSHIFT_MYSQL_DB_USERNAME');
$password = getenv('OPENSHIFT_MYSQL_DB_PASSWORD');
$database = getenv('OPENSHIFT_GEAR_NAME');

if(file_exists('phpsqlajax_dbinfo.php')) {
    require("phpsqlajax_dbinfo.php");
}

$link = mysqli_connect($host.':'.$port, $username, $password, $database);

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

if (array_key_exists('check_submit', $_POST)) {

/*start sanitizing*/

//NAME
$name = (isset($_POST['name'])?$_POST['name']:'');
$name = mysqli_real_escape_string($link, $name);

//ADDRESS
$address = '';
if($_POST['address']==''){
        return_to_course_map();
}else{
        $address = $_POST['address'];
}
$address = mysqli_real_escape_string($link, $address);

//GEO LOCATION
$location = $_POST['location'];
$lat = '';
$long = '';

if($_POST['location']==''){
        return_to_course_map();
}else{
        sscanf($location, '(%f, %f)', $lat, $long);

        if(!is_float($lat)||!is_float($long)){
                return_to_course_map();
        }
}
$lat = mysqli_real_escape_string($link, $lat);
$long = mysqli_real_escape_string($link, $long);


//INFORMATION
$info = (isset($_POST['description'])?$_POST['description']:'');
$info = mysqli_real_escape_string($link, $info);


//Now Store variables into the database
if (mysqli_query($link, "INSERT into markers (name, address, lat, lng, description) VALUES ('$name','$address', '$lat', '$long', '$info' )")) {
    //printf("%d Row inserted.\n", mysqli_affected_rows($link));
    return_to_course_map();
}

} else {
    echo "Nice Try!";
}

function return_to_course_map(){
        mysqli_close($link);
        //Redirect to the EdX page with the output map
        header("Location: https://edge.edx.org/courses/ubc/artssandbox005/1/27eb53a0ed0f48ed8dc1df60b0ad5de0/");
}
