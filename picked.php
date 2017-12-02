<?php
require "loginheader.php";
require_once 'header.php';
$page_title = '精選文章';

$op = isset($_REQUEST['op']) ? filter_var($_REQUEST['op']) : '';
$sn = isset($_REQUEST['sn']) ? (int) $_REQUEST['sn'] : 0;
switch ($op) {
    case 'remove_focus':
        remove_focus($sn);
        echo "sucess";
        // header("location: picked.php");
        exit;
    default:
        $op = "";
        list_picked();
        break;
}

require_once 'footer.php';

function list_picked()
{
    global $db, $smarty;
    $tbl   = 'article';
    $where = "WHERE  `focus`='1' ";
    $sql   = "SELECT * FROM `{$tbl}` $where ORDER BY sort";

    include_once "PageBar.php";
    // $PageBar = getPageBar($db, $sql, 10, 10);
    // $bar     = $PageBar['bar'];
    // $sql     = $PageBar['sql'];
    // $total   = $PageBar['total'];
    //送至資料庫
    $result = $db->query($sql) or die($db->error);
    //取回資料
    $all = array();
    $i   = 0;
    while ($data = $result->fetch_assoc()) {
        $all[$i] = $data;
        $wkstr1  = mb_substr(strip_tags($data['content']), 0, 90);
        if (mb_strlen(strip_tags($data['content'])) > mb_strlen($wkstr1)) {
            $wkstr1 .= "<span style='color:red;font-size:0.6em;font-weight:900;'><<更多.......>></span>";
        }
        $all[$i]['summary'] = $wkstr1;
        $i++;
    }
    $smarty->assign('all', $all);
    // $smarty->assign('bar', $bar);
    // $smarty->assign('total', $total);
}
//將文章移除精選，focus欄位變回 0
function remove_focus($sn)
{
    global $db;

    $sql = "UPDATE `article` SET `focus`='0' WHERE `sn`='{$sn}'";
    $db->query($sql) or die($db->error);

    return $sn;
}
