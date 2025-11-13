<?php
session_start();

require_once("./sf_api/sf_api_func.php");

sf_regist_recruit($_POST);

header('Location: /form/recruit_done');
?>
