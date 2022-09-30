<?php

$username = 'root';
$password = '123456';
$database = 'ponconsoft';
$conn = mysqli_connect('127.0.0.1', $username, $password, $database);
$table = 'jafgegfer';
// 初始化数据表
init_database($conn);
$order = $_GET['order'];
if ($order == 'get_list') {
    echo success(get_list());
} else if ($order == 'update_list') {
    update_list();
    echo success('更新成功');
} else if ($order == 'submit') {
    submit();
    echo success('提交成功');
} else if ($order == 'get_data') {
    echo success(get_data());
}

/**
 * 初始化数据表
 */
function init_database()
{
    global $conn;
    global $table;
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `$table` (
        `name` VARCHAR(10),
        `finish` BOOLEAN
    )");
}

/**
 * 成功响应
 */
function success($data)
{
    header('Content-type: application/json');
    return json_encode([
        'code' => 200,
        'msg' => '成功',
        'data' => $data
    ]);
}

/**
 * 获取名单和完成情况
 */
function get_list()
{
    global $conn;
    global $table;
    $result = mysqli_query($conn, "SELECT * FROM `$table`");
    return mysqli_fetch_all($result);
}

/**
 * 更新名单
 */
function update_list()
{
    global $conn;
    global $table;
    mysqli_query($conn, "TRUNCATE TABLE `$table`");
    $text = file_get_contents('list.txt');
    $list = explode("\r\n", $text);
    foreach ($list as $name) {
        mysqli_query($conn, "INSERT INTO `$table` VALUES ('$name', false)");
    }
}

/**
 * 提交更新
 */
function submit()
{
    global $conn;
    global $table;
    $name = $_GET['name'];
    $finish = $_GET['finish'];
    mysqli_query($conn, "LOCK TABLES `$table` WRITE");
    mysqli_query($conn, "UPDATE `$table` SET `finish` = $finish WHERE `name` = '$name'");
    mysqli_query($conn, "UNLOCK TABLES");
}

/**
 * 获取个人完成情况
 */
function get_data()
{
    global $conn;
    global $table;
    $name = $_GET['name'];
    $result = mysqli_query($conn, "SELECT `finish` FROM `$table` WHERE `name` = '$name'");
    return mysqli_fetch_assoc($result);
}
