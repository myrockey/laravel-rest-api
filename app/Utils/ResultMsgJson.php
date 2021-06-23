<?php

namespace App\Utils;
use Illuminate\Pagination\Paginator;

/**
 * 模型控制器返回
 */
class ResultMsgJson
{
    //region 相关常量变量
    const STATUS_OK = 0;  //正确返回码
    const STATUS_ERROR = 400;  //错误返回码
    const STATUS_AUTH = 1001;  //auth token 失效返回码


    //endregion

    /**
     * 正确返回
     * @param int $code
     * @param array $data
     * @param string $msg
     * @return array
     */
    public static function successReturn($data = array(), $msg = '操作成功', $code = 0)
    {
        $responseData = array(
            'errorCode' => $code,
            'data' => $data,
            'msg' => $msg,

        );
        return $responseData;
    }

    /**
     * 错误返回
     * @param int $code
     * @param  $data
     * @param string $msg
     * @return array
     */
    public static function errorReturn($msg = '操作失败', $data = '', $code = 400)
    {
        $responseData = array(
            'errorCode' => $code,
            'data' => $data,
            'msg' => $msg,

        );
        return $responseData;
    }

    /**
     * 多个参数以数组返回
     *
     * @param mixed ...$args 参数
     * @return array
     */
    public static function argsToArray(...$args)
    {
        return $args;
    }


    /**
     * 分页返回
     *
     * @param $list
     * @param Paginator $paginator
     * @return array
     */
    public static function paginateReturn($list, Paginator $paginator)
    {

        $page = $paginator->currentPage();

        $page_size = $paginator->listRows();

        $pages = $paginator->lastPage();

        $total = $paginator->total();

        return compact('list', 'page', 'page_size', 'total', 'pages');
    }
}