<?php

/**
 * Create by wey @ 2021/12/6 13:11 with PhpStorm , Copyright © 2021 , wey. All rights reserved.
 */
class ningxiav2
{
    /** appid **/
    private static $appid = '替换APPID';
    /** appkey = clientId **/
    private static $appkey = '替换APPKEY';
    /** appsecret = clientSecret **/
    private static $appsecret = '替换APPSECRET';
    /** weboauth 安全回调地址 **/
    private static $redirect_uri = 'http(s)://domain.com/REDIRECT_URL';
    /** cookie前缀 **/
    private static $cookie_pre = 'nxv2_';

    /**
     * 构造函数
     */
    public function __construct(){

    }

    /**  * * * * * * * * * * * *
     *                         *
     *         webauth区       * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                         *
     * * * * * * * * * * * * * */

    /**
     * 生成weboauth的访问链接
     * @return string
     */
    public static function create_oauth_url(){

        $weboauth = 'https://auth2.nxeduyun.com/oauth2/v1/connect/authorize';
        $weboauth .='?grant_type=authorization_code&response_type=code&state=web';
        $weboauth .= '&client_id='.self::$appkey;
        $weboauth .= '&redirect_uri='.urlencode(self::$redirect_uri.'/index.php');

        return $weboauth;
    }

    /**
     * 通过code换取token
     * @param $code
     * @return mixed
     */
    public static function code2access_token($code){

        $url = "https://auth2.nxeduyun.com/oauth2/v1/connect/token";
        $post_data = array(
            'grant_type'=>'authorization_code',
            'code'=>$code,
            'redirect_uri'=>self::$redirect_uri.'/index.php',
            'client_id'=>self::$appkey,
            'client_secret'=>self::$appsecret
        );

        $response = self::goPost($url, $post_data);
        $response = json_decode($response,true);

        return $response;
    }

    /**
     * 拼接api header 的Authorization
     * @return string
     */
    private static function cookie2authheader(){
        if(!$_COOKIE[self::$cookie_pre.'access_token'] || !$_COOKIE[self::$cookie_pre.'token_type']){
            //todo 重新授权
        }else{
            $Authorization = 'Authorization:'.$_COOKIE[self::$cookie_pre.'token_type'].' '.$_COOKIE[self::$cookie_pre.'access_token'];
        }

        return $Authorization;
    }

    /**
     * accesstoken换取基本用户信息
     * @param $token_type
     * @param $accesstoken
     * @return bool|string
     */
    public static function accesstoken2userinfo($code){
        $accesstoken = self::code2access_token($code);
        $url = "https://auth2.nxeduyun.com/oauth2/v1/connect/userinfo";
        $headers[] = 'Authorization:'.$accesstoken['token_type'].' '.$accesstoken['access_token'];
        $response = self::goGet($url,$headers);
        $response = json_decode($response,true);
        $response['id_token'] = $accesstoken['id_token'];
        return $response;
    }


    /**  * * * * * * * * * * * *
     *                         *
     *         api接口区        * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                         *
     * * * * * * * * * * * * * */


    /**
     * 获取班级学生
     * @param $classId
     * @return mixed
     */
    public static function api_getStudentByClassId($classId){
        $url = 'https://edu.nxeduyun.com/api?method=basedata.student.getStudentByClassId&version=v1&classId='.$classId;
        $response = self::goGet($url);
        $response = json_decode($response,true);
        return $response['result'];
    }

    /**
     * 获取学校下面的所有老师
     * @param $schoolId
     * @return mixed
     */
    public static function api_getTeacherBySchoolId($schoolId,$pageNo=1,$pagetSize=100){
        $url = 'https://edu.nxeduyun.com/api?method=basedata.teacher.getTeacherBySchoolId&version=v1';
        $url .= '&pageNo='.$pageNo.'&pagetSize='.$pagetSize.'&schoolId='.$schoolId;
        $response = self::goGet($url);
        $response = json_decode($response,true);
        return $response['result'];
    }

    /**
     * 获取用户的详细信息
     * @param $userId
     * @return mixed
     */
    public static function api_getUserAndIdentity($userId){
        $url = 'https://edu.nxeduyun.com/api?method=basedata.user.getUserAndIdentity&version=v1&userId='.$userId;
        $response = self::goGet($url);
        $response = json_decode($response,true);
        return $response['result'];
    }

