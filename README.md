# dedecms-ping

ping.php中数据库连接和RSS地址需要修改

修改方法：

1. 找到ping.php中的倒数第2行：

$arc = new Ping('你的网站title',get_arcurl($id),get_domain(),'http://www.abc.com/rss.php');

这行的意思是：

$arc = new Ping('网站名称',get_arcurl($id),get_domain(),'网站RSS地址');

修改为自己的就可以了


2. 搜索一下：$conn = mysql_connect

$name = '数据库名';

$conn = mysql_connect('数据库连接','数据库帐号','数据库密码');

数据库连接这里一般为：localhost

将上面4个修改为自己的（注意有2处地方需要修改的）


3. 修改完之后上传覆盖，然后到文档列表，文章列表最后面会多了一个图标。发完文章之后点击那个图标就ping出去了，然后会提示成功或者失败。