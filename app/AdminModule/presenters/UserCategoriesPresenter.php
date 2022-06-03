<?php

namespace App\AdminModule\Presenters;

use DbTable;
use Nette\Database\DriverException;

/**
 * Prezenter pre spravu kategorii uzivatelov.
 * 
 * Posledna zmena(last change): 03.06.2022
 * @actions default
 *
 *	Modul: ADMIN
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.5
 */
class UserCategoriesPresenter extends BasePresenter
{
  // --- Models ---
  /** @var DbTable\User_categories @inject */
  public $user_categories;


  public function renderDefault(): void
  {
    $this->template->user_categories = $this->user_categories->findAll();
  }

  /*public function createComponentCategoriesGrid($name)
  {
    if ($this->user->isAllowed($this->name, 'edit')) {
      $grid->addInlineEdit()->setClass('btn btn-xs btn-info ajax')
        ->setIcon('pencil-alt')
        ->onControlAdd[] = function ($container) {
        $container->addText('id', '')->setAttribute('readonly');
        $container->addText('name', '');
        $container->addText('shortcut', '');
      };
      $grid->getInlineEdit()->onSetDefaults[] = function ($container, $item) {
        $container->setDefaults([
          'id' => $item->id,
          'name' => $item->name,
          'shortcut' => $item->shortcut,
        ]);
      };
      $grid->getInlineEdit()->onSubmit[] = function ($id, $values) use ($sthis) {
        $values->offsetSet('id', $id);
        $sthis->user_categories->saveCategori($values);
        $sthis->flashMessage("Kategória $values->name bola v poriadku uložená!", 'success');
        $sthis->redrawControl('flashes');
      };
    }

    if ($this->user->isAllowed($this->name, 'add')) {
      $grid->addInlineAdd()->setPositionTop(FALSE)->setText('Pridať kategóriu')->setClass('btn btn-xs btn-success')
        ->onControlAdd[] = function ($container) {
        $container->addText('id', '')->setAttribute('readonly');
        $container->addText('name', '');
        $container->addText('shortcut', '');
      };
      $grid->getInlineAdd()->onSubmit[] = function ($values) use ($sthis) {
        $values->offsetSet('id', 0);
        $sthis->user_categories->saveCategori($values);
        $sthis->flashMessage("Kategória $values->name bola v poriadku pridaná!<br> Do tabuľky je vložená na koniec.", 'success,n');
        $sthis->redrawControl('flashes');
        //        $sthis->redrawControl('categoriesGrid-grid');
      };
    }
  }*/

  /** 
   * Funkcia pre spracovanie signálu vymazavania kategórie
   * @param int $id Id kategorie */
  function handleConfirmedDeleteCategory(int $id)
  {
    try {
      $this->user_categories->zmaz($id);
      $this->flashMessage('Kategória bola zmazaná!', 'success');
    } catch (DriverException $e) {
      $this->flashMessage('Došlo k chybe pri vymazávaní. Skúste neskôr znovu...' . $e->getMessage(), 'danger');
    }
    if ($this->isAjax()) {
      $this->redrawControl();
    } else {
      $this->redirect('UserCategories:');
    }
  }
}
