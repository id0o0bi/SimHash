<?php

/**
 * 用PHP实现的SimHash算法
 * 
 * @author id0o0bi.org@gmail.com
 * @link https://www.cnblogs.com/maybe2030/p/5203186.html [使用SimHash进行海量文本去重]
 * @link https://github.com/yanyiwu/simhash
 * @link https://github.com/leonsim/simhash
 * @link http://php.net/manual/en/function.hash.php  [PHP Hash functions]
 */
class SimHash 
{
    /**
     * 获取内容simhash值
     * @param content string 字符串内容
     * 
     * @return hash int64
     */
    public function getStringHash ($content) 
    {
        $crc64 = hash('crc32', $content) . hash('crc32b', $content);

        return str_pad(base_convert($crc64, 16, 2), 64, '0', STR_PAD_LEFT);
    }

    /**
     * @desc getFingerPrint 获取信息指纹
     * 
     * @param $vectors Array 输入向量
     * 
     * @return Array 64个元素的数组
     */
    public function getSimHash ($vectors) 
    {
        $hashed = array(
            0, 0, 0, 0, 0, 0, 0, 0, 
            0, 0, 0, 0, 0, 0, 0, 0, 
            0, 0, 0, 0, 0, 0, 0, 0, 
            0, 0, 0, 0, 0, 0, 0, 0, 
            0, 0, 0, 0, 0, 0, 0, 0, 
            0, 0, 0, 0, 0, 0, 0, 0, 
            0, 0, 0, 0, 0, 0, 0, 0, 
            0, 0, 0, 0, 0, 0, 0, 0, 
        );

        foreach ($vectors as $k => $v) 
        {
            extract($v);

            $binary = str_split($binary);
            $weight = round($weight);
            
            for ($i = 0;$i < 64;$i++) {
                $hashed[$i] += $binary[$i] ? $weight : - $weight;
            }
        }

        $hashed = array_map(function($_){
            return $_ > 0 ? 1 : 0;
        }, $hashed);

        return implode('', $hashed);
    }

    /**
     * 获取两个值的汉明距离
     * @param hashA string
     * @param hashB string
     * 
     * @return similarity float
     */
    public function hammingDistance ($hashA, $hashB) 
    {
        $dis = 0;
        $hashA = str_split($hashA);
        $hashB = str_split($hashB);
        foreach ($hashA as $k => $v) {
            if ($v != $hashB[$k]) $dis++;
        }

        return $dis;
    }
    
}