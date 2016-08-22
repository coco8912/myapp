<?php
/*---------------------------------------------------------------------------------
 |冒泡排序
 ----------------------------------------------------------------------------------
 | 思路分析：在要排序的一组数中，对当前还未排好的序列，从前往后对相邻的两个数依次进行
   比较和调整，让较大的数往下沉，较小的往上冒。即，每当两相邻的数比较后发现它们的排序
   与排序要求相反时，就将它们互换。
   小的在上，大的在下
 -----------------------------------------------------------------------------------
 */
$array = array(1,43,54,62,21,66,32,78,36,76,39);
bubbleSort($array);
//冒泡算法
function bubbleSort($arr){
	$len = count($arr);
	for($i=1;$i<$len;$i++){
		for($j=0;$j<$len-1;$j++){
			if($arr[$j]>$arr[$j+1]){
				$tmp = $arr[$j+1];
				$arr[$j+1] = $arr[$j];
				$arr[$j] = $tmp;
			}
		}
	}
}
/**
 * 选择排序
 * 思路分析：在要排序的一组数中，选出最小的一个数与第一个位置的数交换。然后在剩下的数当中再找最小的与第二个位置的数交换，如此循环到倒数第二个数和最后一个数比较为止。
 * $array = array(1,43,54,62,21,66,32,78,36,76,39);
 */
function selectsort($arr){
	$len = count($arr);
	for($i=0;$i<$len-1;$i++){
		$p = $i;
		for($j=$i+1;$j<$len;$j++){
			if($arr[$p]>$arr[$j]){
				$p = $j;
			}
		}
		if($p!= $i){
			$tmp = $arr[$i];
			$arr[$i] = $arr[$p];
			$arr[$p] = $tmp; 
		}
	}
}
/**
 *插入排序
 *思路分析：在要排序的一组数中，假设前面的数已经是排好顺序的，现在要把第n个数插到前面的有序数中，使得这n个数也是排好顺序的。如此反复循环，直到全部排好顺序。
 * $array = array(1,43,54,62,21,66,32,78,36,76,39);
 */
insertsort($array);
function insertsort($arr){
	$len = count($arr);
	for($i=1;$i<$len;$i++){
		$tmp = $arr[$i]; // 4
		for($j=$i-1;$j>=0;$j--){
			if($tmp < $arr[$j]){ 
				$arr[$j+1] = $arr[$j];
				$arr[$j] = $tmp;
			}else{
				break;
			}
		}
	}
}
/**
 * 快速排序
 * 思路分析：选择一个基准元素，通常选择第一个元素或者最后一个元素。通过一趟扫描，将待排序列分成两部分，一部分比基准元素小，一部分大于等于基准元素。此时基准元素在其排好序后的正确位置，然后再用同样的方法递归地排序划分的两部分。
 *  $array = array(1,43,54,62,21,66,32,78,36,76,39);
 */
 $res = quickSort($array);
 print_r($res);
function quickSort($array)
{
    if(!isset($array[1]))
        return $array;
    $mid = $array[0]; //获取一个用于分割的关键字，一般是首个元素
    $leftArray = array(); 
    $rightArray = array();

    foreach($array as $v)
    {
        if($v > $mid)
            $rightArray[] = $v;  //把比$mid大的数放到一个数组里
        if($v < $mid)
            $leftArray[] = $v;   //把比$mid小的数放到另一个数组里
    }

    $leftArray = quickSort($leftArray); //把比较小的数组再一次进行分割
    $leftArray[] = $mid;        //把分割的元素加到小的数组后面，不能忘了它哦

    $rightArray = quickSort($rightArray);  //把比较大的数组再一次进行分割
    return array_merge($leftArray,$rightArray);  //组合两个结果
}
?>