<?phpnamespace App\AdminModule\Components\Article\TitleArticle;use Nette\Application\UI\Form;//use Nette\Security\User;use DbTable;/** * Formular a jeho spracovanie pre zmenu opravnenia nevlastnikov polozky. * Posledna zmena 01.06.2017 *  * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com> * @copyright  Copyright (c) 2012 - 2017 Ing. Peter VOJTECH ml. * @license * @link       http://petak23.echo-msz.eu * @version    1.0.0 */class ZmenOpravnenieNevlastnikovFormFactory {  /** @var DbTable\Hlavne_menu */	private $hlavne_menu;    /** @var array Hodnoty id=>nazov pre formulare z tabulky hlavne_menu_opravnenie*/	private $hlavne_menu_opravnenie;    /**   * @param DbTable\Hlavne_menu $hlavne_menu   * @param DbTable\Hlavne_menu_opravnenie $hlavne_menu_opravnenie */  public function __construct(DbTable\Hlavne_menu $hlavne_menu, DbTable\Hlavne_menu_opravnenie $hlavne_menu_opravnenie) {		$this->hlavne_menu = $hlavne_menu;    $this->hlavne_menu_opravnenie = $hlavne_menu_opravnenie->opravnenieForm();	}    /**   * Formular.   * @param int $id Id polozky v hlavnom menu   * @param int $id_hlavne_menu_opravnenie Sucasnopravnenie nevlastnikov   * @return Nette\Application\UI\Form */    public function create($id, $id_hlavne_menu_opravnenie)  {		$form = new Form();		$form->addProtection();    $form->addHidden("id", $id);    $form->addRadioList('id_hlavne_menu_opravnenie', 'Nová dĺžka sledovania ako novinky:', $this->hlavne_menu_opravnenie)         ->setDefaultValue($id_hlavne_menu_opravnenie)         ->setOption('description', 'Výberom sa pridajú oprávnenia aj pre užívateľov, ktorý nie sú vlastníci článku.');    $form->addSubmit('uloz', 'Zmeň')         ->setAttribute('class', 'btn btn-success')         ->onClick[] = [$this, 'formSubmitted'];    $form->addSubmit('cancel', 'Cancel')         ->setAttribute('class', 'btn btn-default')         ->setAttribute('data-dismiss', 'modal')         ->setAttribute('aria-label', 'Close')         ->setValidationScope(FALSE);		return $form;	}    /**    * Spracovanie formulara.   * @param Nette\Forms\Controls\SubmitButton $button Data formulara */  public function formSubmitted($button) {		$values = $button->getForm()->getValues(); 	//Nacitanie hodnot formulara    try {			$this->hlavne_menu->zmenOpravnenieNevlastnikov($values);		} catch (Database\DriverException $e) {			$button->addError($e->getMessage());		}  }}