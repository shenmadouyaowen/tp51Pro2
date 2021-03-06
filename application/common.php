<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 公用的方法  返回json数据，进行信息的提示
 * @param $status 状态
 * @param string $message 提示信息
 * @param array $data 返回数据
 */
function showMsg($status,$message = '',$data = array()){
    $result = array(
        'status' => $status,
        'message' =>$message,
        'data' =>$data
    );
    exit(json_encode($result));
}
/**
 * 进行图片数据的上传，写入表 xupload_imgs
 * @param string $slide_show
 * @param int $tag_id
 * @param int $type 0：商品轮播图  1：订单评论图片
 */
function uploadSlideShow($slide_show = '',$tag_id = 0,$type = 0){
    $arrSlideShow = explode(",",$slide_show);
    foreach ($arrSlideShow as $value){
        if ($value){
            $addData = [
                'tag_id' => $tag_id,
                'type'  => $type,
                'picture' => $value,
                'add_time' => date('Y-m-d H:i:s', time()),

            ];
            Db('xupload_imgs')
                ->insert($addData);
        }
    }
}

/**
 * 处理显示图片服务器地址,可进行图片服务器地址的处理
 * @param $imgUrl
 * @return string
 */
function imgToServerView($imgUrl)
{
    $imgServerUrl = config('app.IMG_SERVER_PUBLIC') . $imgUrl;
    return $imgServerUrl;
}
/**
 * ue编辑器通过FTP上传图片（$str代表从表单接收到的content字符串）
 * 返回处理后的content字符串
 * @param $str
 * @return mixed
 */
function ftpImageToServerUE($str)
{
    $imgServerPublic = config('app.IMG_SERVER_PUBLIC');
    $pattern = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png|\.jpeg|\.mp4]))[\'|\"].*?[\/]?>/";
    preg_match_all($pattern, $str, $match);
    foreach ($match[1] as $k => $v) {
        //此时 $subDealStr 为以“upload/”开头的图片路径
        $subDealStr = substr($v, 1);
        $remotefile = '/public' . $v;
        if (startsWithStr($subDealStr, "upload")) {
            //进行FTP 图片上传操作
            $ftp = new \app\api\controller\Upload();
            $ftp->ftpImageToServer($subDealStr, $remotefile);
            //ftp_upload($remotefile, $subDealStr);
        }
    }
    return str_replace("src=\"/upload", "src=\"$imgServerPublic/upload", $str);
}
/**
 * 判断是否以某个字符串开头
 * @param $str
 * @param $char
 * @return bool
 */
function startsWithStr($str, $char)
{
    return (bool)preg_match('/^' . $char . '/', $str);
}

/**
 * 设置后台管理员密码加密方式
 * @param string $input
 * @param int $tag 0：加密操作  1：解密操作
 * @return string
 */
function cmsAdminToLoginForPassword($input = '',$tag = 0){
    $makedStr = md5(base64_encode($input));
    return $makedStr;
}