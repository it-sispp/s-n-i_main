<?php

namespace app\controllers;

use app\models\Callers;
use app\components\Common;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $learn_form_model = new Callers();

        if ($learn_form_model->load(Yii::$app->request->post()) && $learn_form_model->validate() && $learn_form_model->save()) {
            $learn_form_model->site = 's-n-i.ru';
            $learn_form_model->save();
                //Отправка заявки на почту
                    Yii::$app->mailer->compose()
                        ->setFrom('web.admin@sispp.ru')
                        ->setTo(['contract@sispp.ru', 'n.a@sispp.ru'])
                        ->setSubject('Заявка на звонок с сайта >>' . $_SERVER['SERVER_NAME'])
                        ->setHtmlBody('<div> <strong>ФИО: </strong> ' . $learn_form_model['name'] . '</div>'
                            . '<div> <strong>Номер телефона: </strong>' . $learn_form_model['phone'] . '</div>'
                            . '<div> <strong>Населенный пункт: </strong>' . $learn_form_model['city'] . '</div>'
                            . '<div> <strong>email: </strong>' . $learn_form_model['email'] . '</div>'
                        )
                        ->send();

                Yii::$app->session->setFlash('success', "<div style='text-align: center'>Спасибо за Ваше обращение!
                    Наш менеджер свяжется с вами в течение 15 минут для уточнения всех деталей!</div>");
            return $this->refresh();
        }
        return $this->render('index', compact('learn_form_model'));
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
