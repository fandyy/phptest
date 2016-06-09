<?php

//保留标签
function strip_word_html($text, $allowed_tags = '<b><i><sup><sub><em><strong><u>')
{
    mb_regex_encoding('UTF-8');

    $search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u');
    $replace = array('\'', '\'', '"', '"', '-');

    $text = preg_replace($search, $replace, $text);


    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');//html_entity_decode() 函数把 HTML 实体转换为字符。


    if(mb_stripos($text, '/*') !== FALSE){
        $text = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm');
    }

    $text = preg_replace(array('/<([0-9]+)/'), array('< $1'), $text);

    $num_matches = preg_match_all("/\<style/u", $text, $matches);
    if($num_matches){
        $text = preg_replace('/\<style\>(.)*\<\/style\>/isu', '', $text);
    }

    $num_matches = preg_match_all("/\<script/u", $text, $matches);
    if($num_matches){
        $text = preg_replace('/\<script(.)*\>/isu', '', $text);
    }

    $text = strip_tags($text, $allowed_tags);


    $text = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ''), $text);

    $search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu');
    $replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>');
    $text = preg_replace($search, $replace, $text);

    $num_matches = preg_match_all("/\<!--/u", $text, $matches);
    if($num_matches){
        $text = preg_replace('/\<!--(.)*--\>/isu', '', $text);
    }



    return $text;
}

//去掉属性
function clean_inside_tags($txt,$tags){

    preg_match_all("/<([^>]+)>/i",$tags,$allTags,PREG_PATTERN_ORDER);

    foreach ($allTags[1] as $tag){
        $txt = preg_replace("/<".$tag."[^>]*>/i","<".$tag.">",$txt);
    }

    return $txt;
}

//用来过滤html
function trmhtml($text){
    $rm = '<p><img>';
    $text = str_replace("&nbsp;","",$text);
    $text = strip_word_html($text, $rm);
    $cit = clean_inside_tags($text, '<p>');
    $cit = str_replace("&nbsp;","",$cit);
    return $cit;
}



//使用
$str = '
<style>.h{font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
}</style>
<div>HHHEEE</div>
<div class="nav_ent"><p>sss</p>
<div class="nav_ent1_1"><a href="/"><img src="/news/images/topcopy_20130809.jpg" /></a></div>
<div class="nav_ent1"><a href="/news/"><img src="/news/images/news_t.jpg"  alt="新闻" border="0"></a></div>
<div class="nav_ent2"><table width="100%" class=43160 cellpadding="0" cellspacing="0"><tr><td valign=top><A href="/world/" target=_top>国际</A>&nbsp;&nbsp;<A href="/china/" target=_top>时政</A>&nbsp;&nbsp;<A href="/opinion/" target=_top>评论</A>&nbsp;&nbsp;<A href="/photo/" target=_blank>图片</A>&nbsp; <A href="/world/paper.htm" target=_blank>外媒</A>&nbsp; <A href="/news/live/" target=_top>直播</A>&nbsp; <A href="/news/special.htm" target=_top>专题</A>&nbsp; <A href="/news/update.htm" target=_blank>滚动</A></td></tr><tr><td align=right colspan=1></td></tr></table></div>
</div>
<!--asdfasdf-->
<div class="clearit"id="top" name="top"></div>
<div class="gotop"><a href="#top">&nbsp;</a></div>
</div>';

$str = file_get_contents('http://cn.bing.com/');

echo trmhtml($str);

