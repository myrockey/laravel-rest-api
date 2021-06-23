<?php

namespace App\Utils;

/**
 * 通用数学计算
 */
class Math
{
    /**
     * 计算同比（即同期相比，表示某个特定统计段今年与去年之间的比较）
     * 
     * 公式：同比 = (今年值 - 去年值) / 去年值
     *
     * @param string $lastYearValue 去年值
     * @param string $thisYearValue 今年值
     * @param integer $scale 保留N位小数
     * @return string
     */
    public static function tongbi(string $lastYearValue, string $thisYearValue, int $scale = 0): string
    {
        if ($lastYearValue == $thisYearValue) {
            return number_format(0, $scale, '.', '');
        }

        if (0 == $lastYearValue) {
            return '-';
        }

        return bcmul(bcdiv(bcsub($thisYearValue, $lastYearValue, $scale), $lastYearValue, $scale + 2), '100', $scale);
    }
}
