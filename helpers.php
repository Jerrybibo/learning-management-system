<?php
function connect($hostname, $username, $password, $database) {
    $sql_conn = mysqli_connect($hostname, $username, $password, $database);
    if (!mysqli_select_db($sql_conn, $database)) {
        printf('Error occurred during MySQL connection!\nDetails: %s\n', mysqli_error($sql_conn));
        exit(1);
    }
    return $sql_conn;
}
function disconnect($conn_obj) {
    mysqli_close($conn_obj);
}
function query($conn_obj, $query) {
    if (!($result = mysqli_query($conn_obj, $query))) {
        printf("Error: %s\n", mysqli_error($conn_obj));
        exit(1);
    }
    return $result;
}
function take_first($arr) {
    return $arr[0];
}
function gen_id() {
    $alphanumeric = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    return substr(str_shuffle($alphanumeric),0, 10);
}