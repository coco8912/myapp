<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// | lanfengye <zibin_5257@163.com>
// +----------------------------------------------------------------------

class AjaxPage {
	
	// 分页栏每页显示的页数
	public $rollPage = 5;
	// 页数跳转时要带的参数
	public $parameter ;
	// 分页URL地址
	public $url = '';
	// 默认列表每页显示行数
	public $listRows = 20;
	// 起始行数
	public $firstRow ;
	// 分页总页面数
	protected $totalPages ;
	// 总行数
	protected $totalRows ;
	// 当前页数
	protected $nowPage ;
	// 分页的栏的总页数
	protected $coolPages ;
	// 分页显示定制
	protected $config = array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first% %prePage% %linkPage% %nextPage% %end% %ajax%');
	// 默认分页变量名
	protected $varPage;
	//分页外层div的id
	protected $pagesId;
	//分页内容替换目标ID
	protected $target;
	
	protected $group;
	//是否滚动自动加载 默认false,

	/**
	* 架构函数
	* @access public
	* @param array $totalRows 总的记录数
	* @param array $listRows 每页显示记录数
	* @param array $parameter 分页跳转的参数
	*/
	public function __construct($totalRows,$listRows='',$parameter='',$url='',$target,$pagesId,$group='') {
		$this->scroll = $scroll;
		$this->totalRows = $totalRows;
		$this->parameter = $parameter;
		$this->url = $url;
		$this->target = $target;
		$this->pagesId = $pagesId;
		$this->group = $group;
		$this->varPage = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		if (!empty($listRows)) {
			$this->listRows = intval($listRows);
		}
		$this->totalPages = ceil($this->totalRows/$this->listRows); //总页数
		$this->coolPages = ceil($this->totalPages/$this->rollPage);
		$this->nowPage = !empty($_GET[$this->varPage])?intval($_GET[$this->varPage]):1;
		if($this->nowPage<1){
			$this->nowPage = 1;
		}elseif(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
			$this->nowPage = $this->totalPages;
		}
		$this->firstRow = $this->listRows*($this->nowPage-1);
	}

	public function setConfig($name,$value) {
		if(isset($this->config[$name])) {
			$this->config[$name] = $value;
		}
	}

