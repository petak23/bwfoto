<?php

declare(strict_types=1);

namespace App\FrontModule\Forms\User;

use Language_support;
use Nette\Application\UI\Form;
use Nette\Security;

/**
 * Sign in form
 * Last change 03.10.2020
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2020 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.1.1
 */
class SignInFormFactory {
  /** @var User */
  private $user;
  /** @var Language_support\LanguageMain */
  private $texts;

  /**
   * @param Security\User $user
   * @param Language_support\LanguageMain $language_main */
  public function __construct(Security\User $user, Language_support\LanguageMain $language_main) {
    $this->user = $user;
    $this->texts = $language_main;
	}
  
  /**
   * @return string */
  public function getTexts() {
    return $this->texts;
  }
  
  /**
   * Prihlasovaci formular
   * @var string $language Skratka aktualneho jazyka
   * @return Form */
  public function create($language): Form  {
    $form = new Form();
		$form->addProtection();
    $this->texts->setLanguage($language);
    $form->setTranslator($this->texts);
    $form->addText('email', 'SignInForm_email')
         ->setHtmlType('email')
         ->setAttribute('autofocus', 'autofocus')
         ->setHtmlAttribute('size', 0)->setHtmlAttribute('maxlength', 100)
         ->setHtmlAttribute('placeholder', 'SignInForm_email')
         ->addRule(Form::EMAIL, 'Musíte zadať platnú e-mailovú adresu!')
				 ->setRequired('SignInForm_email_req');
		$form->addPassword('password', 'SignInForm_password')
         ->setHtmlAttribute('size', 0)
         ->setHtmlAttribute('placeholder', 'SignInForm_password')
         ->addRule(Form::MIN_LENGTH, 'SignInForm_password_min_lenght', 3)
				 ->setRequired('SignInForm_password_req');
		$form->addCheckbox('remember', 'SignInForm_remember');
    $form->addSubmit('login', 'SignInForm_login')
         ->setAttribute('class', 'btn btn-success')
         ->onClick[] = [$this, 'signInFormSubmitted'];
    $form->addSubmit('forgottenPassword', 'SignInForm_forgottenPassword')
         ->setAttribute('class', 'btn btn-link');
    $renderer = $form->getRenderer();
    $renderer->wrappers['controls']['container'] = 'div class=sign-in-form';
    $renderer->wrappers['pair']['container'] = 'div class="form-group row justify-content-center"';
    $renderer->wrappers['pair']['.error'] = 'has-danger';
    $renderer->wrappers['control']['container'] = 'div class="col-12 col-sm-6"';
    $renderer->wrappers['label']['container'] = 'div class="d-none"';//'div class="col-sm-3 col-form-label"';
    $renderer->wrappers['control']['description'] = 'span class=form-text';
    $renderer->wrappers['control']['errorcontainer'] = 'span class=form-control-feedback';
    $renderer->wrappers['control']['.error'] = 'is-invalid';

    foreach ($form->getControls() as $control) {
      $type = $control->getOption('type');
      if (in_array($type, ['text', 'textarea', 'select'], true)) {
        $control->getControlPrototype()->addClass('form-control');
      } elseif (in_array($type, ['checkbox', 'radio'], true)) {
        if ($control instanceof \Nette\Forms\Controls\Checkbox) {
          $control->getLabelPrototype()->addClass('form-check-label');
        } else {
          $control->getItemLabelPrototype()->addClass('form-check-label');
        }
        $control->getControlPrototype()->addClass('form-check-input');
        $control->getSeparatorPrototype()->setName('div')->addClass('form-check');
      }
    }
		return $form;
	}
  
  /** 
   * Overenie po prihlaseni
   * @param \Nette\Forms\Controls\SubmitButton $button Data formulara */
	public function signInFormSubmitted(\Nette\Forms\Controls\SubmitButton $button) {
    $values = $button->getForm()->getValues();
    try {
      $this->user->setExpiration($values->remember ? '14 days' : '30 minutes');
			$this->user->login($values->email, $values->password);
		} catch (Security\AuthenticationException $e) {
      $button->addError($e->getCode());
    }
	}
}