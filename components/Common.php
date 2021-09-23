<?php

namespace app\components;

use DateTime;

use Yii;
use yii\base\Component;
use yii\helpers\FileHelper;
use yii\helpers\Html;

?>

    <?php

class  Common extends Component {



//Получаем картинку новостей в связи с переносом папки
    public static function getRemoteNewsPicture($image=NULL, $id=NULL) {
        if (!empty($image)) {

            try {
                $array_pict=[];

                //Старое расположение файлов
                $old_file=Yii::$app->params['patch_global_pict'].$image;
                // Open file
                $handle = @fopen($old_file, 'r');

                if($handle){
                    $array_pict['path_file']=$old_file;
                    $array_pict['img']=Html::img($old_file, ['style'=>'width:100%']);
                }
                else {
                    //По новой структуре от 20.08.2021
                    $new_file=Yii::$app->params['patch_global_pict'].'documents/common/news/'.$id.'/'.$image;
                    $array_pict['path_file']=$new_file;
                    $array_pict['img']=Html::img($new_file, ['style'=>'width:100%']);
                }
                return $array_pict;

            } catch (\Exception $e) {

            }
        }

    }









//Добавляем www
    public static function getWwwServerName($server_name) {

        if (mb_substr($server_name, 0,3) !=='www') {
            $server_name='www.'.$server_name;
        }



        return $server_name;
    }




//Получаем ID менеджера из куки
    public static function getCockieCheckOwner() {
        try {
            $text_cookie_param1='Не определён';
            $cookie_param1=htmlspecialchars($_COOKIE["owner_check"]);

            if (!empty($cookie_param1)) {
                $model_manager = User::findOne($cookie_param1);
                $text_cookie_param1=$model_manager['display_name'];
            }
        } catch (\Exception $e) {
            $text_cookie_param1='Не определён';
        }


        return $text_cookie_param1;

    }




//    //Кешируем ID менеджера для заявок после входа на сайт
//    public static function getCacheCheckOwner($id) {
//
//        // Формируем ключ
//        $key = 'CacheCheckOwner';
////        strtotime(date('d-m-Y H:i:s.u'))*rand(1,999999)
//// Обращаемся к кэшу приложения
//        $cache = Yii::$app->cache;
////Данные из кеша
//        $result_cache = $cache->get($key);
//
//        if (empty($result_cache)) {
//            $buldKey = ['yii\widgets\FragmentCache', $key];
//            Yii::$app->cache->delete($buldKey);
//
//            $result_cache = $id;
//
//// Обращаемся к кэшу приложения
//            $cache = \Yii::$app->cache;
//            $cache->set($key, $result_cache, 20/*86400*/);
//        }
//
//    }


    //Получаем ссылки на соцсети
    public static function getSocialUrlsBootstrap($model_org=NULL, $color='white', $margin1='5px') {

        try {

            if (!empty($model_org)) {
                $social_links = explode('---', $model_org['social_links']);

                if (!empty($social_links[3])) {
                echo '<a href="'.$social_links[3].'" target="_blank" style="text-decoration: none;">
                        <i  class="fa fa-vk" style="margin: '.$margin1.'; color: '.$color.'" aria-hidden="true"></i>
                    </a>';
                }

                if (!empty($social_links[2])) {
                    echo  ' <a href="'.$social_links[2].'" target="_blank" style="text-decoration: none;">
                        <i class="fa fa-instagram" 
                        style="margin: '.$margin1.'; color: '.$color.'" aria-hidden="true"> </i>
                        </a>';
                }

                if (!empty($social_links[0])) {
                    echo    '<a href="'.$social_links[0].'" target="_blank" style="text-decoration: none;">
                        <i class="fa fa-odnoklassniki"  
                        style="margin: '.$margin1.'; color: '.$color.'" aria-hidden="true"></i>
                    </a>';
                }

                if (!empty($social_links[1])) {
                    echo ' <a href="'.$social_links[1].'" target="_blank" style="text-decoration: none;">
                    <i class="fa fa-youtube" 
                    style="margin: '.$margin1.'; color: '.$color.'" aria-hidden="true"></i></a>';
                }
            }

        } catch (\Exception $e) {

        }

    }