	/**
	* 分页显示输出
	* @access public
	*/
	public function show() {

		if(0 == $this->totalRows) return '';
		$p = $this->varPage;
		$nowCoolPage = ceil($this->nowPage/$this->rollPage);
		// 分析分页参数
		if($this->url){
			$depr = C('URL_PATHINFO_DEPR');
			$url = $this->url.$depr.'__PAGE__';
		}else{
			if($this->parameter && is_string($this->parameter)) {
				parse_str($this->parameter,$parameter);
			}elseif(empty($this->parameter)){
				unset($_GET[C('VAR_URL_PARAMS')]);
				if(empty($_GET)) {
					$parameter = array();
				}else{
					$parameter = $_GET;
				}
			}
			$parameter[$p] = '__PAGE__';
			$group = $this->group;
			if($group){
				$url = U($group,$parameter);
			}else{
				$url = U('',$parameter);
			}
			
		}
		//上下翻页字符串
		$upRow = $this->nowPage-1;
		$downRow = $this->nowPage+1;
		if ($upRow>0){
			$upPage = "<a class='prev ie6png' href='javascript:loadakax(".'"'.str_replace('__PAGE__',$upRow,$url).'"'.")'>".$this->config['prev']."</a>";
		}else{
			$upPage = '';
		}

		if ($downRow <= $this->totalPages){
			$downPage = "<a class='next ie6png' href='javascript:loadakax(".'"'.str_replace('__PAGE__',$downRow,$url).'"'.")'>".$this->config['next']."</a>";
		}else{
			$downPage = '';
		}
		// << < > >>
		if($nowCoolPage == 1){
			$theFirst = '';
			$prePage = '';
		}else{
			$preRow = $this->nowPage-$this->rollPage;
			$prePage = "<a class='prev ie6png' href='javascript:loadakax(".'"'.str_replace('__PAGE__',$preRow,$url).'"'.")' >上".$this->rollPage."页</a>";
			$theFirst = "<a href='javascript:loadakax(".str_replace('__PAGE__',1,$url).")' >".$this->config['first']."</a>";
		}
		if($nowCoolPage == $this->coolPages){
			$nextPage = '';
			$theEnd = '';
		}else{
			$nextRow = $this->nowPage+$this->rollPage;
			$theEndRow = $this->totalPages;
			$nextPage = "<a href='javascript:loadakax(".'"'.str_replace('__PAGE__',$nextRow,$url).'"'.")' >下".$this->rollPage."页</a>";
			$theEnd = "<a href='javascript:loadakax(".'"'.str_replace('__PAGE__',$theEndRow,$url).'"'.")' >".$this->config['last']."</a>";
		}
		// 1 2 3 4 5
		/*$linkPage = "";
		for($i=1;$i<=$this->rollPage;$i++){
			$page = ($nowCoolPage -1 )* $this->rollPage + $i;
			if($page!=$this->nowPage){
				if($page<=$this->totalPages){
					$linkPage .= "&nbsp;<a href='javascript:loadakax(".'"'.str_replace('__PAGE__',$page,$url).'"'.");'>&nbsp;".$page."&nbsp;</a>";
				}else{
					break;
				}
			}else{
				if($this->totalPages != 1){
					//$linkPage .= "&nbsp;<span class='current'>".$page."</span>";
					$linkPage .= "&nbsp;<a class='on'>".$page."</a>";
				}
			}
		}*/

		// 1 2 3 4 5
        $linkPage = "";
		if($this->totalPages <=3){
		       for($i=1;$i<=$this->totalPages;$i++){
					$page=($nowCoolPage-1)*$this->rollPage+$i;
					if($page!=$this->nowPage){
						if($page<=$this->totalPages){
							$linkPage .= "&nbsp;<a href='javascript:loadakax(".'"'.str_replace('__PAGE__',$page,$url).'"'.");'>&nbsp;".$page."&nbsp;</a>";
							//$linkPage .= "<a href='".$url."&".$p."=$page'>".$page."</a>";
						}else{
							break;
						}
					}else{
						if($this->totalPages != 1){
							$linkPage .= "&nbsp;<a class='on'>".$page."</a>";
						}
					}
               } 	
		}else{
			if($this->nowPage < 3){  //分页形式   1 2 3 4 5...10
			    for($k=1;$k<=max(3,$this->nowPage+1);$k++){
					if($k == $this->nowPage){
						$linkPage .= "&nbsp;<a class='on'>".$k."</a>";
					}else{
					  // $linkPage .= "<a href='".$url."&".$p."=$k' >".$k."</a>";
					   $linkPage .= "&nbsp;<a href='javascript:loadakax(".'"'.str_replace('__PAGE__',$k,$url).'"'.");'>&nbsp;".$k."&nbsp;</a>";
					}
				}
				$linkPage .= '<span>...</span>';
				$linkPage .= "&nbsp;<a href='javascript:loadakax(".'"'.str_replace('__PAGE__',$this->totalPages,$url).'"'.");'>&nbsp;".$this->totalPages."&nbsp;</a>";
			}else if($this->nowPage + 2 >= $this->totalPages){//分页形式   1...  6 7 8 910 
				$linkPage .= "<a href='javascript:loadakax(".'"'.str_replace('__PAGE__','1',$url).'"'.");'>&nbsp;".'1'."&nbsp;</a>";
			    //$linkPage .= "<a href='".$url."&".$p."=1'  >1</a>"; 
				$linkPage .= '<span>...</span>';
				for($k=$this->totalPages - 2;$k<=$this->totalPages;$k++){
					   if($k==$this->nowPage){
						   $linkPage .= "&nbsp;<a class='on'>".$k."</a>";
						}else{
							 $linkPage .= "&nbsp;<a href='javascript:loadakax(".'"'.str_replace('__PAGE__',$k,$url).'"'.");'>&nbsp;".$k."&nbsp;</a>";
						}
				}
			}else{          //标准  1...  45 6 78... 10
				$linkPage .= "<a href='javascript:loadakax(".'"'.str_replace('__PAGE__','1',$url).'"'.");'>&nbsp;".'1'."&nbsp;</a>";
			    //$linkPage .= "<a href='".$url."&".$p."=1'  >1</a>"; 
				$linkPage .= '<span>...</span>';
				for($k = $this->nowPage;$k<=$this->nowPage+2;$k++){
					   if($k==$this->nowPage){
						   $linkPage .= "&nbsp;<a class='on'>".$k."</a>";
						}else{
						    $linkPage .= "&nbsp;<a href='javascript:loadakax(".'"'.str_replace('__PAGE__',$k,$url).'"'.");'>&nbsp;".$k."&nbsp;</a>";
						}
				}
				$linkPage .= '<span>...</span>';
				$linkPage .= "&nbsp;<a href='javascript:loadakax(".'"'.str_replace('__PAGE__',$this->totalPages,$url).'"'.");'>&nbsp;".$this->totalPages."&nbsp;</a>";
			}
		}
		if($this->target){
		//<script>jquery分页</script>
	$ajax = <<<eco
	 <script>
	 
	 	function loadakax(url){
			$.ajax({
				url: url,
				dataType: "html",
				type: "POST",
				cache: false,
				async:true,
				beforeSend:function(){
					icage.beforeLoading();
				},
				success: function(html){
					$("#popup_loading").remove();
					$("#{$this->target}").html(html);
					return false;
				}
			});
		}
	 </script>
eco;
			}
		$pageStr = str_replace(
			array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%','%ajax%'),
			array($this->config['header'],$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd,$ajax),$this->config['theme']);
		return $pageStr;
	}
}	