    /**
     * 获取学校详情
     * @param $schoolId
     * @return mixed
     */

    public static function api_getSchoolById($schoolId){
        $url = 'https://edu.nxeduyun.com/api?method=basedata.school.getSchoolById&version=v1&schoolId='.$schoolId;
        $response = self::goGet($url);
        $response = json_decode($response,true);

        return $response['result'];
    }

    /**
     * 获取学校科目
     * @param $schoolId
     * @return void
     */
    public static function api_getCourseBySchoolId($schoolId){
        $url = 'https://edu.nxeduyun.com/api?method=basedata.course.getCourseBySchoolId&version=v1&schoolId='.$schoolId;
        $response = self::goGet($url);
        $response = json_decode($response,true);
        return $response['result'];
    }

    public static function api_getClassBySchoolId($schoolId,$pageNo=1,$pageSize=100){
        $url = 'https://edu.nxeduyun.com/api?method=basedata.class.getClassBySchoolId&version=v2&schoolId='.$schoolId;
        $url .= '&pageNo='.$pageNo.'&pageSize='.$pageSize;
        $response = self::goGet($url);
        $response = json_decode($response,true);
        return $response['result'];
    }


    /**
     * 获取授权的地区列表
     * @return mixed
     */
    public static function api_getAuthAreaList(){
        $url = 'https://edu.nxeduyun.com/api?method=basedata.area.getAuthAreaList&version=v1';
        $response = self::goGet($url);
        $response = json_decode($response,true);
        return $response['result'];
    }

    /**
     * 获取授权地区下的学校列表
     * @param $areacode
     * @return mixed
     */
    public static function api_getSchoolList($areaCode='640100',$pageNo=1,$pagetSize=100){
        //[{"areaCode":640110,"areaName":"银川直属","parentId":640100}]
        $url = 'https://edu.nxeduyun.com/api?method=basedata.school.getSchoolList&version=v1&areaCode='.$areaCode.'&pageNo=1&pagetSize=100';
        $response = self::goGet($url);
        $response = json_decode($response,true);
        return $response['result'];
    }

    /**
     * 获取平台地区信息
     * @return mixed
     */
    public static function api_getAreaList(){
        $url = 'https://edu.nxeduyun.com/api?method=basedata.area.getAreaList&version=v1&pageNo=1&pageSize=100';
        $response = self::goGet($url);
        $response = json_decode($response,true);
        return $response['result'];
    }


    /**  * * * * * * * * * * * *
     *                         *
     *         通用方法区        * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                         *
     * * * * * * * * * * * * * */