    public static function getTeacherById($id) {
        try {
            $teacher = Teachers::findOne($id);
            return $teacher['name'];

        } catch (\yii\db\Exception $e) {

        }

    }



//Получаем запись из таблицы текущей организации
    public static function getCurrentModelOrganization() {

        try {

        $domain_name = Yii::$app->params['domain_name'];

        $model = Organizations::find()
            ->where(['domain_name_base' => $domain_name])
            ->asArray()
            ->one();

            return $model;

        } catch (\yii\db\Exception $e) {
        }

    }


//Глобальный подсчёт слушателей
    public static function getGlobalCountStudents() {

        try {

            $model_org=Common::getCurrentModelOrganization();

            if (!empty($model_org)) {

// Формируем ключ
                $key = 'get_global_count_students_'.Yii::$app->params['domain_name'];


// Обращаемся к кэшу приложения
                $cache = Yii::$app->cache;
//Данные из кеша
                $result_cache = $cache->get($key);





                if (empty($result_cache)) {

                    $buldKey = ['yii\widgets\FragmentCache', $key];
                    Yii::$app->cache->delete($buldKey);


                    $client = new Client();
                    $response = $client->createRequest()
                        ->setMethod('post')
                        ->setUrl('http://pay.cabinet.sispp.ru/main/default/global-count-students')
                        ->setData(
                            [
                                'tokenGlobalCount' => '15a-4ec3284-bc94-124e82-47-a22579b1-17-5c8fe9',

                                'id_org' => $model_org['id'],//Подставить основной ID
                                'id_org_secondary' => $model_org['id_reserve'], //Подставить дополнительный с МИПКП

                            ])
                        ->send();


                    $result_cache = $response->data['count_global'];

// Обращаемся к кэшу приложения
                    $cache = \Yii::$app->cache;
                    $cache->set($key, $result_cache, 864000);


                }




                return $result_cache;
            }

        } catch (\yii\db\Exception $e) {
        }
    }



    //Показываем программы ПО
    public static function showProgrammsPO(
        $education1_index = NULL,
        $education2_index = NULL,
        $global_type = NULL, $id_org = NULL, $search_text = '' ) {
        //Получаем программы ПО (условия примерные, уточнить у Зубаревой)

        $model_programms = MedicalProgramms::find()
            ->where(
                '(
                   id_org=' . $id_org
                . ' or id_org_secondary=' . $id_org
                . ' or id_org_third=' . $id_org
                . ')'
            )

            ->andwhere('CHAR_LENGTH(trim(category_id))>0')

//            ->andWhere(['like', 'name', $search_text])
            ->andWhere('(name like "%'.$search_text.'%"'. ' or category_id like '.'"%'.$search_text.'%"'.')')


            ->groupBy('name')
            ->orderBy('name')
//            ->limit(40)
        ;

    //Образование текстом
        $param_text = Yii::$app->params['type_education'][$education1_index]['name'];
        $param2_text = Yii::$app->params['type_education'][$education2_index]['name'];
//Образование текстом


//        if (!empty($param)) {
            
            if (!empty($model_programms)) {
                if (!empty($global_type)) {
                    $model_programms =
                        $model_programms
                            ->andWhere('(education=' . $param_text
                                . ' or education=' . $param2_text . ')')
                            ->andWhere(['global_type_programm' => $global_type])
                            ->andWhere('( id_moodle >0) ')
                            ->asArray()
                            ->all();


                    if (!empty($model_programms)) {

                        $offset_model = 0;
                        for ($i1 = 1; $i1 <= 4; $i1++) {

                            ?>
                            <div class="col-12 col-sm-6 col-lg-6">
                                <ul class="courses-list">
                                    <?
                                    foreach (array_slice($model_programms,
                                        $offset_model, 10) as $row):
                                            $name_programm = $row['name'];
//                                        if (!empty($name_category)) {

//                                        $param1 = NULL, $education1 = NULL, $education2 = NULL, $categoy_id=''

                                        echo '<li>

'.Html::a($name_programm

                                                , ['/catalog'],
                                                [

                                                    'data-method' => 'GET',
                                                    'data-params' =>
                                                        [
                                                            'param1' => $row['global_type_programm'],

//                                                            'education1'=>'"'.$row['education'].'"',
//                                                            'education2'=>'"'.$row['education'].'"',
                                                            'education1_index'=>$education1_index,
                                                            'education2_index'=>$education2_index,

                                                            'category_id'=>'"'.$row['category_id'].'"',
                                                            'name_programm'=>$name_programm

                                                        ]
                                                ]).'



</li>';
//                                        }

                                    endforeach;
                                    ?>
                                </ul>
                            </div>

                            <?
                            $offset_model = $offset_model + 10;
                        }
                    }
                }
            }
//        }


    }










    //Создаём токен
    public static function getToken()
    {

        $date = new DateTime();




        return mb_substr(md5(md5($date->getTimestamp())), 0, 30);

    }



