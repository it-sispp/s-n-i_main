<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Сибирь. Наука Интеллект';
?>
<div class="site-index">
    <div class="container">
        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-6">
                <div class="main_img_cont">
                    <?= Html::img('/assets/img/logo.png', ['style' => 'width: 60%']) ?>
                    <p class="main_label_center">СИБИРЬ. НАУКА. ИНТЕЛЛЕКТ</p>
                </div>
            </div>
            <div class="col-lg-3"></div>
        </div>
        <div class="row">
            <div class="col-lg-2" style="text-align: center; padding: 0"><?= Html::img('/assets/img/sispp.png', ['style' => 'width: 80%']) ?>
                <p class="institute_label">АНО ДПО <br>«Сибирский институт практической психологии, педагогики и социальной работы»</p>
            </div>
            <div class="col-lg-2" style="text-align: center; padding: 0"><?= Html::img('/assets/img/ipipksz.png', ['style' => 'width: 80%']) ?>
                <p class="institute_label">ЧУДПО <br>«Институт переподготовки и повышения квалификации специалистов здравоохранения»</p>
            </div>
            <div class="col-lg-2" style="text-align: center; padding: 0"><?= Html::img('/assets/img/sinmo.png', ['style' => 'width: 80%']) ?>
                <p class="institute_label">АНО ДПО <br>«Сибирский институт непрерывного медицинского образования»</p>
            </div>
            <div class="col-lg-2" style="text-align: center; padding: 0"><?= Html::img('/assets/img/mipkp.png', ['style' => 'width: 80%']) ?>
                <p class="institute_label">АНО ДПО <br>«Международный институт повышения квалификации и переподготовки»</p>
            </div>
            <div class="col-lg-2" style="text-align: center; padding: 0"><?= Html::img('/assets/img/mmk.png', ['style' => 'width: 80%']) ?>
                <p class="institute_label">АНО ПО <br>«Международный многопрофильный колледж»</p>
            </div>
            <div class="col-lg-2" style="text-align: center; padding: 0"><?= Html::img('/assets/img/abs.png', ['style' => 'width: 80%']) ?>
                <p class="institute_label">ООО <br>«Академия бизнес-стратегий»</p>
            </div>
        </div>
    </div>
</div>
<div class="site_form_section">
    <div class="site_form_section2">
        <div class="site_form_label" data-customstyle="yes">Обратная связь</div>
        <?php if( Yii::$app->session->hasFlash('success') ): ?>
            <div class="alert alert-success alert-dismissible" role="alert" style="background: #26ae04; color: white; font-weight: 600; font-size: 20px">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <?php echo Yii::$app->session->getFlash('success'); ?>
            </div>
        <?php endif;?>
            <div class="container">
                <div class="row">
                    <div class="col-lg-3"></div>
                    <div class="col-lg-6">
                        <?php
                        $form = ActiveForm::begin([
                        ])
                        ?>

                        <?php
                        echo $form->field($learn_form_model,'name')->textInput(
                            ['placeholder' => 'Ваше имя',
                                'id'=>'phone_base1'
                            ])->label(false) ?>

                        <?= $form->field($learn_form_model, 'city')->textInput(
                            ['placeholder' => 'Город (Населенный пункт)'])->label(false) ?>

                        <?php echo $form->field($learn_form_model,'phone')
                            ->widget(\yii\widgets\MaskedInput::className(),
                                ['mask' => '+9 (999) 999-99-99'])
                            ->textInput(['placeholder' => 'Номер телефона'])->label(false) ?>

                        <?= $form->field($learn_form_model, 'email')->textInput(
                            ['placeholder' => 'email'])->label(false) ?>

<!--                        <div style="transform:scale(0.77);transform-origin:0 0;">-->
<!--                            --><?//= $form->field($learn_form_model, 'reCaptcha')->label(false)->widget(
//                                \himiklab\yii2\recaptcha\ReCaptcha2::className(),
//                                [
//                                    'siteKey' => '6LdKA9sbAAAAAFUNkWms_bP_92WdKfIs9-HSg8jO', // unnecessary is reCaptcha component was set up
//                                ]
//                            )->label(false) ?>
<!--                        </div>-->


                        <?php echo Html::submitButton('Получить консультацию',
                            [

                                'id'=>'btn_base1'
                            ]
                        )
                        ?>



                        <?php $form = ActiveForm::end(); ?>
                    </div>
                    <div class="col-lg-3"></div>
                </div>

            </div>


    </div>

</div>
