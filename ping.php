<?php 
header("Content-type: text/html; charset=gbk");
class ping{
	private $title;    //博客名称
	private $hosturl;  //博客首页地址	
	private $arturl;   //新发文章地址
	private $rssurl;   //博客rss地址
	private $baiduXML; //百度XML结构
	private $baiduRPC; //百度XML地址
	
	public function __construct($title,$arturl,$hosturl,$rssurl)
	{
		if(empty($title) || empty($arturl))
			return false;
		$this->title=$title;
		$this->hosturl=$hosturl;
		$this->rssurl=$rssurl;
		$this->arturl=$arturl;
		$this->baiduRPC='http://ping.baidu.com/ping/RPC2';
		
		$this->baiduXML = '<?xml version=\"1.0\" encoding=\"gb2312\"?>';
		$this->baiduXML .='<methodCall>';
		$this->baiduXML .='	<methodName>weblogUpdates.extendedPing</methodName>';
		$this->baiduXML .='		<params>';
		$this->baiduXML .='		<param><value><string>'.$this->hosturl.'</string></value></param>';
		$this->baiduXML .='		<param><value><string>'.$this->title.'</string></value></param>';
		$this->baiduXML .='		<param><value><string>'.$this->arturl.'</string></value></param>';
		$this->baiduXML .='		<param><value><string>'.$this->rssurl.'</string></value></param>';
		$this->baiduXML .='	</params>';
		$this->baiduXML .='</methodCall>'; 
	}
	public function pingbaidu() 
	{ 
		$ch = curl_init();
		$headers=array(
			'User-Agent: request',
			'Host: ping.baidu.com',
			'Content-Type: text/xml',
		);
		curl_setopt($ch, CURLOPT_URL, $this->baiduRPC); 
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch, CURLOPT_POST, 1); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->baiduXML); 
		$res = curl_exec ($ch); 
		curl_close ($ch); 
		//return $res;
		return (strpos($res,"<int>0</int>"))?true:false;	
	} 
}


	function get_domain()
	{
		/* 协议 */
		$protocol = 'http://';
		/* 域名或IP地址 */
		if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
			$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
		} elseif (isset($_SERVER['HTTP_HOST'])) {
			$host = $_SERVER['HTTP_HOST'];
		} else {
			/* 端口 */
			if (isset($_SERVER['SERVER_PORT'])) {
				$port = ':' . $_SERVER['SERVER_PORT'];
				if ((':80' == $port & 'http://' == $protocol) || (':443' == $port & 'https://' == $protocol)) {
					$port = '';
				}
			} else {
				$port = '';
			}

			if (isset($_SERVER['SERVER_NAME'])) {
				$host = $_SERVER['SERVER_NAME'] . $port;
			} elseif (isset($_SERVER['SERVER_ADDR'])) {
				$host = $_SERVER['SERVER_ADDR'] . $port;
			}
		}

		return $protocol.$host;
	}
	function get_title($id)
	{
		$arc_title='';
		$name = '数据库名';
        $conn = mysql_connect('数据库连接','数据库帐号','数据库密码');

		if (!$conn) {
			die('数据库连接失败'.mysql_error());
		}

		mysql_select_db($name,$conn);
		mysql_query("set character set 'gbk'");//读库
		mysql_query("set names 'gbk'");//写库
		$sql='SELECT `title`,`typeid`,`id` FROM `dede_archives` WHERE  `id` ='.$id.' LIMIT 0,1';
		$query=mysql_query($sql,$conn);		
		$row=array();

		while($row=mysql_fetch_array($query)){
			//echo $row['id'];
			$arc_title=$row['title'];				
		}			
		mysql_close($conn);
		return $arc_title;
	}
	function get_arcurl($id)
	{				
		$arc_url='';
		$hostdomain=get_domain();
		$name = '数据库名';
        $conn = mysql_connect('数据库连接','数据库帐号','数据库密码');

		if (!$conn) {
			die('数据库连接失败'.mysql_error());
		}
		mysql_select_db($name,$conn);
		mysql_query("set character set 'gbk'");//读库
		mysql_query("set names 'gbk'");//写库
		$sql='SELECT `title`,`typeid`,`id` FROM `dede_archives` WHERE  `id` ='.$id.' LIMIT 0,1';
		$query=mysql_query($sql,$conn);		
		$row=array();
		while($row=mysql_fetch_array($query))
		{
			$sql2='SELECT `typedir`,`namerule` FROM  `dede_arctype` WHERE  `id` = '.$row['typeid'].' LIMIT 0 , 1';
			$query=mysql_query($sql2,$conn);
			while($row2=mysql_fetch_array($query)){
				$typedir=str_replace('{cmspath}',$hostdomain,$row2['typedir']);
				$arc_url=str_replace('{typedir}',$typedir,$row2['namerule']);
				date_default_timezone_set('Asia/Shanghai');
				$y=date('Y');
				$m =date('m');
				$d=date('d');				
				$arc_url=str_replace('{Y}',$y,$arc_url);
				$arc_url=str_replace('{M}',$m,$arc_url);
				$arc_url=str_replace('{D}',$d,$arc_url);
				$arc_url=str_replace('{aid}',$id,$arc_url);				
			}			
		}
		mysql_close($conn);
		return $arc_url;
	}

	if(!isset($_GET['id']) || empty($_GET['id']))
		$_GET['id']=1;
	$id=intval($_GET['id']);
	//echo get_title($id);                               获取标题
	//echo get_arcurl($id);                              获取文章连接
	//get_domain()                                       获取协议+标准域名，eg:http://www.baidu.com 
	//__construct($title,$arturl,$hosturl,$rssurl)       参数顺序：网站名称，文章页url，首页地址，RSS页面地址
	
	
	$arc = new Ping('你网站的title',get_arcurl($id),get_domain(),'http://www.abc.com/rss.php');

	
	echo $arc->pingbaidu();
?> 