//Кнопка всех программ
    public static function getBtnAllProgramm($param1 = '#',
                                             $education1_index = '', $education2_index = ''
//    $category_id=''
    )
    {
        return '<div >  
                    '

            . Html::a('Полный список специальностей'.
                '<img  src="/assets/img/'.Yii::$app->params['domain_name'].'/arrow-green.png">',
                ['/main/catalog'],
                [
                    'data-method' => 'get',


                    'class' => 'text-nowrap btn-green-stroke2 txt14 semibold',

                    'data-params' =>
                        [
                         'param1' => $param1,
//                         'education1'=>$education1,
//                         'education2'=>$education2,

                         'education1_index'=>$education1_index,
                         'education2_index'=>$education2_index,

//                         'category_id'=>$category_id
                        ]
                ])
            . '</div>';
    }


//Отправляем почту
    public static function sendEmailGlobal($email_to = NULL, $email_subject = NULL, $email_html = NULL)
    {
        if (!empty($email_from)) {
            if (!empty($email_to)) {
                if (!empty($email_subject)) {
                    if (!empty($email_html)) {
                        try {
                            Yii::$app->mailer->compose()
                                ->setFrom(Yii::$app->params['global_email_for_sending'])
                                ->setTo($email_to)
                                ->setSubject($email_subject)
                                ->setHtmlBody($email_html)
                                ->send();
                        } catch (\Swift_TransportException $e) {
                        } catch (yii\base\ErrorException $e) {

                        }
                    }
                }
            }
        }
    }


