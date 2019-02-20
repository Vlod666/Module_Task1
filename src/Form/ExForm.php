<?php

namespace Drupal\ex_form\Form;

use Drupal\Core\Form\FormBase;																			// Базовый класс Form API
use Drupal\Core\Form\FormStateInterface;														// Класс отвечает за обработку данных


class ExForm extends FormBase {

	// метод, который отвечает за саму форму
	public function buildForm(array $form, FormStateInterface $form_state) {

		$form['Fname'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Ваше имя'),
			'#description' => $this->t('Имя должно содержать английские буквы'),
			'#required' => TRUE,
		];

        $form['Lname'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Ваша фамилия'),
            '#description' => $this->t('Фамилия должна содержать английские буквы'),
            '#required' => TRUE,
        ];

        $form['subject'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Предмет'),

            '#required' => TRUE,
        ];

        $form['message'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Сообщение'),
            '#description' => $this->t('Введите текст'),
            '#required' => TRUE,
        ];

        $form['email'] = [
            '#type' => 'email',
            '#title' => $this->t('email'),
            '#description' => $this->t('Введите коректно ваш email'),
            '#required' => TRUE,
        ];



		$form['actions']['submit'] = [
			'#type' => 'submit',
			'#value' => $this->t('Отправить форму'),
		];

		return $form;
	}

	// метод, который будет возвращать название формы
	public function getFormId() {
		return 'ex_form_exform_form';
	}

	// ф-я валидации
	public function validateForm(array &$form, FormStateInterface $form_state) {
        $title = [];
		$title[0] = $form_state->getValue('Fname');
        $title[1] = $form_state->getValue('Lname');



		if (preg_match("/[^a-zA-Z]/", $title[0], $match)) {
			$form_state->setErrorByName('Fname', $this->t('Введите английские буквы'));
		}

        if (preg_match("/[^a-zA-Z]/", $title[1], $match)) {
            $form_state->setErrorByName('Lname', $this->t('Введите английские буквы'));
        }

		$email = $form_state->getValue('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $form_state->setErrorByName('email', $this->t('Неправильный email'));
        }
	}

	// действия по сабмиту
	public function submitForm(array &$form, FormStateInterface $form_state) {


       // $mailManager = \Drupal::service('plugin.manager.mail');

        //$to     = $form_state->getValue('email');
        //$key    = 'ex_email';
        //$send = true;
        $params = [];
        $params['subject'] =$form_state->getValue('subject');
        $params['body'] =$form_state->getValue('message');
        //$result = $mailManager->mail('ex_form', $key, $to, language_default(), $params , NULL ,$send );

                          // отправка на email
        $firstname = $form_state->getValue('Fname');
        $lastname = $form_state->getValue('Lname');
        $res = mail('vvladiolus@mail.ru', $params['subject'], $params['body']);

        if($res){

            drupal_set_message('Уважаемый '. $firstname . ' ' . $lastname .' письмо отправлено на E-mail vvladiolus@mail.ru!' );
            \Drupal::logger('ex_form')->notice($form_state->getValue('email') . ' почта отправлена.', []);
        }
        else
        {
            drupal_set_message("Ошбика при отправке письма!" );
        }

                //создание контакта

        $email = $form_state->getValue('email');


        $url = "https://api.hubapi.com/contacts/v1/contact/createOrUpdate/email/".$email."/?hapikey=e1d5a93c-63c5-43b2-8d8f-c3553c8f6655";

        $data = array(
            'properties' => [
                [
                    'property' => 'firstname',
                    'value' => $firstname
                ],
                [
                    'property' => 'lastname',
                    'value' => $lastname
                ]
            ]
        );


        $json = json_encode($data,true);

        $response = \Drupal::httpClient()->post($url.'&_format=hal_json', ['headers' => ['Content-Type' => 'application/json'],'body' => $json]);

	}

}

/*function ex_form_mail($key, &$message, $params) {
    switch ($key) {

        case 'ex_email':
            $message['from'] = \Drupal::config('system.site')->get('mail');
            $message['subject'] = $params['subject'];
            $message['body'][] =  $params['body'];
            break;
    }
}*/
