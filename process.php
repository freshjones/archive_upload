<?php
$data = array('bob'=>$_POST);
header('Content-type: application/json; charset=utf-8');
echo json_encode($data);