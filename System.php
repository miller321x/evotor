<?php

/**
 * Created by PhpStorm.
 * User: 1
 * Date: 31.03.17
 * Time: 19:44
 */
class System
{


    /** SQL */





    /** WORK WITH REQUEST */

    public static function getRequest() {


        if($_SERVER["REQUEST_METHOD"] == 'GET') {

            $object = (object) $_REQUEST;
            return $object;

        } else {

            $data = JSON::get();

            if(isset($data->data)) {

                return $data->data;

            } else {

                return $data;

            }

        }

    }

    public static function dateFormat($date,$App) {
        $date = explode('-',$date);

        $months = [];

        $months['09'] = Ui::lang($App,'SEPTEMBER');
        $months['10'] = Ui::lang($App,'OCTOBER');
        $months['11'] = Ui::lang($App,'NOVEMBER');
        $months['12'] = Ui::lang($App,'DECEMBER');
        $months['01'] = Ui::lang($App,'JANUARY');
        $months['02'] = Ui::lang($App,'FEBRUARY');
        $months['03'] = Ui::lang($App,'MART');
        $months['04'] = Ui::lang($App,'APRIL');
        $months['05'] = Ui::lang($App,'MAY');
        $months['06'] = Ui::lang($App,'JUNE');
        $months['07'] = Ui::lang($App,'JULY');
        $months['08'] = Ui::lang($App,'AUGUST');

        if($date[1] == '00') {
            return Ui::lang($App,'DATE_UNDEFINED');
        } else {
            return $date[2].' '.$months[$date[1]];
        }

    }


    /** WORK WITH Params */

    public static function setParams($params, $Controller) {

        $data = [];

        $data['status'] = isset($params->status) ? $params->status : 1;
        $data['search'] = isset($params->search) ? $params->search : null;
        $data['dep_id'] = isset($params->dep_id) ? $params->dep_id : null;
        $data['mode'] = isset($params->mode) ? $params->mode : null;
        $data['team_id'] = isset($params->team_id) ? $params->team_id : null;
        $data['ids'] = isset($params->ids) ? str_ireplace('_', ',', $params->ids) : null;
        $data['order'] = isset($params->order) ? $params->order : 'DESC';
        $data['sort'] = isset($params->sort) ? $params->sort : 'id';
        $data['limit'] = isset($params->limit) ? $params->limit : $Controller->limit;
        $data['page'] = isset($params->page) ? $params->page : 1;
        $data['offset'] = ($data['page'] - 1) * $data['limit'];

        return $data;
    }


    /** WORK WITH CURL */
    public static function curl($url, $post = null) {

        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        if($post) {

            curl_setopt($ch, CURLOPT_POST, 1);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        }


        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);

