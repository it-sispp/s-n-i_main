<?php

namespace app\models;

use app\components\behaviors\PurifyBehavior;

use floor12\phone\PhoneValidator;
use Yii;

/**
 * This is the model class for table "{{%form_default}}".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $email
 * @property string|null $phone
 * @property string $city
 * @property string $comment
 */
class Callers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $reCaptcha;

    public static function tableName()
    {

        return 'univ_callers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 50],

            ['phone',   PhoneValidator::class],

            [['created_at'], 'safe'],
            [['updated_at'], 'safe'],
            ['name', 'required', 'message'=>'Необходимо ввести ваше имя'],
            ['email', 'required','message'=>'Необходимо ввести ваш адрес электронной почты'],
            ['email', 'email', 'message'=>'Введите корректный адрес электронной почты'],
            ['phone', 'required','message'=>'Необходимо ввести номер телефона'],
            ['city', 'required','message'=>'Необходимо ввести ваш город (Населенный пункт)'],
//         
            [['name'], 'trim'],
            [['token'], 'string', 'max' => 30],



//            [['email'], 'email', 'max' => 50],

            [['email'], 'email'],
            [['email'], 'required'],

//            [['reCaptcha'],  \himiklab\yii2\recaptcha\ReCaptchaValidator2::className(),
//                'secret' => '6LdKA9sbAAAAAJ6dJ1e1ElPtsWfQSSoVjvv9EwEb', // unnecessary if reСaptcha is already configured
//                'uncheckedMessage' => 'Подтвердите, что вы не робот']
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'ФИО',
            'email' => 'Электронная почта',
            'phone' => 'Телефон',
            'city' => 'Город',
            'comment' => 'Комментарий',
            'site' => 'Заявка с сайта',
            'checkbox_agreement' => 'Согласие на обработку персональных'
        ];
    }

    public function behaviors(){
        return [
            'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],

                ],
            ],

            'purify' => [
                'class' => PurifyBehavior::class,
                'attributes' => ['name', 'phone', 'city'],
            ],
        ];
    }
}
