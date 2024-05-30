<?php

include 'include.php';

function getAllPHPFiles($folder)
{
    // var_dump($folder,file_exists($folder), is_dir($folder));
    // die;
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST,
        RecursiveIteratorIterator::CATCH_GET_CHILD
    );

    foreach ($iterator as $path => $file) {
        if ($file->isFile() && $file->getExtension() === 'html') {
            $files[] = $path;
        }
    }

    return $files;
}

// $files = getAllPHPFiles('D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first');
// var_export($files);
$files = array(
    // 0 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\activity\\activity_list.html',
    // 1 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\activity\\event.html',
    // 2 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\activity\\index.html',
    // 3 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\alipay\\alipay_success_synchro.html',
    // 4 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\alipay\\index.html',
    // 5 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\article\\news_detail.html',
    // 6 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\article\\news_list.html',
    // 7 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\article\\unified_list.html',
    // 8 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\callback\\pay_success_synchro.html',
    // 9 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\index\\agree.html',
    // // 10 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\index\\index.html',
    // 11 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\index\\more_list.html',
    // 12 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\index\\search.html',
    // 13 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\index\\unified_list.html',
    14 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\live\\index.html',
    // 15 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\login\\index.html',
    // 16 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\material\\material_list.html',
    // 17 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\material\\my_material.html',
    // 18 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\member\\member_manage.html',
    // 19 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\member\\member_recharge.html',
    // 20 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\merchant\\income.html',
    // 21 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\merchant\\index.html',
    // 22 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\merchant\\info.html',
    // 23 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\merchant\\teacher_detail.html',
    // 24 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\merchant\\teacher_list.html',
    // 25 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\address.html',
    // 26 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\bill_detail.html',
    // 27 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\coin_detail.html',
    // 28 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\edit_address.html',
    // 29 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\express.html',
    // 30 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\gold_coin.html',
    // // 31 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\index.html',
    // 32 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\my_gift.html',
    // // 33 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\order.html',
    // // 34 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\order_list.html',
    // // 35 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\order_pink.html',
    // // 36 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\order_pink_after.html',
    // 37 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\order_reply.html',
    // 38 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\order_store_list.html',
    // 39 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\order_verify.html',
    // 40 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\refund_apply.html',
    // 41 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\save_phone.html',
    // 42 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\sign_in.html',
    // 43 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\sign_in_list.html',
    // 44 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\sign_list.html',
    // 45 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\sign_order.html',
    // 46 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\user_info.html',
    // 47 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\my\\verify_activity.html',
    // // 48 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\public\\container.html',
    // // 49 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\public\\error.html',
    // // 50 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\public\\foot.html',
    // // 51 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\public\\head.html',
    // // 52 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\public\\requirejs.html',
    // // 53 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\public\\right_nav.html',
    // // 54 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\public\\store_menu.html',
    // // 55 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\public\\style.html',
    // // 56 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\public\\success.html',
    // 57 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\service\\server_ing.html',
    // 58 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\service\\service_ing.html',
    // 59 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\service\\service_list.html',
    // 60 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\service\\service_new.html',
    // 61 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\data_details.html',
    // 62 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\details.html',
    // 63 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\exchange.html',
    // 64 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\gift.html',
    // 65 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\gift_receive.html',
    // 66 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\gift_special.html',
    // 67 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\grade_list.html',
    // 68 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\grade_special.html',
    // 69 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\group_list.html',
    // 70 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\order_pink.html',
    // 71 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\pink.html',
    // 72 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\play.html',
    // 73 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\poster_show.html',
    // 74 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\record.html',
    // 75 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\single_details.html',
    // 76 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\single_text_detail.html',
    // 77 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\source_detail.html',
    // 78 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\special_cate.html',
    // 79 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\task_info.html',
    // 80 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\special\\text_detail.html',
    // 81 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\spread\\become_promoter.html',
    // 82 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\spread\\commission.html',
    // 83 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\spread\\my_promoter.html',
    // 84 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\spread\\poster_special.html',
    // 85 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\spread\\poster_spread.html',
    // 86 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\spread\\special.html',
    // 87 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\spread\\spread.html',
    // 88 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\spread\\spread_detail.html',
    // 89 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\spread\\withdraw.html',
    // 90 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\store\\detail.html',
    // 91 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\store\\index.html',
    // 92 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\store\\order_confirm.html',
    // 93 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\topic\\certificate_detail.html',
    // 94 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\topic\\certificate_list.html',
    // 95 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\topic\\problem_detail.html',
    // 96 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\topic\\problem_index.html',
    // 97 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\topic\\problem_result.html',
    // 98 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\topic\\problem_sheet.html',
    // 99 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\topic\\question_category.html',
    // 100 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\topic\\question_detail.html',
    // 101 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\topic\\question_detail_wrong.html',
    // 102 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\topic\\question_index.html',
    // 103 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\topic\\question_result.html',
    // 104 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\topic\\question_sheet.html',
    // 105 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\topic\\question_user.html',
    // 106 => 'D:\\laragon\\www\\cqiu\\video3\\application\\wap\\view\\first\\topic\\question_wrong.html',,,
);
echo '<ol>';
foreach ($files as $f) {
    echo '<li>', $f, PHP_EOL;
    $folder = basename(dirname($f));
    $name = basename($f, '.html');
    $content = file_get_contents($f);

    $content = str_replace('/wap/first/zsff/images/', '../../static/images2/', $content);
    $content = preg_replace("#\{:Url\('([^']+)'\)\}#i", '$1', $content);
    $content = str_replace("<a ", '<navigator class="a" ', $content);
    $content = str_replace("</a>", '</navigator>', $content);
    $content = str_replace(" :href=", ' :url=', $content);

    addPage($content, $folder, $name);
    savePageContent($content, $folder, $name);
    // break;
}