//Поиск файлов
    public static function filessearch($path)
    {
        $path2 = Yii::getAlias($path);
        $files = array();
        if (is_dir(Yii::getAlias($path2))) {
            $files = FileHelper::findFiles($path2, ['recursive' => FALSE]);// код, который может выбросить исключение
        }
        return $files;
    }

    //Получаем сколько лет работает организация
    public static function getCountYearsWorksOrganization($time_stamp_org = NULL)
    {
        try {

            $date_start = date('Y', $time_stamp_org);
            $date_finish = date('Y');
            return $date_finish - $date_start;

        } catch (Exception $e) {
            return 5;
        }

        return 5;

    }


    //Показываем документы
    public static function showHiddenBigOriginalDoc($number_pict = NULL, $patch_site = NULL, $pict_id = NULL)
    {

        if (!empty($number_pict)) {
            if (!empty($patch_site)) {
                if (!empty($pict_id)) {
                    $content =
                        '<div class="modal fade" role="dialog" tabindex="-1" id="' . $pict_id . '">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: none;">
                <button type="button"
                           class="close"
                        data-dismiss="modal"
                        aria-label="Close" style="color: #e0e0e0;">
                    <span aria-hidden="true">×</span></button></div>
            <div class="modal-body text-center">
                <img class="img-fluid"
                     src="'
                        . Yii::$app->params['patch_global_pict']
                        . 'documents/'
                        . $patch_site . '/original/' . $number_pict . '.jpg'
                        . '">
            </div>
        </div>
    </div>
</div>';

                    return $content;


                }
            }
        }


    }


    //Показываем документ "смотреть все"
    public static function showAddHiddenDoc($number_pict = NULL, $patch_site = NULL, $caption_pict = NULL, $pict_link = NULL)
    {

        if (!empty($number_pict)) {
            if (!empty($patch_site)) {
                if (!empty($caption_pict)) {
                    if (!empty($pict_link)) {

                        $content =
                            '<div class="col-12 col-sm-10 col-md-6 mar-bot-30 collapse" 
                    id="docCollapse">
                <div class="doc" style="background-image: url(' .
                            Yii::$app->params['patch_global_pict']
                            . 'documents/' . $patch_site . '/original/' . $number_pict . '.jpg'
                            . ');">
                    <div class="doc-grad">
                        <div class="text-center doc-grad-hover">
                            <p class="text-center text-white d-flex
                            d-xl-flex justify-content-xl-center txt18 regular"
                               style="max-width: 350px;margin: auto;">' . $caption_pict . '</p>
                            <a class="text-lg-center justify-content-center doc-read-more
                            txt11 medium uppercase" href="#" style="margin: 15px 0;"
                               data-toggle="modal" data-target="' . $pict_link . '">
                                <img src="/assets/img/ico18.png" style="width: 23px;height: 13px;">&nbsp;Смотреть документ</a>
                            <a
                                    class="btn-yellow txt13 semibold dark" href="#" data-toggle="modal"
                                    data-target="#callback">Нужен такой документ
                                    </a>
                        </div>
                    </div>
                </div>
            </div>';

                        return $content;
                    }
                }
            }
        }

        return '';
    }
    //Показываем документ "смотреть все"


    //Баннер с заменой текста и картинки
    public static function showTopBanner($pict = NULL, $text_banner = NULL)
    {

        if (!empty($pict)) {
            if (!empty($text_banner)) {

                $content =
                    '<section id="top-banner" class="catalog-banner">
    <div class="parallax-bg"><img class="image-bg3 catalog-banner-bg"
                                  src="' . $pict . '" style="top: 100px;"></div>
    <nav class="navbar navbar-light navbar-expand-md d-none d-md-block menu">
        <div class="container-fluid">
        <button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1">
                <span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon">
                
</span>
                </button>
            <div class="collapse navbar-collapse" id="navcol-1">
                <ul class="nav navbar-nav" style="margin: auto;">
                   '
                    . Common::showNavBarItems()
                    . '
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row title-row-catalog">
            <div class="col-12 col-md-8 col-lg-6 text-center text-sm-left">
                <div class="title-bg-white">
                    <p class="txt40 bold dark">' . $text_banner . '
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>';

                return $content;

            }
        }

    }


    //Показываем программы
    public static function showProgrammsActual($education1_index = NULL,
                                               $education2_index = NULL,
                                               $global_type = NULL,
                                               $id_org = NULL,
                                               $search_text = '' ){
        //Получаем категории программ ПП, ПК, ПО (условия примерные, уточнить у Зубаревой)

        $model_programms = MedicalProgramms::find()
            ->where(
                '(
                   id_org=' . $id_org
//                . ' or id_org_secondary=' . $id_org
//                . ' or id_org_third=' . $id_org
                . ')'
            )

//            ->andwhere('CHAR_LENGTH(trim(category_id))>0')
            ->orderBy('category_id')

        ;

        //Если поиск, то группируем по категории
        if (!empty($search_text)) {
            $model_programms
            ->andWhere('(name like "%'.$search_text.'%"'
                . ' or category_id like '.'"%'.$search_text.'%"'.')');
        }
        if (empty($search_text)) {
            $model_programms->andwhere('CHAR_LENGTH(trim(category_id))>0')
                ->groupBy('category_id')
//                ->limit(40)
            ;
        }

//Образование текстом
        $param_text = Yii::$app->params['type_education'][$education1_index]['name'];
        $param2_text = Yii::$app->params['type_education'][$education2_index]['name'];
//Образование текстом


            if (!empty($param_text)) {
            if (!empty($model_programms)) {
                if (!empty($global_type)) {
                    $model_programms =
                        $model_programms
                            ->andWhere('(education=' . $param_text
                                . ' or education=' . $param2_text . ')')

                            ->andWhere(['global_type_programm' => $global_type])
                            ->andWhere('( id_moodle >0) ')
                            ->asArray()
                            ->all();



                    if (!empty($model_programms)) {

                        $offset_model = 0;
//                        $count_length=count($model_programms)/4;
                        $counter_spec=1;

                        for ($i1 = 1; $i1 <= 4; $i1++) {

                            ?>
                            <div class="col-12 col-sm-6 col-lg-3">

                                <ul class="courses-list">
                                    <?
                                    foreach (array_slice($model_programms,
                                        $offset_model, 10) as $row):



                                        //Если поиск, то показываем программы
                                        if (!empty($search_text)) {
                                            $add_category = '<div style="font-size: 12px">' . $row['category_id'] . '</div>';

                                            if (mb_strlen($row['name']) >= 30) {
                                               $name_category_or_programm =  mb_substr($row['name'], 0, 30) . '...' . $add_category;
                                            } else {
                                                  $name_category_or_programm =  $row['name'] . $add_category;
                                            }
                                        }
                                        else { //Иначе показываем категории
                                            if (mb_strlen($row['category_id']) >= 30) {
                                                  $name_category_or_programm = mb_substr($row['category_id'], 0, 30) . '...';
                                            } else {
                                                $name_category_or_programm =  $row['category_id'];
                                            }
                                        }


                                    //temp
//                                        $name_category_or_programm = $row['name'].'<div style="font-size: 12px">'.$row['category_id'].'</div>';
                                    //temp
                                        echo '<li>

                    '.Html::a($name_category_or_programm

                                                , ['/main/catalog'],
                                                [
                                                    'data-method' => 'POST',
                                                    'data-params' =>
                                                        [
                                                            'param1' => $row['global_type_programm'],

                                                            'education1_index'=>$education1_index,
                                                            'education2_index'=>$education2_index,

                                                            'category_id'=>($row['category_id']),
                                                            'name_programm'=>$search_text

                                                        ]
                                                ]).'



</li>';
//                                        }
                                        $counter_spec+=1;
                                    endforeach;
                                    ?>
                                </ul>
                            </div>

                            <?
                            $offset_model = $offset_model + 10;
                        }

                        echo 'Всего специальностей '.count($model_programms);
                    }
                }
            }
        }


    }


