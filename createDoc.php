<?php
/**
 * Created by PhpStorm.
 * User: miller321x
 * Date: 01.08.17
 * Time: 12:57
 */




$read = System::readFile(BASE_PATH.'/backend/config/router.php');


$data = explode('/**',$read);

$menu = [];




$case = '';

for($i = 1; $i <= count($data); $i++) {

    $comment = explode('*/',$data[$i]);
    $lines = explode('*',$comment[0]);

    $menu[$i] = [];
    $menu[$i]['title'] = $lines[1];


    $method = explode(' ',$lines[3]);

    $url = explode(' ',$lines[4]);


    $case .= '<a name="'.$i.'"></a><table id="anc_'.$i.'" style="border:1px solid #ddd;width:90%;margin:15px;margin-top:60px">';

    $case .= '<tr valign="top" style="margin:15px 0px 15px 0px;padding:5px">';

    $case .= '<td valign="top" colspan="2" style="border-bottom:3px solid #ddd;width:100%;color:#666;font-family:arial;font-size:32px;padding:15px;">'.$lines[1].'</td>';

    $case .= '</tr>';

    $case .= '<tr valign="top" style="margin:15px 0px 15px 0px;padding:5px">';

    $case .= '<td valign="top" style="width:30%;color:#777;font-family:arial;font-size:16px;padding:15px;">Описание</td><td style="background:#f1f1f1;width:70%;color:#777;font-family:arial;font-size:16px;padding:15px;">'.$lines[2].'</td>';

    $case .= '</tr>';

    $case .= '<tr style="margin:15px 0px 15px 0px;padding:5px">';

    $case .='<td valign="top" style="width:30%;color:#777;font-family:arial;font-size:16px;padding:15px;">Method:</td><td style="background:#f1f1f1;width:70%;color:#777;font-family:arial;font-size:16px;padding:15px;">'.$method[2].'</td>';

    $case .= '</tr>';

    $case .= '<tr style="margin:15px 0px 15px 0px;padding:5px">';

    $case .='<td valign="top" style="width:30%;color:#777;font-family:arial;font-size:16px;padding:15px;">Url:</td><td style="background:#f1f1f1;width:70%;color:#777;font-family:arial;font-size:16px;padding:15px;"><span style="font-size:20px;font-weight:bold;">'.$url[2].'</span></td>';

    $case .= '</tr>';

    for($i1 = 5; $i1 < count($lines); $i1++) {

        if(strpos($lines[$i1],':') !== false) {

            $param = explode(':',$lines[$i1]);

            $title = $param[1];

            if(strpos($title,'#') !== false) {

                $list = explode('#',$title);

                for($i2 = 1; $i2 <= count($list); $i2++) {

                    $el = explode('=>',$list[$i2]);

                    $options = $el[1];

                    $value_list = explode(',',$el[2]);

                    $values = '';


                        for($i3 = 0; $i3 < count($value_list); $i3++) {

                            $str = explode('-',$value_list[$i3]);

                            if($str[0] != '') {


                                $values .= '<span style="font-weight:bold;">'.$str[0].'</span> : '.str_ireplace('=',':',$str[1]).'<br/>';

                            }

                        }



                        $options = explode('~',$options);
                        $about_options = $options[1];
                        $options = $options[0];




                    $display_val = '';
                    $display_options = '';
                    if($values == '') {
                        $display_val = 'display:none;';
                    }
                    if(strpos($options, '{') === false) {
                        $display_options = 'display:none;';
                    }

                    if($i2 == 1) {
                        $title = $list[0].'<div style="border-top:1px solid #ddd;margin-top:20px;width:100%;float:left;background:#eee;padding:7px;color:#666;font-weight:bold;font-size:16px;padding-top:20px;">'.trim($el[0]).'</div>' .
                            '<div style="'.$display_options.'width:100%;float:left;border-bottom:1px solid #ddd;padding:10px;margin-bottom:20px;">'.$options.' - дополнительные посылаемые  в фигурных скобках опции в конструкторе<br/>'.$about_options.'<br/><br/></div>' .
                            '<div style="'.$display_val.'width:100%;float:left;padding:10px;margin-bottom:20px;font-size:14px">'.$values.'</div>';
                    } else {
                        $title .= '<div style="border-top:1px solid #ddd;margin-top:20px;width:100%;float:left;background:#eee;padding:7px;color:#666;font-weight:bold;font-size:16px;padding-top:20px;">'.trim($el[0]).'</div>' .
                            '<div style="'.$display_options.'width:100%;float:left;border-bottom:1px solid #ddd;padding:10px;margin-bottom:20px;">'.$options.' - дополнительные посылаемые  в фигурных скобках опции в конструкторе<br/>'.$about_options.'<br/><br/></div>' .
                            '<div style="'.$display_val.'width:100%;float:left;padding:10px;margin-bottom:20px;font-size:14px">'.$values.'</div>';
                    }

                }

            }

        } else {

            $title = 'No comment';
        }

        $param = explode(':',$lines[$i1]);

        $param = explode(' ',$param[0]);

        $case .= '<tr style="margin:15px 0px 15px 0px;padding:5px">';

        $case .= '<td valign="top" style="width:30%;color:#777;font-family:arial;font-size:16px;padding:15px;"><span style="font-weight:bold;">'.$param[2].'</span><br/>'.$param[3].'</td><td style="background:#f1f1f1;font-family:arial;min-height:35px;width:70%;color:#777;font-size:16px;padding:15px;">'.$title.'</td>';

        $case .= '</tr>';
    }





    $case .= '</table>';

}






# create menu
$menu_html = '<ul>';
for($i = 1; $i <= count($menu); $i++) {
    if($menu[$i]['title'] != '') {
        $menu_html .= '<li style="padding:10px;"><a href="#'.$i.'" style="color:#666;" id="link_'.$i.'">'.$menu[$i]['title'].'</a></li>';

    }

}
$menu_html .= '</ul>';


$doc_html = '<div style="font-family:arial;width:100%;float:left;color:#fff;background:#0091ea;font-size:36px;"><div style="float:left;margin:20px;">gamificationlab.ru | API</div></div><div style="width:280px;float:left;">'.$menu_html.'</div><div style="width:70%;float:left;">'.$case.'</div>';

echo $doc_html;


$file = BASE_PATH.'/public/doc/index.html';

$doc_html = '';

if(!file_exists($file)) {

    System::newFile($file, $doc_html);

} else {

    System::writeFile($doc_html,$file,'w');

}