        return $output;
    }


    /** WORK WITH STRING */

    public static function strSpecialClear($value = "") {

        $value = trim($value);
        $value = stripslashes($value);
        $value = strip_tags($value);
        $value = htmlspecialchars($value);
        $value = html_entity_decode($value);

        $value = str_ireplace('&nbsp;',' ',$value);

        return $value;
    }

    public static function intLength($val,$len) {

        if($val == '') {
            return 0;
        }
        if(!$val) {
            return 0;
        }
        if($val < 0) {
            return 0;
        }

        if(mb_strlen(''.$val,'UTF-8') > $len) {

            $val = mb_substr(''.$val,0,$len,'UTF-8');

        }

        return intval($val);

    }

    public static function strLength($val,$len) {



        if(mb_strlen($val,'UTF-8') > $len) {

            $val = mb_substr(''.$val,0,$len,'UTF-8');

        }

        return $val;

    }


    /** WORK WITH GENERATING TOKENS */

    public static function genID() {

        $id = self::getToken(16);

        return $id;
    }

    public static function dirSection() {


        $files = self::listing(UPLOADS_PATH,1);
        $dir = $files[count($files) - 1];
        $files = self::listing(UPLOADS_PATH.'/'.$dir,1);

        if(count($files) > 32000) {
            self::mkdirs(UPLOADS_PATH.'/'.($dir + 1).'/',0755);
            $dir = $dir + 1;
        }
        //return $dir;
        return 1;

    }



    public static function genSession() {

        $session = self::getToken(32);

        return $session;
    }

    public static function genHash() {

        $hash = self::getToken(64);

        return $hash;

    }

    protected static function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }

    protected static function getToken($length=32){
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        for($i=0;$i<$length;$i++){
            $token .= $codeAlphabet[self::crypto_rand_secure(0,strlen($codeAlphabet))];
        }
        return $token;
    }





    /** WORK WITH DATE */



    public static function convertDate($date,$mode) {

        return $date;

    }

    /**
     * @return false|string
     */
    public static function toDay($mode = 'default') {

        if($mode == 'default') {
            return date("Y-m-d H:i:s");
        }
        if($mode == 'timestamp') {
            return strtotime("now");
        }
        if($mode == 'date') {
            return date("Y-m-d");
        }

    }

    public static function stamp($time = null) {

        $res = [];
        if($time) {
            $date = new DateTime($time);
            $res['start'] = $date->getTimestamp();
        }


        $date = new DateTime();
        $res['now'] = $date->getTimestamp();

        return $res;
    }

    /** WORK WITH PASSWORD */

    /**
     * @param $pass
     * @return bool|string
     */
    public static function securePass($pass) {

        $pass = self::strSpecialClear($pass);
        /*
        $options = [
            'cost' => 11,
            'salt' => 'H02h-a24Jb.*we!23-j2g84vNg00.2h4Yt988723es32',
        ];
        */
        return password_hash($pass, PASSWORD_DEFAULT);

    }



    /** WORK WITH USER FIELDS */


    /**
     * @param $data
     * @param $type
     * @return string
     */
    public static function phoneEmail($data,$type) {

        if($type == 'email') {
            if(mb_strpos($data,'@') !== false) {
                return $data;
            }

        }
        else {
            if(mb_strpos($data,'@') === false) {
                return $data;
            }
        }

        return '';

    }

    /**
     * For unicode word ending
     * @param $count
     * @param $data
     * @return mixed
     */

    static function getEnding($count,$data)
    {
        $data = explode(':',$data);
        $len = strlen($count);
        $f = 0;
        if ($len >= 2)
            $f = substr ($count, $len-2, $len-1);
        $s =  substr ($count, $len-1);
        switch($s)
        {

            case "1": return $data[0];

            case "2":
            case "3":
            case "4":
                if ($f == 1)
                    return $data[2];
                else
                    return $data[1];
            default: return $data[2];

        }
    }

    /**
     * Check mobile
     * @return int|null
     */
    static function isMobile() {

        require_once 'library/includes/Mobile-Detect-master/Mobile_Detect.php';
        $detect = new Mobile_Detect;

        $contr = 0;
        if ( $detect->isMobile() ) {
            $contr = 1;
        }

        if( $detect->isTablet() ){
            $contr = 0;
        }

        if($contr == 1) {
            return 1;
        } else {
            return null;
        }

    }


    static function newFile($name,$data = null) {

        $file = fopen($name, "w");

        if(!$data) {
            $data = "";
        }

        fwrite($file, $data);
        fclose($file);

    }


    static function writeFile($data,$name,$m) {

        $file = fopen($name, $m);
        fwrite($file, $data);
        fclose($file);

    }

    static function readFile($name) {

        $file = fopen($name, 'r');
        $data = fread($file,filesize($name));
        fclose($file);

        return $data;

    }



    /** WORK WITH IMAGES AND FILES */


    public static function getFileSize($file) {


            $path = BASE_PATH.'/public/'.$file;

            $filesize = filesize($path);



            if($filesize > 1024){
                $filesize = ($filesize/1024);
            if($filesize > 1024){

                $filesize = ($filesize/1024);
                if($filesize > 1024) {
                    $filesize = ($filesize/1024);
                    $filesize = round($filesize, 1);
                    return $filesize." GB";
                } else {
                    $filesize = round($filesize, 1);
                    return $filesize." MB";
                }
            } else {
                $filesize = round($filesize, 1);
                return $filesize." KB";
            }
        } else {
            $filesize = round($filesize, 1);
            return $filesize." B";
        }




    }


    /** Create directory
     * @param $dir
     * @param int $mode
     * @return bool
     */
    public static function mkdirs($dir, $mode=0755)
    {
        if (empty($dir)) {
            return false;
        }

        if (is_dir($dir) || '/' === $dir) {
            return true;
        }

        if (self::mkdirs(dirname($dir), $mode)) {
            $is = mkdir($dir, $mode);
            chmod($dir, $mode);
            return $is;
        }

        return false;
    }


    /**
     * @param $dir
     */
    public static function removeDirRec($dir)
    {
        if ($objs = glob($dir."/*")) {
            foreach($objs as $obj) {
                is_dir($obj) ? self::removeDirRec($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }

    /** Get list files or folders
     * @param $url
     * @param $mode
     * @return array
     */
    public static function listing ($url,$mode) {

        $folders = [];
        $files = [];

        if (is_dir($url)) {

            if ($dir = opendir($url)) {

                while ($file = readdir($dir)) {

                    if ($file != "." && $file != "..") {


                        if(@is_dir($url."/".$file)) {
                            $folders[] = $file;
                        }

                        else {$files[] = $file;}
                    }
                }
            }

            closedir($dir);
        }

        if($mode == 1) {return $folders;}

        if($mode == 0) {return $files;}

    }


    /**
     * Converts bytes into human readable file size.
     *
     * @param string $bytes
     * @return string human readable file size (2,87 Мб)
     * @author Mogilev Arseny
     */
    public static function FileSizeConvert($bytes)
    {
        $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

        foreach($arBytes as $arItem)
        {
            if($bytes >= $arItem["VALUE"])
            {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
                break;
            }
        }
        return $result;
    }



    /** Video frame */

    public static function getVideoPreview($frame,$movie,$thumbnail) {


        $ffmpeg = FFMpeg\FFMpeg::create(array(
            'ffmpeg.binaries'  => ['/usr/bin/ffmpeg'],
            'ffprobe.binaries' => ['/usr/bin/ffprobe'],
            'timeout'          => 3600, // The timeout for the underlying process
            'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
        ));



        $video = $ffmpeg->open($movie);
        $video
            ->filters()
            ->resize(new FFMpeg\Coordinate\Dimension(320, 240))
            ->synchronize();
        $video
            ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds($frame))
            ->save($thumbnail);




        return $thumbnail;


    }


    /** Upload Image */

    public static function getImageUrl($url,$size = null) {

        $default = '';

        if($url != '') {
            //return $url;


            if($size) {

                $url = str_ireplace('.','_'.$size.'x'.$size.'.',$url);
            }

            $path = self::basicDomain().$url;

            return $path;
        }

        return $default;
    }
    public static function basicDomain() {

        return BASIC_DOMAIN;

    }

    public static function validFormat($format,$type = null) {

        $format = strtolower($format);

        if($type == 'doc') {

            $ext = explode(',','pdf,doc,rtf,docx,txt,xls,ppt,pptm,pptx,jpg,jpeg,png,gif');

        } else {

            $ext = explode(',','jpg,jpeg,png,gif');
        }


        for($i = 0; $i < count($ext); $i++) {

            if($format == $ext[$i]) {
                return true;
            }

        }


        return false;

    }


    public static function uploadFile($path,$App,$type,$name = null) {

        if($name) {

            $ext = explode('.',$name);

            $format = $ext[1];

        } else {

            $format = self::base64GetFormat($path);
        }


        if(self::validFormat($format,$type)) {

            if($name) {

                $file = $name;

            } else {

                $file = self::genID().'.'.$format;
            }


            $output_file = self::buildPath('file', $App->userProfile, $file);

            self::base64ToFile($path, $output_file);

            $path = self::buildPath('file_local', $App->userProfile, $file);


            return $path;


        } else {


            $result = [

                "error"   => ["message" =>  "invalid format", "code" => Errors::getCode('invalid format')],

            ];


            echo JSON::encode($result);

            exit();

        }

    }


    public static function uploadImage($image,$App,$type = null,$size = null) {

        $format = self::base64GetFormat($image);

        if(self::validFormat($format)) {


            $img = self::genID().'.'.$format;

            if($type) {


                if($type == 'user_photo_upload') {

                    $output_file = self::buildPath('user_photo_upload', $App->userProfile, $img);

                    self::base64ToFile($image, $output_file);
                    $path = self::buildPath('user_photo_upload_local', $App->userProfile, $img);

                } else {

                    $new_name = $img;
                    if($size) {
                        $new_name = str_ireplace('.','_'.$size.'x'.$size.'.',$img);
                    }
                    $output_file = self::buildPath('media', $App->userProfile, $new_name);
                    self::base64ToFile($image, $output_file);
                    $path = self::buildPath('media_local', $App->userProfile, $img);
                }



            } else {
                $output_file = self::buildPath('new', $App->userProfile, $img);
                self::base64ToFile($image, $output_file);
                $path = self::buildPath('path', $App->userProfile, $img);
            }




            return $path;


        } else {


            $result = [

                "error"   => ["message" =>  "invalid format", "code" => Errors::getCode('invalid format')],

            ];


            echo JSON::encode($result);

            exit();

        }

    }

    /** Get Format Image Base 64 */

    public static function base64GetFormat($base64_string) {

        $data = explode(',', $base64_string);
        $data = explode(';', $data[0]);
        $data = explode('/', $data[0]);

        return $data[1];

    }

    /** SET Image Base 64 */

    public static function base64ToFile($base64_string, $output_file) {



            if(fopen($output_file, "wb")) {

                $ifp = fopen($output_file, "wb");

                $data = explode(',', $base64_string);

                fwrite($ifp, base64_decode($data[1]));

                fclose($ifp);

                return $output_file;


            } else {

               $error = [];

               $error['error'] = $output_file.' file not uploaded';

               echo JSON::encode($error);

               exit();

            }




    }



    /** Resizing image
     * @param $image
     * @param $outfile
     * @param $newWidth
     * @param $newHeight
     * @return bool
     */
    public static function RatioResizeImg( $image, $outfile, $newWidth, $newHeight) {

        // open file
        $regs = getimagesize($image);
        $srcWidth = $regs[0];
        $srcHeight = $regs[1];
        if ((!$srcWidth) or (!$srcHeight))
            return false;

        switch($regs[2]){
//1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(intel byte order), 8 = TIFF(motorola byte order), 9 = JPC, 10 = JP2,
//11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF.
            case 1: $srcImage = ImageCreateFromGIF( $image ); break;
            case 2: $srcImage = ImageCreateFromJPEG( $image ); break;
            case 3: $srcImage = ImageCreateFromPNG( $image ); break;
            default: $srcImage = ImageCreateFromGIF( $image ); break;
        }
        //the following code more than a height or height checks
        //if width more than a width of the picture so that in case of change the correct proportion remained
        $ratioWidth = $srcWidth/$newWidth;
        $ratioHeight = $srcHeight/$newHeight;



        $destWidth = $newWidth;
        $destHeight = $srcHeight/$ratioWidth;

        // create new image
        $destImage = imagecreatetruecolor( $destWidth, $destHeight);

        imagealphablending($destImage, false);
        imagesavealpha($destImage,true);
        $transparencyIndex = imagecolortransparent($srcImage);
        if ($transparencyIndex >= 0)
        {
            $transparencyColor = imagecolorsforindex($srcImage, $transparencyIndex);
            $transparencyIndex = imagecolorallocatealpha( $destImage, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue'], $transparencyColor['alpha'] );
            imagefill($destImage, 0, 0, $transparencyIndex);
            imagecolortransparent($destImage, $transparencyIndex);
        }

        // copy srcImage (source) to destImage (destination)
        imagecopyresampled( $destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight );
//     imagecopyresized( $destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight );
        $ext = substr($outfile, strrpos($outfile, '.') + 1);
        switch($ext){
            case "gif": imagegif( $destImage,$outfile); break;
            case "jpg": imagejpeg( $destImage,$outfile, 85); break;
            case "jpeg": imagejpeg( $destImage,$outfile, 85); break;
            case "png": imagepng( $destImage,$outfile); break;
            default: imagegif( $destImage,$outfile); break;
        }
        imagealphablending($destImage, true);


        // clear memory
        ImageDestroy( $srcImage );
        ImageDestroy( $destImage );
        return true;
    }




    /**
     * @param $image
     * @param $outfile
     * @return bool
     */
    public static function RenameImg( $image, $outfile) {
        // open file
        $regs = getimagesize($image);
        $srcWidth = $regs[0];
        $srcHeight = $regs[1];
        if ((!$srcWidth) or (!$srcHeight))
            return false;

        switch($regs[2]){
//1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(intel byte order), 8 = TIFF(motorola byte order), 9 = JPC, 10 = JP2,
//11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF.
            case 1: $srcImage = ImageCreateFromGIF( $image ); break;
            case 2: $srcImage = ImageCreateFromJPEG( $image ); break;
            case 3: $srcImage = ImageCreateFromPNG( $image ); break;
            default: $srcImage = ImageCreateFromGIF( $image ); break;
        }
        //the following code more than a height or height checks
        //if width more than a width of the picture so that in case of change the correct proportion remained
        $destWidth = $srcWidth;
        $destHeight = $srcHeight;
        // create new image
        $destImage = imagecreatetruecolor( $destWidth, $destHeight);

        imagealphablending($destImage, false);
        imagesavealpha($destImage,true);
        $transparencyIndex = imagecolortransparent($srcImage);
        if ($transparencyIndex >= 0)
        {
            $transparencyColor = imagecolorsforindex($srcImage, $transparencyIndex);
            $transparencyIndex = imagecolorallocatealpha( $destImage, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue'], $transparencyColor['alpha'] );
            imagefill($destImage, 0, 0, $transparencyIndex);
            imagecolortransparent($destImage, $transparencyIndex);
        }
        // copy srcImage (source) to destImage (destination)
//     imagecopyresampled( $destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight );
        imagecopy( $destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight/*, $srcWidth, $srcHeight */);
        $ext = substr($outfile, strrpos($outfile, '.') + 1);
        switch($ext){
            case "gif": imagegif( $destImage,$outfile); break;
            case "jpg": imagejpeg( $destImage,$outfile, 100); break;
            case "jpeg": imagejpeg( $destImage,$outfile, 100); break;
            case "png": imagepng( $destImage,$outfile); break;
            default: imagegif( $destImage,$outfile); break;
        }
        imagealphablending($destImage, true);


        // clear memory
        ImageDestroy( $srcImage );
        ImageDestroy( $destImage );
        unlink($image);
        return true;
    }





    /**
     * @param $url
     * @param $size
     * @param $muse
     * @param bool $replace
     * @param bool $resize
     * @return mixed|string
     */
    public static function get_image($url, $size, $type = true)
    {
        if($url != '')
        {

            if(file_exists($url))
            {
                $sourse = $url;


                if($type) {

                    $url = str_ireplace('.jpg','_x'.$size.'.jpg',$url);
                    $url = str_ireplace('.jpeg','_x'.$size.'.jpg',$url);
                    $url = str_ireplace('.png','_x'.$size.'.png',$url);
                    $url = str_ireplace('.gif','_x'.$size.'.gif',$url);
                }



                    $res = self::create_new_image($sourse,$url,$size,'');

                    if($res == false)
                    {
                        if(file_exists($url))
                        {
                            return $url;
                        }

                    }
                    else
                    {
                        return $url;
                    }

            }

        }
        else
        {
            return $url;
        }
    }

    /**
     * @param $url
     * @param $size
     * @param $muse
     * @param bool $replace
     * @param bool $resize
     * @return mixed
     */
    public static function basic_resize($url, $size, $muse, $replace = false, $resize = false)
    {
        if($url != '')
        {

            $sourse = $url;

            $res = self::create_new_image($sourse,$url,$size,'');

            return $url;


        }


    }


    /**
     * @param $sourse
     * @param $url
     * @param $size
     * @param $attr
     * @return string
     */

    function create_new_image($sourse,$url,$size,$attr)
    {

        copy($sourse, $url);


        $res = '';
        $img_path = $url;

        $img_path_format = substr($img_path,-4);
        $fileName = $img_path;
        if($img_path_format == '.png')
        {
            $res = self::save_image($_SERVER['DOCUMENT_ROOT'].'/'.$fileName.'~'.$attr,'png',$size);
        }
        if($img_path_format == '.jpg')
        {
            $res = self::save_image($_SERVER['DOCUMENT_ROOT'].'/'.$fileName.'~'.$attr,'jpg',$size);
        }
        if($img_path_format == '.gif')
        {
            $res = self::save_image($_SERVER['DOCUMENT_ROOT'].'/'.$fileName.'~'.$attr,'gif',$size);
        }
        return $res;

    }


    /**
     * @param $fileName
     * @param $mode
     * @param $nWidth
     * @return bool
     */
    public static function save_image($fileName,$mode,$nWidth) {
        global $param;

        $pos = strpos($fileName,'~');

        if($pos !== false)
        {
            $path = explode('~',$fileName);
            if($path[1] != '')
            {
                if($path[1] != '0,0,0,0')
                {
                    $param = $path[1];
                }
                else
                {
                    $param = 'none';
                }
            }
            else
            {
                $param = 'none';
            }
            $fileName = $path[0];
        }

        if(file_exists($fileName))
        {
            if($param)
            {
                if($param == 'load')
                {
                    unset($param);
                }
            }
            if(isset($param))
            {
                if($param != 'none')
                {
                    $wh_list = explode(',',$param);
                }
            }
            if($mode == 'png')
            {
                $image = $fileName;
                $img = imagecreatefrompng($image);

                if(!isset($param))
                {
                    $x2 = $nWidth;
                    $Width = imagesx($img);
                    $Height = imagesy($img);
                    $y2 = intval($x2*$Height/$Width);
                    $x1 = 0;
                    $y1 = 0;

                }
                else
                {
                    $x2 = $nWidth;
                    $y2 = $nWidth;
                    $x1 = 0;
                    $y1 = 0;
                }
                if(isset($param))
                {
                    if($param != 'none')
                    {
                        $nHeight = $nWidth;
                        if(isset($wh_list[0]))
                        {
                            $x = $wh_list[0];
                            $y = $wh_list[1];
                        }
                        else
                        {
                            $x = 0;
                            $y = 0;
                        }
                    }
                    else
                    {
                        $size = getimagesize($fileName);
                        $nHeight = $nWidth;
                        if($size[0] > $size[1])
                        {
                            $n = ($size[0] - $size[1]) / 2;
                            $x = $n;
                            $y = 0;
                        }
                        else
                        {
                            $n = ($size[1] - $size[0]) / 2;
                            $x = 0;
                            $y = $n;
                        }
                    }
                }
                if(isset($param))
                {
                    if($param != 'none')
                    {
                        if(isset($wh_list[2]))
                        {
                            $Width = $wh_list[2];
                            $Height = $wh_list[3];
                        }
                        else
                        {
                            $size = getimagesize($fileName);
                            $Width = $size[0];
                            $Height = $size[1];
                        }
                    }
                    else
                    {
                        $size = getimagesize($fileName);

                        if($size[0] > $size[1])
                        {
                            $Height = $size[1];
                            $Width = $Height;

                        }
                        else
                        {
                            $Width = $size[0];
                            $Height = $Width;
                        }
                    }
                }
                $thumb = imagecreatetruecolor($x2,$y2);

                imagesavealpha($thumb,true);
                imagefill($thumb,0,0,IMG_COLOR_TRANSPARENT);
                imagecopyresampled($thumb, $img, 0, 0, $x, $y, $x2,$y2, $Width, $Height);
                unlink($fileName);
                imagepng($thumb,$fileName);
            }
            else
            {
                switch(substr($fileName,-4))
                {
                    case ".gif":
                        $Res=imagecreatefromgif($fileName);
                        $ext=($Res)?".gif":"";
                        break;
                    case ".jpg":
                        $Res=imagecreatefromjpeg($fileName);
                        $ext=($Res)?".jpg":"";
                        break;
                    default: return FALSE;
                }

                if($Res)
                {
                    if(isset($param))
                    {
                        if($param != 'none')
                        {
                            if(isset($wh_list[2]))
                            {
                                $Width = $wh_list[2];
                            }
                            else
                            {
                                $size = getimagesize($fileName);
                                $Width = $size[0];
                            }
                            if(isset($wh_list[3]))
                            {
                                $Height = $wh_list[3];
                            }
                            else
                            {
                                $size = getimagesize($fileName);
                                $Height =  $size[1];
                            }
                        }
                        else
                        {
                            $size = getimagesize($fileName);

                            if($size[0] > $size[1])
                            {
                                $Height = $size[1];
                                $Width = $Height;

                            }
                            else
                            {
                                $Width = $size[0];
                                $Height = $Width;
                            }
                        }
                    }
                    else
                    {
                        $Width=imagesx($Res);
                        $Height=imagesy($Res);
                    }
                }
                else
                    return FALSE;

                if($param)
                {
                    if($param != 'none')
                    {
                        $nHeight = $nWidth;
                        if(isset($wh_list[0]))
                        {
                            $x = $wh_list[0];
                        }
                        else
                        {
                            $x = 0;
                        }
                        if(isset($wh_list[1]))
                        {
                            $y = $wh_list[1];
                        }
                        else
                        {
                            $y = 0;
                        }
                    }
                    else
                    {

                        $nHeight = $nWidth;
                        if($size[0] > $size[1])
                        {
                            $n = ($size[0] - $size[1]) / 2;
                            $x = $n;
                            $y = 0;
                        }
                        else
                        {
                            $n = ($size[1] - $size[0]) / 2;
                            $x = 0;
                            $y = $n;
                        }
                    }
                }
                else
                {
                    $Koef=($nWidth/$Width);
                    $nHeight=(int)($Koef*$Height);
                    $x = 0;
                    $y = 0;
                }

                $nRes=imagecreatetruecolor($nWidth,$nHeight);

                if(!imagecopyresampled($nRes,$Res,0,0,$x,$y,$nWidth,$nHeight,$Width,$Height))
                    return FALSE;

                $Res = $nRes;
                unlink($fileName);

                switch($ext)
                {
                    case ".jpg":
                        if(!imagejpeg($Res,$fileName,100))
                            return FALSE;
                        break;
                    case ".gif":
                        if(!imagegif($Res,$fileName,100))
                            return FALSE;
                        break;
                    default : return FALSE;
                }
                return TRUE;
            }
        }
        else
        {
            return FALSE;
        }
    }


    /**
     * @param $type
     * @param $user
     * @return bool|string
     */

    public static function buildPath($type, $user, $name_img) {


        if(isset($user['dir_section'])) {

            $dir_section = $user['dir_section'];

            $system_id = $user['system_id'];

        } else {

            $dir_section = $user->dir_section;

            $system_id = $user->system_id;
        }

        if(isset($user['company_id'])) {

            $company_id = $user['company_id'];

        } else {

            $company_id = $user->company_id;

        }



        $path = false;

        switch ($type) {

            case 'media':



                $path = BASE_PATH.'/public/uploads/media/images/'.$name_img;


                break;

            case 'media_local':

                $path = 'uploads/media/images/'.$name_img;


                break;

            case 'new':

                $path = UPLOADS_PATH . '/'
                    . $dir_section . '/'
                    . $system_id
                    . '/user_content/'
                    . $name_img;


                break;

            case 'path':

                $path = UPLOADS_PATH_RELATIVE . '/'
                    . $dir_section . '/'
                    . $system_id
                    . '/user_content/'
                    . $name_img;


                break;


            case 'user_photo_upload':

                $path = BASE_PATH . '/public/uploads/usr/'
                    . $dir_section . '/'
                    . $system_id
                    . '/user_content/'
                    . $name_img;



                break;

            case 'file':


                $path = BASE_PATH . '/public/uploads/docs/'
                    . $company_id . '/'
                    . $name_img;

                if(!file_exists(BASE_PATH . '/public/uploads/docs/'
                    . $company_id)) {

                    System::mkdirs(BASE_PATH . '/public/uploads/docs/'
                        . $company_id.'/',0755);

                }


                break;

            case 'file_local':

                $path = 'uploads/docs/'
                    . $company_id . '/'
                    . $name_img;



                break;

            case 'user_photo_upload_local':

                $path = 'uploads/usr/'
                    . $dir_section . '/'
                    . $system_id
                    . '/user_content/'
                    . $name_img;



                break;

            case 'user_photo':

                $path = UPLOADS_PATH_RELATIVE . '/'
                    . $dir_section . '/'
                    . $system_id
                    . '/'
                    . $name_img;

                if(!file_exists($path)) {

                    $path = false;

                }

                break;

            case 'user_photo_min':

                $path = UPLOADS_PATH_RELATIVE . '/'
                    . $dir_section . '/'
                    . $system_id
                    . '/user_content/miniature/'
                    . $name_img;

                if(!file_exists($path)) {

                    $path = false;

                }

                break;

        }

        return $path;

    }


    /**
     * @param $name_img
     * @param $type
     * @param $id
     * @param int $size
     * @return bool|string
     */

    public static function pathToImage($name_img,$type,$id,$size = 120) {

        $path = false;

        switch ($type) {

            case 'user_photo':





                break;

            case 'user_photo_min':


                $name_img = str_ireplace('.','_x'.$size.'.',$name_img);

                if($name_img != '') {

                    $user = Users::findFirstById($id);

                    if ($user) {

                        $path = self::buildPath($type, $user, $name_img);


                    }

                }

                if($path) {

                    return $path;

                } else {

                    return '';

                }

                break;

        }

    }


}