    /**
     * 统一请求post
     * @param $url
     * @param $post_data
     * @return bool|string
     */
    private static function goPost($url, $post_data,$headers)
    {
        if(!$headers) {
            $headers[] = 'Content-Type:application/x-www-form-urlencoded';
        }
        $post_data = http_build_query($post_data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        $return = curl_exec($curl);
        curl_close($curl);
        return $return;
    }

    /**
     * 统一请求get
     * @param $url
     * @param $headers
     * @return bool|string
     */
    private static function goGet($url,$headers)
    {
        $has_auth = 0;
        foreach ($headers as $k => $v) {
            if(strpos('--'.$v, 'Authorization')){
                $has_auth = 1;
            }
        }
        if($has_auth==0) {
            $headers[] = self::api_authorization();
        }

        $curl  =  curl_init () ;
        curl_setopt ($curl , CURLOPT_URL ,  $url) ;
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt ( $curl , CURLOPT_RETURNTRANSFER ,  true ) ;
        $return  =  curl_exec ( $curl ) ;
        curl_close ( $curl ) ;
        return $return;
    }

    /**
     * 根据clientId和clientSecret获取令牌信息
     * @return mixed
     */
    public static function api_authorization(){
        if($_COOKIE[self::$cookie_pre.'token_type'] && $_COOKIE[self::$cookie_pre.'access_token']){
            $response['token_type'] = $_COOKIE[self::$cookie_pre.'token_type'];
            $response['access_token'] = $_COOKIE[self::$cookie_pre.'access_token'];
        }else{
            $url = 'https://auth2.nxeduyun.com/oauth2/v1/connect/token';
            $post_data = array(
                'grant_type'=>'client_credentials',
                'client_id'=>self::$appkey,
                'client_secret'=>self::$appsecret
            );
            $response = self::goPost($url,$post_data);
            $response = json_decode($response,true);
            setcookie(self::$cookie_pre.'access_token',$response['access_token'],time()+7000);
            setcookie(self::$cookie_pre.'token_type',$response['token_type'],time()+7000);
        }
        $Authorization = 'Authorization:'.$response['token_type'].' '.$response['access_token'];
        return $Authorization;
    }

    /**  * * * * * * * * * * * *
     *                         *
     *         数据上报区        * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                         *
     * * * * * * * * * * * * * */

    /**
     * 数据上报
     * @param $api
     * @param $data
     * @param $version
     * @return bool|string
     */
    public static function report_data_api($api,$data,$version='v1'){
        $report['count'] = (int) count($data);
        $report['dataContent'] = $data;
        $report['seqNO'] = (string) 'BOZEDU'.date('YmdHis');
        $headers[] = self::api_authorization();
        $url = "https://edu.nxeduyun.com/api?method=".$api."&version=".$version;
        $response = self::goPost_json($url,$report,$headers);
        return $response;
    }

    /**
     * 数据上报专用的post
     * @param $url
     * @param $post_data
     * @param $headers
     * @return bool|string
     */
    private static function goPost_json($url, $post_data,$headers)
    {
        $post_data = json_encode($post_data,JSON_UNESCAPED_UNICODE);

        $headers[] = 'Content-Type: application/json;charset=utf-8';
        $headers[] = 'Content-Length: '.strlen($post_data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        $return = curl_exec($curl);
        curl_close($curl);
        return $return;
    }


    /**  * * * * * * * * * * * *
     *                         *
     *         基础数据区        * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                         *
     * * * * * * * * * * * * * */

    /**
     * 通用数据转化：地区
     * 宁夏地区ID -> 用户中心地区ID
     * （部分地区有缺失）
     * @param $areaCode
     * @return array|mixed
     */
    public static function base_areacode_ex($areaCode){
        $area[640000] = array (
            'areaCode' => 640000,
            'areaName' => '宁夏回族自治区',
            'parentId' => 1,
            'ebag_id'=>30
        );
        $area[640100] = array (
            'areaCode' => 640100,
            'areaName' => '银川市',
            'parentId' => 640000,
            'ebag_id'=>470
        );
        $area[640101] = array (
            'areaCode' => 640101,
            'areaName' => '市辖区',
            'parentId' => 640100,
            'ebag_id'=>0
        );
        $area[640104] = array (
            'areaCode' => 640104,
            'areaName' => '兴庆区',
            'parentId' => 640100,
            'ebag_id'=>4906
        );
        $area[640105] = array (
            'areaCode' => 640105,
            'areaName' => '西夏区',
            'parentId' => 640100,
            'ebag_id'=>4909
        );
        $area[640106] = array (
            'areaCode' => 640106,
            'areaName' => '金凤区',
            'parentId' => 640100,
            'ebag_id'=>4911
        );
        $area[640109] = array (
            'areaCode' => 640109,
            'areaName' => '固原直属',
            'parentId' => 640400,
            'ebag_id'=>0
        );
        $area[640110] = array (
            'areaCode' => 640110,
            'areaName' => '银川直属',
            'parentId' => 640100,
            'ebag_id'=>0
        );
        $area[640111] = array (
            'areaCode' => 640111,
            'areaName' => '石嘴山直属',
            'parentId' => 640200,
            'ebag_id'=>0
        );
        $area[640113] = array (
            'areaCode' => 640113,
            'areaName' => '吴忠直属',
            'parentId' => 640300,
            'ebag_id'=>0
        );
        $area[640117] = array (
            'areaCode' => 640117,
            'areaName' => '宁东能源化工基地',
            'parentId' => 640100,
            'ebag_id'=>0
        );
        $area[640121] = array (
            'areaCode' => 640121,
            'areaName' => '永宁县',
            'parentId' => 640100,
            'ebag_id'=>4907
        );
        $area[640122] = array (
            'areaCode' => 640122,
            'areaName' => '贺兰县',
            'parentId' => 640100,
            'ebag_id'=>4910
        );
        $area[640181] = array (
            'areaCode' => 640181,
            'areaName' => '灵武市',
            'parentId' => 640100,
            'ebag_id'=>4908
        );
        $area[640199] = array (
            'areaCode' => 640199,
            'areaName' => '闽宁镇',
            'parentId' => 640100,
            'ebag_id'=>0
        );
        $area[640200] = array (
            'areaCode' => 640200,
            'areaName' => '石嘴山市',
            'parentId' => 640000,
            'ebag_id'=>471
        );
        $area[640201] = array (
            'areaCode' => 640201,
            'areaName' => '市辖区',
            'parentId' => 640200,
            'ebag_id'=>0
        );
        $area[640202] = array (
            'areaCode' => 640202,
            'areaName' => '大武口区',
            'parentId' => 640200,
            'ebag_id'=>4912
        );
        $area[640205] = array (
            'areaCode' => 640205,
            'areaName' => '惠农区',
            'parentId' => 640200,
            'ebag_id'=>4914
        );
        $area[640221] = array (
            'areaCode' => 640221,
            'areaName' => '平罗县',
            'parentId' => 640200,
            'ebag_id'=>4913
        );
        $area[640300] = array (
            'areaCode' => 640300,
            'areaName' => '吴忠市',
            'parentId' => 640000,
            'ebag_id'=>472
        );
        $area[640301] = array (
            'areaCode' => 640301,
            'areaName' => '市辖区',
            'parentId' => 640300,
            'ebag_id'=>0
        );
        $area[640302] = array (
            'areaCode' => 640302,
            'areaName' => '利通区',
            'parentId' => 640300,
            'ebag_id'=>4915
        );
        $area[640303] = array (
            'areaCode' => 640303,
            'areaName' => '红寺堡区',
            'parentId' => 640300,
            'ebag_id'=>0
        );
        $area[640323] = array (
            'areaCode' => 640323,
            'areaName' => '盐池县',
            'parentId' => 640300,
            'ebag_id'=>4917
        );
        $area[640324] = array (
            'areaCode' => 640324,
            'areaName' => '同心县',
            'parentId' => 640300,
            'ebag_id'=>4916
        );
        $area[640381] = array (
            'areaCode' => 640381,
            'areaName' => '青铜峡市',
            'parentId' => 640300,
            'ebag_id'=>4918
        );
        $area[640400] = array (
            'areaCode' => 640400,
            'areaName' => '固原市',
            'parentId' => 640000,
            'ebag_id'=>473
        );
        $area[640401] = array (
            'areaCode' => 640401,
            'areaName' => '市辖区',
            'parentId' => 640400,
            'ebag_id'=>0
        );
        $area[640402] = array (
            'areaCode' => 640402,
            'areaName' => '原州区',
            'parentId' => 640400,
            'ebag_id'=>4919
        );
        $area[640422] = array (
            'areaCode' => 640422,
            'areaName' => '西吉县',
            'parentId' => 640400,
            'ebag_id'=>4922
        );
        $area[640423] = array (
            'areaCode' => 640423,
            'areaName' => '隆德县',
            'parentId' => 640400,
            'ebag_id'=>4923
        );
        $area[640424] = array (
            'areaCode' => 640424,
            'areaName' => '泾源县',
            'parentId' => 640400,
            'ebag_id'=>4921
        );
        $area[640425] = array (
            'areaCode' => 640425,
            'areaName' => '彭阳县',
            'parentId' => 640400,
            'ebag_id'=>4920
        );
        $area[640500] = array (
            'areaCode' => 640500,
            'areaName' => '中卫市',
            'parentId' => 640000,
            'ebag_id'=>474
        );
        $area[640501] = array (
            'areaCode' => 640501,
            'areaName' => '市辖区',
            'parentId' => 640500,
            'ebag_id'=>0
        );
        $area[640502] = array (
            'areaCode' => 640502,
            'areaName' => '沙坡头区',
            'parentId' => 640500,
            'ebag_id'=>4925
        );
        $area[640510] = array (
            'areaCode' => 640510,
            'areaName' => '中卫市直属',
            'parentId' => 640500,
            'ebag_id'=>0
        );
        $area[640521] = array (
            'areaCode' => 640521,
            'areaName' => '中宁县',
            'parentId' => 640500,
            'ebag_id'=>4924
        );
        $area[640522] = array (
            'areaCode' => 640522,
            'areaName' => '海原县',
            'parentId' => 640500,
            'ebag_id'=>4926
        );
        $area[641100] = array (
            'areaCode' => 641100,
            'areaName' => '省厅直属',
            'parentId' => 640000,
            'ebag_id'=>0
        );
        $area[641110] = array (
            'areaCode' => 641110,
            'areaName' => '省厅直属',
            'parentId' => 641100,
            'ebag_id'=>0
        );

        return $area[$areaCode];
    }

    /**
     * 通用数据转化：学段
     * 宁夏学段 -> 用户中心学段年级数据
     * @param $xueduan_code
     * @return array|int[]|mixed
     */
    public static function base_xueduan($xueduan_code){
        $xueduan['xxjd_youeryuan'] = array();
        $xueduan['xxjd_xiaoxue'] = array(1,2,3,4,5,6);
        $xueduan['xxjd_chuzhong'] = array(7,8,9);
        $xueduan['xxjd_gaozhong'] = array(11,12,13);
        $xueduan['xxjd_zhigao'] = array(17,18,19,21);
        $xueduan['xxjd_jigong'] = array(17,18,19,21);

        return $xueduan[$xueduan_code] ?: array();
    }

    /**
     * 通用数据转化：年级
     * 宁夏年级->用户中心年级ID
     * @param $grade_code
     * @return int|mixed
     */
    public static function base_grade($grade_code){
        $grade['grade_1'] = 1;
        $grade['grade_2'] = 2;
        $grade['grade_3'] = 3;
        $grade['grade_4'] = 4;
        $grade['grade_5'] = 5;
        $grade['grade_6'] = 6;
        $grade['grade_7'] = 7;
        $grade['grade_8'] = 8;
        $grade['grade_9'] = 9;
        $grade['grade_10'] = 11;
        $grade['grade_11'] = 12;
        $grade['grade_12'] = 13;
        $grade['grade_31'] = 17;
        $grade['grade_32'] = 18;
        $grade['grade_33'] = 19;
        $grade['grade_41'] = 1;
        $grade['grade_42'] = 1;
        $grade['grade_43'] = 1;
        $grade['grade_44'] = 1;

        return $grade[$grade_code];
    }

    /**
     * 通用数据转化：学科
     * 宁夏学科->用户中心学科ID
     * @param $subejct_code
     * @return array|mixed|string
     */
    public static function base_subject($subejct_code){
        $subject['jcsub01'] = array('name'=>'语文','ebag_id'=>1);
        $subject['jcsub02'] = array('name'=>'数学','ebag_id'=>2);
        $subject['jcsub03'] = array('name'=>'英语','ebag_id'=>3);
        $subject['jcsub08'] = array('name'=>'思想品德','ebag_id'=>17);
        $subject['jcsub10'] = array('name'=>'历史与社会','ebag_id'=>22);
        $subject['jcsub11'] = array('name'=>'历史','ebag_id'=>8);
        $subject['jcsub12'] = array('name'=>'地理','ebag_id'=>7);
        $subject['jcsub13'] = array('name'=>'物理','ebag_id'=>5);
        $subject['jcsub14'] = array('name'=>'化学','ebag_id'=>9);
        $subject['jcsub15'] = array('name'=>'生物','ebag_id'=>6);
        $subject['jcsub16'] = array('name'=>'科学','ebag_id'=>21);
        $subject['jcsub17'] = array('name'=>'艺术','ebag_id'=>30);
        $subject['jcsub18'] = array('name'=>'美术','ebag_id'=>19);
        $subject['jcsub19'] = array('name'=>'体育与健康','ebag_id'=>14);
        $subject['jcsub20'] = array('name'=>'音乐','ebag_id'=>18);
        $subject['jcsub21'] = array('name'=>'信息技术','ebag_id'=>20);

        return $subject[$subejct_code] ?: '';
    }

}