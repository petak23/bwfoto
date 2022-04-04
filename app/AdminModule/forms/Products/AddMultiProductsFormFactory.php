<?php
declare(strict_types=1);

namespace App\AdminModule\Forms\Products;

use DbTable;
use Nette\Database;
use Nette\Application\UI\Form;
use Nette\Security\User;

/**
 * Formular a jeho spracovanie pre pridanie viacerich produktov polozky.
 * Posledna zmena 04.04.2022
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.8
 */
class AddMultiProductsFormFactory {
  
  /** @var DbTable\Products */
	private $products;
  /** @var array */
  private $products_settings;
  /** @var int */
  private $id_user_main;
  /** @var string */
  private $products_dir;
  
  public function __construct(string $dir_to_products,
                              DbTable\Products $products, 
                              DbTable\Udaje $udaje, 
                              User $user) {
    $this->products = $products;
    $this->products_settings = $udaje->findBy(['id_druh'=>8])->fetchPairs('nazov', 'text');
    $this->id_user_main = $user->getId();
    $this->products_dir = $dir_to_products;
	}
  
  /**
   * Formular pre pridanie produktu a editaciu polozky.
   * @param Database\Table\ActiveRow $clanok Clanok, ku ktoremu su produkty 
   * @return Form */
  public function create(Database\Table\ActiveRow $clanok): Form  {
    $form = new Form();
		$form->addProtection();
    $form->addSubmit('ulozz', 'Ukonč')
         ->setHtmlAttribute('class', 'btn btn-success')
         ->onClick[] = [$this, 'productsFormSubmitted'];
    $form->addFileUpload("uploader")
         ->setMaxFiles((int)$this->products_settings["product_max_upload_files"])
         ->setFileFilter('\Zet\FileUpload\Filter\ImageFilter')
         ->setParams(['products_settings' => $this->products_settings,
                      'panorama' => $clanok->hlavne_menu->id_hlavne_menu_template == 6,
                      'main_data' => [ 
                          'id_hlavne_menu'	 	=> $clanok->id_hlavne_menu,
                          'id_user_main'      => $this->id_user_main,
                          'id_user_roles'     => $clanok->hlavne_menu->id_user_roles,
                        ]
                     ])
         ->setOption('description', 
                 sprintf('Max veľkosť obrázku produktu určená serverom je: %s. Maximálny počet nahrávaných obrázkov je %s.', 
                         $form['uploader']->getFileSizeString(), 
                         $form['uploader']->getMaxFiles()
                        )
                );
		$form->addSubmit('ulozk', 'Ukonč')
         ->setHtmlAttribute('class', 'btn btn-success')
         ->onClick[] = [$this, 'productsFormSubmitted'];
		return $form;
	}
  
  /** 
   * Spracovanie formulara. 
   * @throws Database\DriverException   */
  public function productsFormSubmitted(Form $form, $values): void {
    $uploader = $values->uploader;
    if ($uploader != null) { // Presun suborov z tmp adresara
      foreach ($uploader as $k=>$v) {
        $prod = $this->products->find($v);
        $image_name = str_replace('tmp/', "", $prod->main_file);
        $thumb_name = str_replace('tmp/', "", $prod->thumb_file);
        rename($prod->main_file, $image_name);
        rename($prod->thumb_file, $thumb_name);
        $prod->update([
            "main_file" => $image_name,
            "thumb_file" => $thumb_name,
            "saved" => 1,
        ]);
      }
    }
    // Vymazanie tmp adresara ak tam nieco zostalo - napr. chybne prenosy
    foreach (glob($this->products_dir."tmp/*", GLOB_NOSORT) as $file) {
      @unlink($file);
    }
  }
}