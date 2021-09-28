<?php
declare(strict_types=1);

namespace App\AdminModule\Components\Article\TitleImage;

use DbTable;
use Nette\Application\UI\Form;
use Nette\Database;

/**
 * Formular a jeho spracovanie pre zmenu nadpisu.
 * Posledna zmena 27.09.2021
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.5
 */
class ZmenNadpisFormFactory {
  /** @var DbTable\Hlavne_menu_lang */
	private $hlavne_menu_lang;
  /** @var DbTable\Lang */
  public $lang;
 
  public function __construct(DbTable\Hlavne_menu_lang $hlavne_menu_lang,
                              DbTable\Lang $lang) {
    $this->hlavne_menu_lang = $hlavne_menu_lang;
    $this->lang = $lang->findAll();
	}
  
  /**
   * Formular.
   * @param int $id Id polozky v hlavnom menu
   * @return Form */  
  public function create(int $id): Form  {
    
		
    $form = new Form();
		$form->addProtection();
    $form->addHidden("id_hlavne_menu", $id);
    foreach ($this->lang as $j) {
      if ($this->lang->count() > 1) $form->addGroup('Časť pre jazyk: '.$j->nazov);
      $form->addText($j->skratka.'_view_name', 'Názov zobrazený v nadpise pre jazyk '.$j->nazov.":", 90, 255)
           ->addRule(Form::MIN_LENGTH, 'Popis musí mať spoň %d znaky!', 2)
           //->setOption('description', 'Podrobnejší popis položky slúži pre vyhľadávače a zároveň ako pomôcka pre užívateľa, keď príde ukazovateľom myši nad odkaz(bublinová nápoveda).')
           ->setRequired('Popis pre jazyk "'.$j->nazov.'" musí byť zadaný!');
      $form->addText($j->skratka.'_menu_name', 'Názov zobrazený v menu pre jazyk: '.$j->nazov.":", 30, 100)
           ->addRule(Form::MIN_LENGTH, 'Názov musí mať spoň %d znaky!', 2)
           ->setRequired('Názov  pre jazyk "'.$j->nazov.'" musí byť zadaný!');
      $form->addText($j->skratka.'_h1part2', 'Druhá časť nadpisu(podtitulok) pre jazyk: '.$j->nazov.":", 90, 100);
      $values = $this->hlavne_menu_lang->findOneBy(['id_hlavne_menu'=>$id, 'id_lang'=>$j->id]);
      if ($values != null) {
        $form->setDefaults([
          $j->skratka.'_menu_name'=> $values->menu_name,
          $j->skratka.'_h1part2'=> $values->h1part2,
          $j->skratka.'_view_name'=> $values->view_name,
        ]);
      }
		}
    if ($this->lang->count() > 1) $form->addGroup("");
    $form->addSubmit('uloz', 'Zmeň')
         ->setHtmlAttribute('class', 'btn btn-success')
         ->onClick[] = [$this, 'formSubmitted'];
    $form->addSubmit('cancel', 'Cancel')
         ->setHtmlAttribute('class', 'btn btn-default')
         ->setHtmlAttribute('data-dismiss', 'modal')
         ->setHtmlAttribute('aria-label', 'Close')
         ->setValidationScope([]);
		return $form;
	}
  
  /** 
   * Spracovanie formulara pre zmenu nadpisov.
   * @param \Nette\Forms\Controls\SubmitButton $button Data formulara */
  public function formSubmitted(\Nette\Forms\Controls\SubmitButton $button) {
		$values = $button->getForm()->getValues(); 	//Nacitanie hodnot formulara
    try {
      foreach ($this->lang as $j) {
        $h = $this->hlavne_menu_lang->findOneBy(['id_hlavne_menu'=>$values->id_hlavne_menu, 'id_lang' => $j->id]);
        $h1part2 = $values->{$h->lang->skratka.'_h1part2'};
        $data = [
          'menu_name' => $values->{$h->lang->skratka.'_menu_name'},
          'h1part2' => strlen($h1part2) ? $h1part2 : null,
          'view_name' => $values->{$h->lang->skratka.'_view_name'},
        ];
        $this->hlavne_menu_lang->uloz($data, $h->id);
      }
		} catch (Database\DriverException $e) {
			$button->addError($e->getMessage());
		}
  }
}