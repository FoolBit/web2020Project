<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use app\admin\model\Role;

function stamp2date($timestamp)
{
	return date('Y-m-d H:i:s', $timestamp);
}

function buildResponse($code, $msg, $payload)
{
	$res = [
		'code' => $code,
		'msg'  => $msg
	];
	foreach ($payload as $key => $value) {
		$res[$key] = $value;
	}

	return json($res);
}

function errorResponse($msg, $payload = [])
{
	return buildResponse(1, $msg, $payload);
}

function successResponse($msg = [], $payload = [])
{
	return buildResponse(0, $msg, $payload);
}

function buildMsg($state, $msg)
{
	$result = [
		'state' => $state,
		'msg' => $msg,
	];
	return $result;
}

function msectime()
{
	list($msec, $sec) = explode(' ', microtime());
	$msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
	return $msectime;
}

if (!function_exists('array_column')) {
	function array_column($arr2, $column_key)
	{
		$data = [];
		foreach ($arr2 as $key => $value) {
			$data[] = $value[$column_key];
		}
		return $data;
	}
}