// --------------------
function addPage($content, $folder, $name)
{
    preg_match('#\{block name="title"\}(.*?)\{/block\}#s', $content, $matches);
    // var_dump($matches);
    $title = $matches[1]??'';
    $json = <<<PAGE
		{
			"path" : "pages/$folder/$name",
			"style" :
			{
				"navigationBarTitleText" : "$title",
				"enablePullDownRefresh" : false
			}
		}
PAGE;
    $pageFile = 'D:\laragon\www\cqiu\video3\video-uniapp\pages.json';
    $newContent = str_replace(
        '}
	],
	"subpackages": [{', '},
' . $json . '
	],
	"subpackages": [{', file_get_contents($pageFile)
    );
    file_put_contents($pageFile, $newContent);
}

// --------------------
function trans($content)
{
    // $string = '<a :href="\'{:url(\'topic/problem_index\')}?id=\' + item.id">练习</a>';
    $pattern = '/<a :href="\'\{:url\(\'([^\'\"]+)\'\)}([^"]+)\"([^>]*)>([^<]+)<\/a>/';
    $replacement = '<view class="a" @click="goUrl(\'$1$2)"$3>$4</view>';
    $result = preg_replace($pattern, $replacement, $content);
    // echo '<xmp>', $result, '</xmp>';
    return $result;
}

function savePageContent($content, $folder, $name)
{
    preg_match('#<style>(.*)</style>#s', $content, $matches);
    // var_dump($matches);
    $css = $matches[1]??'';

    preg_match('#\{block name="content"\}(.*?)\{/block\}#s', $content, $matches);
    // var_dump($matches);
    $html = $matches[1]??'';
    $html = preg_replace('#<script>(.*)</script>#s', '', $html);
    $html = trans($html);

    preg_match('#new Vue\(\{(.*)\);?\s*\}\);?\s*</script>#s', $content, $matches);
    // var_dump($matches);
    $js = $matches[1]??'';

    $f2 = "D:\laragon\www\cqiu\\video3\\video-uniapp\pages/$folder/$name.vue";
    var_dump($f2);

    is_dir($folder = dirname($f2)) or mkdir($folder);

    $newContent = <<<VUE
<template>
$html
</template>

<script>
import \$h from '../../common/helper.js';
import storeApi from '../../common/store.js';
import BaseLogin from '../../components/base-login.vue';
import quickMenu from '../../components/quick-menu.vue';

const app = getApp();
var site_name = app.globalData.site_name;
var wechat_qrcode = app.globalData.wechat_qrcode;

	export default {
$js
</script>

<style>
$css
</style>
VUE;
    file_put_contents($f2, $newContent);
}
