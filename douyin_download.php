<?php
/*
  CURL 是一个利用URL语法规定来传输文件和数据的工具，支持很多协议，如HTTP、FTP、TELNET等。
  PHP也支持 cURL 库。本文将介绍 cURL 的一些高级特性，以及在PHP中如何运用它。
  封装php curl 直接可以使用
  */
function curl_post($url, $postfields = '', $headers = '', $timeout = 20, $file = 0)
{
    $ch = curl_init();//初始化一个的curl对话，返回一个链接资源句柄
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_HEADER => false,
        CURLOPT_NOBODY => false,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
    );
    if (is_array($postfields) && $file == 0) {
        $options[CURLOPT_POSTFIELDS] = http_build_query($postfields);
    } else {
        $options[CURLOPT_POSTFIELDS] = $postfields;
    }
    curl_setopt_array($ch, $options);//
    if (is_array($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    $result = curl_exec($ch);//执行一个的curl对话
    $code = curl_errno($ch);//返回一个的包含当前对话错误消息的数字编号
    $msg = curl_error($ch);//返回一个的包含当前对话错误消息的char串
    $info = curl_getinfo($ch);//获取一个的curl连接资源的消息
    curl_close($ch);//关闭对话，并释放资源
    return array(
        'data' => $result,
        'code' => $code,
        'msg' => $msg,
        'info' => $info
    );
}

function curl_get($url, $headers = '', $timeout = 3)
{
    $ch = curl_init();//初始化一个的curl对话，返回一个链接资源句柄
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_HEADER => false,
        CURLOPT_NOBODY => false,
        CURLOPT_POST => false,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
    );
    curl_setopt_array($ch, $options);//
    if (is_array($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    $result = curl_exec($ch);//执行一个的curl对话
    $code = curl_errno($ch);//返回一个的包含当前对话错误消息的数字编号
    $msg = curl_error($ch);//返回一个的包含当前对话错误消息的char串
    $info = curl_getinfo($ch);//获取一个的curl连接资源的消息
    curl_close($ch);//关闭对话，并释放资源
    return array(
        'data' => $result,
        'code' => $code,
        'msg' => $msg,
        'info' => $info
    );
}

/*
如果你喜欢喝茶，一定要来贵州品下湄潭茶海的茶 #贵州  #旅行 https://v.douyin.com/JjssFsv/ 复制此链接，打开【抖音短视频】，直接观看视频！
昨天有人说看了我的视频，终于不用羡慕瑞士了，我说，那是你对祖国不够了解。 #旅行 https://v.douyin.com/JjsWP3B/ 复制此链接，打开【抖音短视频】，直接观看视频！
旅行的意义：不在景区，不在终点，在路上！   #最美公路  #旅行  #自驾游 https://v.douyin.com/JjsTsRP/ 复制此链接，打开【抖音短视频】，直接观看视频！
人的一生注定会遇到两个人，一个惊艳了时光，一个温柔了岁月。#风景 #旅行 https://v.douyin.com/Jjspvfd/ 复制此链接，打开【抖音短视频】，直接观看视频！
人生苦短,无谓去担心别人怎么想你怎么说你,做你想做的,快乐点。#旅行 #风景 https://v.douyin.com/JjsgruF/ 复制此链接，打开【抖音短视频】，直接观看视频！
旅行，就是从自己活腻的地方到别人活腻的地方去。#旅行 #风景 https://v.douyin.com/JjsHMhG/ 复制此链接，打开【抖音短视频】，直接观看视频！
路，好不好走，自己亲自走过才知道，风景美不美，自己亲眼看过才知道。#风景 #旅行 https://v.douyin.com/Jjsq5gr/ 复制此链接，打开【抖音短视频】，直接观看视频！
你们错过的风景，我都给你们记录下来了，希望有一天你们能亲自看到 #风景 https://v.douyin.com/Jjs3mrh/ 复制此链接，打开【抖音短视频】，直接观看视频！
睡在山海间，住在人情里，旅行，也是一场修行。  #旅行  #风景 https://v.douyin.com/JjsXxKG/ 复制此链接，打开【抖音短视频】，直接观看视频！

上帝的杰作，还是人类的伟大！#航拍 https://v.douyin.com/Jjcx51R/ 复制此链接，打开【抖音短视频】，直接观看视频！
愿你的世界，星光满载，初心不改，走过汹涌的人潮和历经生活的磨难，所托良人。#风景 https://v.douyin.com/JjcXYum/ 复制此链接，打开【抖音短视频】，直接观看视频！
人生最好的旅行就是你在一个陌生的地方，发现一种久违的感动@影途时光 @绝色倾城 https://v.douyin.com/JjctFF5/ 复制此链接，打开【抖音短视频】，直接观看视频！
诗人喜欢远方和流浪，我偏爱你与人间烟火。#旅行 #风景 #云南 https://v.douyin.com/JjcQJQU/ 复制此链接，打开【抖音短视频】，直接观看视频！
生活需要仪式感，就像平凡的日子需要一束光。旅行绝对是点缀生活最好的方式。 #风景 https://v.douyin.com/JjcVHXh/ 复制此链接，打开【抖音短视频】，直接观看视频！
我们穷极一生追求的幸福，不在过去，不在未来，在当下，眼中景，碗中餐，身边人 https://v.douyin.com/JjcHpw7/ 复制此链接，打开【抖音短视频】，直接观看视频！
很多美好只适合一个人享受，比如思念、孤独、等待。又或者一首久违的老歌  #旅行 https://v.douyin.com/JjcVBpJ/ 复制此链接，打开【抖音短视频】，直接观看视频！
一河隔两岸，自有两重天。 #风景怎么拍都很美。 #旅行 https://v.douyin.com/JjcQqKE/ 复制此链接，打开【抖音短视频】，直接观看视频！
地球腰带上的“绿宝石“，此生必去的浪漫之地 #贵州  #旅行 https://v.douyin.com/JjcpnSK/ 复制此链接，打开【抖音短视频】，直接观看视频！
上帝一定是打翻了调色盘，才成就了 #贵州这么美的地方 #加榜梯田   #旅行 https://v.douyin.com/Jjcnyxd/ 复制此链接，打开【抖音短视频】，直接观看视频！

世界最壮观的超级山路之一， #张家界天门山99道弯，值得一看 #旅行 https://v.douyin.com/J6JEKtA/ 复制此链接，打开【抖音短视频】，直接观看视频！
有的路，你必须一个人走，这不是孤独，而是选择。 #风景  #摄影 https://v.douyin.com/J6eJ8mW/ 复制此链接，打开【抖音短视频】，直接观看视频！
中国最著名的三大拐弯之一，当之无愧的震撼 #旅行  #风景 https://v.douyin.com/J6JTbLs/ 复制此链接，打开【抖音短视频】，直接观看视频！
生活不止诗和远方，还有我们一直念念不忘的家乡。  #摄影#风景  #田园生活 https://v.douyin.com/J6Jc7Lv/ 复制此链接，打开【抖音短视频】，直接观看视频！
即使走过一些弯路，也比原地踏步好，至少见识的更多！ #旅行  #风景 https://v.douyin.com/J6JqAed/ 复制此链接，打开【抖音短视频】，直接观看视频！
在这样的世外桃源里度假1个月，你最想带谁来？ #旅行  #风景 https://v.douyin.com/J6JWMN9/ 复制此链接，打开【抖音短视频】，直接观看视频！
生活不止眼前的苟且，还有我主页里面的精彩。 #旅行  #风景 https://v.douyin.com/J6JK7K3/ 复制此链接，打开【抖音短视频】，直接观看视频！
当这首熟悉的曲子响起的时候，内心顿时感慨万千。 #旅行  #风景 https://v.douyin.com/J6J48bY/ 复制此链接，打开【抖音短视频】，直接观看视频！
我向往的远方不是某个地方，而是去远方途中的经历。 #旅行    #滇藏线 https://v.douyin.com/J6Jq97a/ 复制此链接，打开【抖音短视频】，直接观看视频！
旅行中，珍惜所有的不期而遇，看淡所有的不辞而别。 #旅行  #风景 https://v.douyin.com/J6JGf3s/ 复制此链接，打开【抖音短视频】，直接观看视频！
多走了弯路不一定吃亏，但肯定会有不一样的收获。 #旅行  #风景 https://v.douyin.com/J6e1yBx/ 复制此链接，打开【抖音短视频】，直接观看视频！
即使生活一地鸡毛，我们也要把它扎成一个鸡毛掸子。 #旅行  #风景   #自驾游 https://v.douyin.com/J6JK4yV/ 复制此链接，打开【抖音短视频】，直接观看视频！


小的时候，向往外面的世界，大了反而想回到最开始的地方！#旅行 #家乡 #风景 https://www.iesdouyin.com/share/video/6857686005428800782/?region=CN&mid=6857686121065794318&u_code=19118hi08&titleType=title&utm_source=copy_link&utm_campaign=client_share&utm_medium=android&app=aweme 复制此链接，打开【抖音短视频】，直接观看视频！
西藏，这是一个你没有任何理由不来的地方。 #旅行  #自驾游 https://v.douyin.com/J6LvpC3/ 复制此链接，打开【抖音短视频】，直接观看视频！
起步到成功，中间会有漫长的过渡期，那个时期我们称为‘坚持’。 #旅行  #风景 https://v.douyin.com/J6LsSor/ 复制此链接，打开【抖音短视频】，直接观看视频！
总有一个人的出现，让你感觉，人间值得。 #旅行  #风景  #自驾游 https://v.douyin.com/J6LsNuh/ 复制此链接，打开【抖音短视频】，直接观看视频！
给时间一点时间，等过去过去，让开始开始。 #旅行  #风景 https://v.douyin.com/J6NResm/ 复制此链接，打开【抖音短视频】，直接观看视频！
西藏就像一张过滤网，能净化你心中的浮躁，脑海里的杂念。 #旅行  #风景 https://v.douyin.com/J6LK1sr/ 复制此链接，打开【抖音短视频】，直接观看视频！
这里是特克斯八卦城，全城没有一个红绿灯。 #旅行  #自驾游  #八卦城 https://v.douyin.com/J6LgTCv/ 复制此链接，打开【抖音短视频】，直接观看视频！
或许我们走那么远，并不完全为了风景，只是想会一会不一样的自己。 #旅行 https://v.douyin.com/J6NR7KS/ 复制此链接，打开【抖音短视频】，直接观看视频！
就是在这里，我把科三又重温了一遍。 #旅行  #风景  #自驾游 https://v.douyin.com/J6LgNwn/ 复制此链接，打开【抖音短视频】，直接观看视频！
努力不是非要拼出输赢，只是莫负青春。 #旅行  #风景 https://v.douyin.com/J6LE2r2/ 复制此链接，打开【抖音短视频】，直接观看视频！
努力的意义就是为了看到更大的世界，也是为了让世界看到自己。#旅行 @一方烟火 https://v.douyin.com/J6LoXwP/ 复制此链接，打开【抖音短视频】，直接观看视频！
即使你的努力没人看得见，老天也会为你比心。 #旅行  #自驾游  #风景 https://v.douyin.com/J6LG2cs/ 复制此链接，打开【抖音短视频】，直接观看视频！
如果生活很沉闷，那就跑起来，因为跑起来有风。 #旅行  #风景  #u型公路 https://v.douyin.com/J6Lg8NX/ 复制此链接，打开【抖音短视频】，直接观看视频！
必须努力，还要潇洒，乾坤未定，你我皆是黑马。 #旅行  #风景 https://v.douyin.com/J6NRpRp/ 复制此链接，打开【抖音短视频】，直接观看视频！
熬过无人问津的日子，才能得到诗和远方。 #旅行  #风景  #自驾游 https://v.douyin.com/J6NJUdf/ 复制此链接，打开【抖音短视频】，直接观看视频！
只有走过最艰难的路，才能看见最美的风景。 #旅行  #风景  #自驾川藏 https://v.douyin.com/J6LVvUj/ 复制此链接，打开【抖音短视频】，直接观看视频！
做你喜欢的事叫自由，喜欢你做的事叫幸福。 #旅行  #风景 https://v.douyin.com/J6LgJ8D/ 复制此链接，打开【抖音短视频】，直接观看视频！
其实一直思念的不是故乡，而是童年，愿你出走半生，归来仍是少年。 #旅行  #风景 https://v.douyin.com/J6LGWUd/ 复制此链接，打开【抖音短视频】，直接观看视频！
旅途中的遇见让我明白：一辈子很短，要么老去，要么有趣。#西藏风景  #旅行 https://v.douyin.com/J6N8rYp/ 复制此链接，打开【抖音短视频】，直接观看视频！
任何你喜欢和坚持做的事，都不叫浪费时间。 #旅行  #风景 https://v.douyin.com/J6Lb8L1/ 复制此链接，打开【抖音短视频】，直接观看视频！
南迦巴瓦峰，十人九不遇，传说看见一次，就会幸运一年。 #旅行 #风景 https://v.douyin.com/J6L3nNH/ 复制此链接，打开【抖音短视频】，直接观看视频！
在路上，可以忘记自己的年纪，在路上，可以忘记所有的烦恼。 #旅行  #自驾游 https://v.douyin.com/J6LnMEM/ 复制此链接，打开【抖音短视频】，直接观看视频！
要经历什么样的风雨，才能遇见双彩虹。 #旅行  #风景 https://v.douyin.com/J6LsR96/ 复制此链接，打开【抖音短视频】，直接观看视频！
这样一个童话般的世界里，你愿意待多久呢？ #旅行  #风景 https://v.douyin.com/J6NRs95/ 复制此链接，打开【抖音短视频】，直接观看视频！
每当我找不到存在的意义，我就去看看这个世界。 #旅行  #风景 https://v.douyin.com/J6LTxMY/ 复制此链接，打开【抖音短视频】，直接观看视频！
旅行就是走出安逸圈，教会你以一种全新的方式感受世界万物。 #旅行  #风景 https://v.douyin.com/J6N8y1g/ 复制此链接，打开【抖音短视频】，直接观看视频！
保持热爱，奔赴山海，去追寻，去经历，去后悔，你会发现人间值得。 #旅行 https://v.douyin.com/J6NdhEY/ 复制此链接，打开【抖音短视频】，直接观看视频！
如果你喜欢旅行，那么你一定会喜欢这条路。因为它叫独库公路。 #旅行  #风景 https://v.douyin.com/J6NJgeK/ 复制此链接，打开【抖音短视频】，直接观看视频！
旅行一直让我坚信，最美的风景，并不在景区。 #旅行  #风景 https://v.douyin.com/J6LG2VE/ 复制此链接，打开【抖音短视频】，直接观看视频！
有没有那么一个地方，让你想到就心血澎湃，特别想去。#新疆  #旅行  #风景 https://v.douyin.com/J6LTvQV/ 复制此链接，打开【抖音短视频】，直接观看视频！
生活的烦恼来自差一点，生活的愉悦来自刚刚好。 #旅行  #风景 https://v.douyin.com/J6LVK32/ 复制此链接，打开【抖音短视频】，直接观看视频！
愿你在我看不到的地方，安然无恙。 #旅行  #风景 https://v.douyin.com/J6LKPq2/ 复制此链接，打开【抖音短视频】，直接观看视频！
一旦踏上旅程，必须努力向前，愿你归来，仍是少年。 #旅行  #风景 https://v.douyin.com/J6LTXmK/ 复制此链接，打开【抖音短视频】，直接观看视频！
旅行和读书一样，都是在别人的世界，寻找自己。 #旅行  #风景 https://v.douyin.com/J6Lw2Uv/ 复制此链接，打开【抖音短视频】，直接观看视频！
有些路很远，走起来很累，但不走会后悔。 #旅行  #风景  #最美公路 https://v.douyin.com/J6LKpow/ 复制此链接，打开【抖音短视频】，直接观看视频！
时间会告诉我们，越简单的喜欢，越长远。 #旅行  #风景 https://v.douyin.com/J6LGYu9/ 复制此链接，打开【抖音短视频】，直接观看视频！
要承认自己很平凡，但更要拼命的追赶。 #旅行  #风景   #旅拍 https://v.douyin.com/J6LgfVH/ 复制此链接，打开【抖音短视频】，直接观看视频！
旅行的美好，莫过于带上你最想带的人。 #旅行  #风景  #云南 https://v.douyin.com/J6LbkmG/ 复制此链接，打开【抖音短视频】，直接观看视频！
不要让生活耗尽了热情，还有诗和远方在等你。 #旅行   #风景  #云南 https://v.douyin.com/J6LTKpg/ 复制此链接，打开【抖音短视频】，直接观看视频！
一个人行走的范围有多大，那他的世界就有多大。 #旅行  #风景  #自驾游 https://v.douyin.com/J6NJgau/ 复制此链接，打开【抖音短视频】，直接观看视频！
很多人私信问，为什么发条路会火，因为路就是远方，路就是向往，路就是希望。 #旅行 https://v.douyin.com/J6Ls9rS/ 复制此链接，打开【抖音短视频】，直接观看视频！
一定要活得精彩，不要跟世界赌气，因为世界根本不认识你。 #旅行  #风景 https://v.douyin.com/J6L7gbr/ 复制此链接，打开【抖音短视频】，直接观看视频！
这是通往阿里的路，朋友说，没去过阿里，就不要跟他聊西藏。 #西藏  #旅行 https://v.douyin.com/J6LWfkx/ 复制此链接，打开【抖音短视频】，直接观看视频！
只有在路上，你才能明确自己的方向。 #旅行  #风景  #自驾游  #西藏 https://v.douyin.com/J6L7tH5/ 复制此链接，打开【抖音短视频】，直接观看视频！
一定要在年轻的时候，做一些老了想起来会笑的事。 #旅行  #风景  #西藏 https://v.douyin.com/J6NdkqP/ 复制此链接，打开【抖音短视频】，直接观看视频！
你能走多远，你的眼光就有多远。 #旅行  #自驾游  #风景 https://v.douyin.com/J6LbbSd/ 复制此链接，打开【抖音短视频】，直接观看视频！
当我们找不到答案的时候，就去看一看这个世界。 #旅行  #风景  #自驾游 https://v.douyin.com/J6NeMNV/ 复制此链接，打开【抖音短视频】，直接观看视频！
当你意志不坚定的时候，一定要去一次西藏。 #西藏拉萨  #旅行  #自驾游 https://v.douyin.com/J6LwdAV/ 复制此链接，打开【抖音短视频】，直接观看视频！
旅行如同生活，负重太多，前行很难。 #年保玉则  #旅行  #风景  #自驾游 https://v.douyin.com/J6LW93B/ 复制此链接，打开【抖音短视频】，直接观看视频！
这个地方，我称它为仙境应该不为过吧。 #旅行  #风景  #自驾游 https://v.douyin.com/J6N1nCA/ 复制此链接，打开【抖音短视频】，直接观看视频！
旅行，去哪里并不重要，心宽，那便是远方。 #旅行  #自驾游  #风景 https://v.douyin.com/J6LWMSC/ 复制此链接，打开【抖音短视频】，直接观看视频！
只要在旅行的路上，就能忘记所有的烦恼。 #旅行  #西藏自驾游   #风景 https://v.douyin.com/J6NRGBY/ 复制此链接，打开【抖音短视频】，直接观看视频！
生活不需要比别人好，但一定要比以前过得好。 #旅行  #自驾318  #风景 https://v.douyin.com/J6LnbAp/ 复制此链接，打开【抖音短视频】，直接观看视频！
旅行，总能让我在陌生的地方，发现一种久违的感动。 #旅行  #自驾川藏  #风景 https://v.douyin.com/J6Lbprd/ 复制此链接，打开【抖音短视频】，直接观看视频！
若不是亲眼所见，谁能相信还有如此美丽有意境的地方。 #旅行  #风景  #自驾游 https://v.douyin.com/J6LVaV8/ 复制此链接，打开【抖音短视频】，直接观看视频！
每个人心中都有一对翅膀，就看你什么时候想飞。 #旅行  #自驾游  #风景 https://v.douyin.com/J6LqLA5/ 复制此链接，打开【抖音短视频】，直接观看视频！
我们渴望的不是旅行，不是风景，只是想通过旅行找回真正的自己。 #自驾游  #旅行 https://www.iesdouyin.com/share/video/6835496584189414659/?region=CN&mid=6835496738447477512&u_code=19118hi08&titleType=title&utm_source=copy_link&utm_campaign=client_share&utm_medium=android&app=aweme 复制此链接，打开【抖音短视频】，直接观看视频！
好的风景，不在乎什么天气，任何时候都美丽。#旅行 #风景 #扎尕那 https://www.iesdouyin.com/share/video/6835268989275426059/?region=CN&mid=6835269298447125255&u_code=19118hi08&titleType=title&utm_source=copy_link&utm_campaign=client_share&utm_medium=android&app=aweme 复制此链接，打开【抖音短视频】，直接观看视频！
经常旅行的人，他一定很大度随和，因为他的心里是整个世界。 #旅行  #自驾游 https://v.douyin.com/J6N8kYK/ 复制此链接，打开【抖音短视频】，直接观看视频！
那些感觉毫无意义的重复，一定会在某一天，让你看到坚持的意义。 #旅行  #风景 https://v.douyin.com/J6NdBWE/ 复制此链接，打开【抖音短视频】，直接观看视频！
旅行就是为了让我们经历更多，见识更广，体验更深。 #旅行  #自驾游  #风景 https://v.douyin.com/J6LssaY/ 复制此链接，打开【抖音短视频】，直接观看视频！
这是目前保存最完整的一个原始部落了。 #旅行  #自驾游  #风景 https://v.douyin.com/J6LWJbJ/ 复制此链接，打开【抖音短视频】，直接观看视频！
旅行的美好，并不在于风景，而是在于和谁同行。 #旅行  #自驾游  #最美公路 https://v.douyin.com/J6Neo7c/ 复制此链接，打开【抖音短视频】，直接观看视频！
每一次的出发，都能让我充满期待，收获了更多。#自驾游  #旅行  #风景 https://v.douyin.com/J6L3HTq/ 复制此链接，打开【抖音短视频】，直接观看视频！
西藏它不在布达拉宫，不在拉萨，它是前往西藏路上的经历和遇见。 #旅行 https://v.douyin.com/J6LofFr/ 复制此链接，打开【抖音短视频】，直接观看视频！
旅行让我明白，越简单的喜欢，越长久。 #旅行  #自驾游  #风景 https://v.douyin.com/J6LKhMB/ 复制此链接，打开【抖音短视频】，直接观看视频！
从我脚底下这片被踩平的草地来看，这个地方应该是火了。  #旅行  #风景 https://v.douyin.com/J6LcUn9/ 复制此链接，打开【抖音短视频】，直接观看视频！
我向往的远方不是终点，而是去远方的经历。 #自驾游  #旅行风景  #最美公路 https://v.douyin.com/J6LtYhp/ 复制此链接，打开【抖音短视频】，直接观看视频！
旅行让我懂得，经历就是一种财富。 #自驾游  #旅行  #风景  #七彩丹霞 https://v.douyin.com/J6LwBkB/ 复制此链接，打开【抖音短视频】，直接观看视频！
你要变得足够强大，然后才有然后。 #自驾游  #旅行  #风景 https://v.douyin.com/J6LGH3M/ 复制此链接，打开【抖音短视频】，直接观看视频！

书山有路勤为径@抖音小助手  https://v.douyin.com/JrN3gEW/ 复制此链接，打开【抖音短视频】，直接观看视频！
拽不倒踢不倒还狂奔50米，这是地球人能进的球吗？#罗纳尔多 #巴萨  https://v.douyin.com/JrNtFWp/ 复制此链接，打开【抖音短视频】，直接观看视频！
@抖音小助手 @DOU+小助手 @抖音推广助手 #上热门🔥 #护卫犬 #上热门🔥 #开了挂的马犬#上热门  https://v.douyin.com/JrNwEJg/ 复制此链接，打开【抖音短视频】，直接观看视频！
高手在民间  https://v.douyin.com/JrNg6pA/ 复制此链接，打开【抖音短视频】，直接观看视频！
帅哥跳的太棒了👍👍👍  https://v.douyin.com/JrNbN9U/ 复制此链接，打开【抖音短视频】，直接观看视频！
🦐:我都等半天了，你这俩货到底上不上啊😂😂  https://v.douyin.com/JrNnod5/ 复制此链接，打开【抖音短视频】，直接观看视频！
这就是#鲁尼 ，一个为了#足球 梦想永不言弃的热血少年！#遇见足球  https://v.douyin.com/JrF1psc/ 复制此链接，打开【抖音短视频】，直接观看视频！
#田径田径  手记11秒的慢动作  https://v.douyin.com/JrN3SRd/ 复制此链接，打开【抖音短视频】，直接观看视频！
谢谢你们的关注，乡村教师的苦一言难尽，控辍保学，太难了！天天追学生@ #控辍保学永远在路上 @抖音小助手  https://v.douyin.com/JrNGw5k/ 复制此链接，打开【抖音短视频】，直接观看视频！
小姐姐倒是照完镜子就走了，留我心动好几天 #苹果原相机  https://v.douyin.com/JrNogSY/ 复制此链接，打开【抖音短视频】，直接观看视频！
小伙子跳的很到位…#梧桐山 #深圳  https://v.douyin.com/JrFJCbU/ 复制此链接，打开【抖音短视频】，直接观看视频！
幸福的一家4口#竹鸡#爱护小动物  https://v.douyin.com/JrNKAj6/ 复制此链接，打开【抖音短视频】，直接观看视频！
指桑骂槐#重温经典  https://v.douyin.com/JrNXXjj/ 复制此链接，打开【抖音短视频】，直接观看视频！
雷霆小将弗格森折叠空接，在空中还等了一会儿球。 #重启新赛季  https://v.douyin.com/JrNw1JK/ 复制此链接，打开【抖音短视频】，直接观看视频！
ok  https://v.douyin.com/JrNGMs9/ 复制此链接，打开【抖音短视频】，直接观看视频！
李树枝穿着这身衣服进城，太有挑战了。  https://v.douyin.com/JrNwtfx/ 复制此链接，打开【抖音短视频】，直接观看视频！
ook  https://v.douyin.com/JrNEdXu/ 复制此链接，打开【抖音短视频】，直接观看视频！
#鑫禾面条哥 @DOU+小助手 #抖音小助手  https://v.douyin.com/JrNw4uD/ 复制此链接，打开【抖音短视频】，直接观看视频！
写好字的核心贵在走心！#书法  https://v.douyin.com/JrNKsoB/ 复制此链接，打开【抖音短视频】，直接观看视频！
年轻的帕奎奥通过连翻轰炸征服对手#dou出真功夫 #拳击 #帕奎奥 @抖音格斗  https://v.douyin.com/JrNXWgR/ 复制此链接，打开【抖音短视频】，直接观看视频！

带我回家的海上小火车..  https://v.douyin.com/Jh829B4/


mn  https://v.douyin.com/JhAj2QD/ 复制此链接，打开【抖音短视频】，直接观看视频！
总是有人说我整容，我不知道我哪里还需要整了？  https://v.douyin.com/JhAA6oQ/ 复制此链接，打开【抖音短视频】，直接观看视频！
献丑了宝贝们  https://v.douyin.com/JhAdFc9/ 复制此链接，打开【抖音短视频】，直接观看视频！
美丽不是错 嫉妒才是原罪  https://v.douyin.com/JhABWoW/ 复制此链接，打开【抖音短视频】，直接观看视频！
个子不高   脾气不小😂😂  https://v.douyin.com/JhArubw/ 复制此链接，打开【抖音短视频】，直接观看视频！
该如何？  https://v.douyin.com/JhALnso/ 复制此链接，打开【抖音短视频】，直接观看视频！
不能停止我对你的爱❤️  https://v.douyin.com/JhASHvf/ 复制此链接，打开【抖音短视频】，直接观看视频！
别追公交车了 追我吧 我跑得慢 还有点可爱  https://v.douyin.com/JhAM4m1/ 复制此链接，打开【抖音短视频】，直接观看视频！
天气这么热，给大家看个热舞吧。￼  https://v.douyin.com/JhABEfr/ 复制此链接，打开【抖音短视频】，直接观看视频！
为什么不点赞，是不是欲擒故纵？￼  https://v.douyin.com/JhAk4Nf/ 复制此链接，打开【抖音短视频】，直接观看视频！
我感觉我懂你的特别🌈  https://v.douyin.com/JhAk5pG/ 复制此链接，打开【抖音短视频】，直接观看视频！
叫宝贝  https://v.douyin.com/JhAf4oM/ 复制此链接，打开【抖音短视频】，直接观看视频！
泡温泉吗♨️  https://v.douyin.com/JhAB6Lr/ 复制此链接，打开【抖音短视频】，直接观看视频！
这是什么歌也太好听了吧？？  https://v.douyin.com/JhA6K1w/ 复制此链接，打开【抖音短视频】，直接观看视频！
 */
$lines = <<<URLS
诱惑之成熟的味道  https://v.douyin.com/JBnSdMf/ 复制此链接，打开【抖音短视频】，直接观看视频！
自己的感受比道理更重要 希望你懂～  https://v.douyin.com/JBnP4M8/ 复制此链接，打开【抖音短视频】，直接观看视频！
#东方舞鼓舞 每一个不曾起舞的日子 都是对生命的辜负  https://v.douyin.com/JBnD9jD/ 复制此链接，打开【抖音短视频】，直接观看视频！
如果世界对你不温柔 可以让我试试 做你的世界吗。  https://v.douyin.com/JBntyKW/ 复制此链接，打开【抖音短视频】，直接观看视频！
这是说了谢谢反而才亏欠的情感....这歌词直戳人心听着鼻酸  https://v.douyin.com/JBntyRH/ 复制此链接，打开【抖音短视频】，直接观看视频！
做你自己 爱你的人永远爱你  https://v.douyin.com/JBnExXD/ 复制此链接，打开【抖音短视频】，直接观看视频！
不想长大  一直浪漫吧  https://v.douyin.com/JBW11Vf/ 复制此链接，打开【抖音短视频】，直接观看视频！
接纳自己，成为自己 ^_^圣诞节快乐  https://v.douyin.com/JBWetBd/ 复制此链接，打开【抖音短视频】，直接观看视频！
不会吧不会吧，不会真的有人以为我是微胖吧  https://v.douyin.com/JBWRV7B/ 复制此链接，打开【抖音短视频】，直接观看视频！
其实美的不是风景，而是你的心境。平淡中，心是滚烫的。  https://v.douyin.com/JBWL1dc/ 复制此链接，打开【抖音短视频】，直接观看视频！
原本想一口一口吃掉忧愁，不料一口一口吃成肉球  https://v.douyin.com/JBnoYV3/ 复制此链接，打开【抖音短视频】，直接观看视频！
回眸一笑百媚生￼  https://v.douyin.com/JBnKdtV/ 复制此链接，打开【抖音短视频】，直接观看视频！
#下雨天不要问我为何每天如此大的马力，我只是幼儿园毕业十几年的孩子#全美丽  https://v.douyin.com/JBnKWFg/ 复制此链接，打开【抖音短视频】，直接观看视频！
有一种清纯叫做没洗脸#校花 #少女写真 #清纯女神 #美女 #校花评选 #北京舞蹈学院 #清纯甜美 #艺考生  https://v.douyin.com/JBWeh3A/ 复制此链接，打开【抖音短视频】，直接观看视频！
#lisa #lisa的ending #howyoulikethat #blackpink  https://v.douyin.com/JBnEpvK/ 复制此链接，打开【抖音短视频】，直接观看视频！
最后一个这衣服的库存了 l流泪  https://v.douyin.com/JBWNVo9/ 复制此链接，打开【抖音短视频】，直接观看视频！
别看了 快到我怀里来吧 最近你辛苦了  https://v.douyin.com/JBWJ1Ka/ 复制此链接，打开【抖音短视频】，直接观看视频！
想在评论区看到一片红色心海，以在座的各位应该没问题吧 #苏幕遮  https://v.douyin.com/JBncuLc/ 复制此链接，打开【抖音短视频】，直接观看视频！
#减肥 #运动 让我们一起消灭啤酒肚！  https://v.douyin.com/JBnKT7y/ 复制此链接，打开【抖音短视频】，直接观看视频！
人生最孤独的瞬间，是突然看懂梵高的画。#美术生 #梵高  https://v.douyin.com/JBWLstU/ 复制此链接，打开【抖音短视频】，直接观看视频！

老板昨天喝多啦悄悄的对我说：你那天穿的裙子很漂亮☺️真的挺漂亮 是什么意思啊？  https://v.douyin.com/JBnTJxe/ 复制此链接，打开【抖音短视频】，直接观看视频！
今天面试，老板亲自送我回去☺️好紧张  https://v.douyin.com/JBncmq6/ 复制此链接，打开【抖音短视频】，直接观看视频！
王牌飞行员申请出战！  https://v.douyin.com/JBW8uhC/ 复制此链接，打开【抖音短视频】，直接观看视频！
爱啦  https://v.douyin.com/JBntxKS/ 复制此链接，打开【抖音短视频】，直接观看视频！
蜘蛛侠爆发 美貌终结者  https://v.douyin.com/JBWdSce/ 复制此链接，打开【抖音短视频】，直接观看视频！
你们喜欢哪一个我？1：工作帆2：做饭帆3：运动帆  https://v.douyin.com/JBWJLtv/ 复制此链接，打开【抖音短视频】，直接观看视频！
URLS;
//$lines = '';
foreach (explode("\n", $lines) as $shareText) {
    //$shareText = '书法，李白诗一首，请欣赏… https://v.douyin.com/JjgW5em/ 复制此链接，打开【抖音短视频】，直接观看视频！';

    preg_match('#(.*) (https://v\.douyin\.com/.*/)#i', $shareText, $matches);
    var_dump($matches);

    if ($matches) {
        list(, $description, $url) = $matches;
        $description = str_replace(['@DOU+小助手', '抖音', '小助手', ':', '/'], '', $description);
        //http://3g.gljlw.com/diy/ttxs_dy2.php?url=
        echo $queryUrl = 'http://3g.gljlw.com/diy/ttxs_dy.php?url='
            . str_replace(':', '%3A', $url) . '&r=' . ($r = '11463419003982045') . '&s=' . md5($url . '@&^' . $r);
        $header =<<<HEADER
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.122 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Referer: http://3g.gljlw.com/diy/douyin.php
Upgrade-Insecure-Requests: 1
HEADER;
        $opt = array('http' => array('header' => $header));
        $context = stream_context_create($opt);
        $file_contents = file_get_contents($queryUrl, false, $context);
//
//        $r = curl_get($queryUrl, explode("\r\n", $header));
//        file_put_contents('tmp.htm', $r['data']);
//        var_dump($r);
//        die;

        # !important:
        # bash命令行执行，才有curl命令；
        # Chrome-Copy as cURL(bash),参数是单引号，php里面需要改成双引号，否则不对，&不知怎么转义。。
//        $file_contents = `curl "$queryUrl" -H "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.122 Safari/537.36" -H "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9" -H "Referer: http://3g.gljlw.com/diy/douyin.php" --compressed --insecure`;
//        curl 'http://3g.gljlw.com/diy/ttxs_dy.php?url=https%3A//v.douyin.com/JhAj2QD/&r=11463419003982045&s=d0d638a927757e1dcb75f7c3ad23fcd1' \
//        -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.122 Safari/537.36' \
//        -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9' \
//        -H 'Referer: http://3g.gljlw.com/diy/douyin.php' \
//        --compressed \
//        --insecure
//        $file_contents = `curl "http://3g.gljlw.com/diy/ttxs_dy2.php?url=https%3A%2F%2Fv.douyin.com%2FJhAj2QD%2F&r=11463419003982045&s=d0d638a927757e1dcb75f7c3ad23fcd1" -H "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.122 Safari/537.36" -H "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9" -H "Referer: http://3g.gljlw.com/diy/douyin.php" --compressed --insecure`;

        file_put_contents('tmp.htm', $file_contents);
        preg_match('#class="KL_bbs_textarea" rows="5" style="width:100%">(.*)</textarea>#', $file_contents, $matches, 0, 2858);
//        var_dump($matches);
//        die;
        if ($matches) {
            $downloadUrl = $matches[1];
            if (strpos($downloadUrl, 'yximgs.com')) die('获取视频失败！');
//        $downloadUrl = 'http://v9-hs.ixigua.com/a3bf3dca805e49563914a968519142a7/5f294a0e/video/tos/cn/tos-cn-ve-15/28342504453f41679e2bdf4f472db1ba/?a=1112&br=3177&bt=1059&cr=0&cs=0&dr=3&ds=6&er=&l=202008041843130100140490900E00A4D6&lr=&mime_type=video_mp4&qs=0&rc=MzlmOXQ6eG1ndDMzZWkzM0ApZDw8ZWdlOjw2NzpmNzc5ZmdeXjJnaWplcmFfLS0zLS9zczIuLS41MTZhMTVgYGJgNDM6Yw%3D%3D&vl=&vr=';
            file_put_contents("生活#日常#$description.mp4", file_get_contents($downloadUrl));
//            $fileName = "生活#日常#$description.mp4";
//            file_put_contents('down' . date('YmdH') . '.sh', "curl '$downloadUrl' -o '$fileName'\n", FILE_APPEND);
        }
    }
}
