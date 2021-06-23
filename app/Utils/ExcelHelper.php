<?php

declare(strict_types=1);

namespace App\Utils;

use think\Exception;

/**
 * Excel 相关常用方法
 */
class ExcelHelper
{
    /**
     * 生成Excel文件
     *
     * @param string $fileName 文件名称
     * @param array $sheet Excel结构，支持多sheet，按如下格式组织数据
     *  [
     *      [
     *          'sheet' => 'sheet name',
     *          'column' => [
     *              'column name' => 'column field',
     *              'column name' => 'column field',
     *          ],
     *      ]
     *  ]
     * @param array $data 文件数据，支持多sheet，按如下格式组织数据
     *  [
     *      [
     *          'sheet name' => [
     *              'column A'',
     *              'column B'',
     *          ],
     *      ]
     *  ]
     * @param string $savePath 文件保存路径(默认/uploads/temp)
     * @return string 
     */
    public static function generate(string $fileName, array $sheet, array $data = [], string $savePath = '/uploads/temp'): string
    {
        if (empty($fileName) || empty($sheet)) {
            throw new Exception("参数错误");
        }

        $excel = new \Vtiful\Kernel\Excel([
            'path' => public_path($savePath)
        ]);

        $firstSheet = array_shift($sheet);
        $obj = $excel->fileName($fileName . '.xls', $firstSheet['sheet'])->header(array_keys($firstSheet['column']));
        if (!empty($data[$firstSheet['sheet']])) {
            $obj->data($data[$firstSheet['sheet']]);
        }

        if (!empty($sheet)) {
            foreach ($sheet as $item) {
                $obj = null;
                $obj = $excel->addSheet($item['sheet'])->header(array_keys($item['column']));
                if (!empty($data[$item['sheet']])) {
                    $obj->data($data[$item['sheet']]);
                }
            }
        }

        return $excel->output();
    }

    /**
     * 读取Excel文件数据
     *
     * @param string $path 文件所在路径
     * @param string $fileName 文件名称
     * @param array $sheet Excel结构，支持多sheet，按如下格式组织数据
     *  [
     *      [
     *          'sheet' => 'sheet name',
     *          'column' => [
     *              'column name' => 'column field',
     *              'column name' => 'column field',
     *          ],
     *      ]
     *  ]
     * @return array
     */
    public static function read(string $path, string $fileName, array $sheet): array
    {
        if (empty($path) || empty($fileName) || empty($sheet)) {
            throw new Exception("参数错误");
        }
        
        $excel = new \Vtiful\Kernel\Excel([
            'path' => $path
        ]);
        $fileObj = $excel->openFile($fileName);
        
        $data = [];
        
        foreach ($sheet as $item) {
            $sheetData = $fileObj->openSheet($item['sheet'])->getSheetData();
            array_shift($sheetData);

            $columnField = array_values($item['column']);
            $tempSheet = [];
            foreach ($sheetData as $v) {
                $tempSheet[] = array_combine($columnField, $v);
            }

            $data[$item['sheet']] = $tempSheet;
        }

        return $data;
    }

    /**
     * 将Excel时间转换为时间戳
     * 
     * EXCEL  中读取到的时间原型是 浮点型，现在要转成 格式化的标准时间格式
     * 返回的时间是 UTC 时间（世界协调时间，加上8小时就是北京时间）
     * @param float|int $dateValue Excel浮点型数值
     * @param int $calendar_type 设备类型 默认Windows 1900.Windows  1904.MAC
     * @return int 时间戳
     */
    public static function toTimestamp($dateValue = 0, $calendar_type = 1900)
    {
        // Excel中的日期存储的是数值类型，计算的是从1900年1月1日到现在的数值
        if (1900 == $calendar_type) { // WINDOWS中EXCEL 日期是从1900年1月1日的基本日期
            $myBaseDate = 25569; // php是从 1970-01-01 25569是到1900-01-01所相差的天数
            if ($dateValue < 60) {
                --$myBaseDate;
            }
        } else { // MAC中EXCEL日期是从1904年1月1日的基本日期(25569-24107 = 4*365 + 2) 其中2天是润年的时间差？
            $myBaseDate = 24107;
        }

        // 执行转换
        if ($dateValue >= 1) {
            $utcDays = $dateValue - $myBaseDate;
            $returnValue = round($utcDays * 86400);
            if (($returnValue <= PHP_INT_MAX) && ($returnValue >= -PHP_INT_MAX)) {
                $returnValue = (int)$returnValue;
            }
        } else {
            // 函数对浮点数进行四舍五入
            $hours = round($dateValue * 24);
            $mins = round($dateValue * 1440) - round($hours * 60);
            $secs = round($dateValue * 86400) - round($hours * 3600) - round($mins * 60);
            $returnValue = (int)gmmktime($hours, $mins, $secs);
        }

        return $returnValue - (3600 * 8); // 返回时间戳
    }

    /**
     * 将Excel时间转换为指定格式日期字符串
     *
     * @param float|int $date Excel浮点型数值
     * @param string $format 日期格式
     * @return string
     */
    public static function toDateTime($date, string $format = 'Y-m-d H:i:s'): string
    {
        return date($format, self::toTimestamp($date));
    }
}