//Показываем массив ссылок
    public static function showNavBarItems($picture = '', $style = '', $add_block1='')
    {
        $array_link = [

            0 => ['description' => '<img src="/images/technical/other/home.png" width="20">', 'link' => '/'],
            1 => ['description' => 'Каталог специальностей', 'link' => '/catalog/all#tabCourses'],
            2 => ['description' => 'НМО', 'link' => '/nmo'],
            3 => ['description' => 'Стажировка', 'link' => '/internship'],

            //            4 => ['description' => 'Семинары', 'link' => '/seminars'],
            4 => ['description' => 'Вебинары', 'link' => '/webinar'],
            5 => ['description' => 'Статьи', 'link' => 'articles'], ///articles
            6 => ['description' => 'ЦЗН', 'link' => '/employment'],
            7 => ['description' => 'Об институте', 'link' => '/about'],
            8 => ['description' => 'Отзывы', 'link' => '/reviews'],

            9 => ['description' => 'Контакты', 'link' => '/contacts'],

        ];

        $content = ' 
  <div class="parallax-bg">
  '.$add_block1.'
        <img style="' . $style . '" class="image-bg" src="/images/technical/background/' . $picture . '">
  </div>
        
   <nav class="navbar navbar-light navbar-expand-md d-none d-md-block menu">
        <div class="container-fluid">
            <button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navcol-1">
                <ul class="nav navbar-nav" style="margin: auto;" >';

        foreach ($array_link as $row):
            $content = $content . '<li class="nav-item" role="presentation">
<a class="nav-link txt11 medium" href="' . $row['link'] . '" style="font-size:13px;">' . $row['description'] . '</a></li>';    // Принудительно задали шрифт крупнее
        endforeach;


        $content = $content . '</ul>
            </div>
        </div>
    </nav>';
        return $content;

    }
//Показываем массив ссылок


//Обращаемся к модели текущей организации
    public static function showFullRequisitesModel()
    {
        try {

            $domain_name = Yii::$app->params['domain_name'];
            $model = Organizations::find()
                ->where(['domain_name_base' => $domain_name])
                ->asArray()
                ->one();
            return $model;

        } catch (\Exception $e) {
        }

        return NULL;

    }
//Обращаемся к модели текущей организации


//Формат телефона
    public static function getFormatPhone($my_phone = NULL)
    {

        $my_phone = preg_replace('/[^0-9]/', '', $my_phone);

        if (((strlen($my_phone) == 11) and (($my_phone[0] == 7) or ($my_phone[0] == 8)))) {
            $my_phone = mb_substr($my_phone, 1, 10);

            return '+7 (' . mb_substr($my_phone, 0, 3) . ') '
                . mb_substr($my_phone, 3, 3) . '-'
                . mb_substr($my_phone, 6, 2) . '-'
                . mb_substr($my_phone, 8, 2);
        } else {
            if (strlen($my_phone) == 10) {
                return '+7 (' . mb_substr($my_phone, 0, 3) . ') '
                    . mb_substr($my_phone, 3, 3) . '-'
                    . mb_substr($my_phone, 6, 2) . '-'
                    . mb_substr($my_phone, 8, 2);
            }
        }

        return $my_phone;

    }

}

