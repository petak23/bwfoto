<?phpnamespace App\FrontModule\Components\Clanky;use DbTable;use Language_support;use Nette;/** * Komponenta pre zobrazenie aktualnych projektov pre FRONT modul *  * Posledna zmena(last change): 27.07.2017 * * @author Ing. Peter VOJTECH ml <petak23@gmail.com> * @copyright Copyright (c) 2012 - 2017 Ing. Peter VOJTECH ml. * @license * @link http://petak23.echo-msz.eu * @version 1.0.2 */class AktualnyProjektControl extends Nette\Application\UI\Control {    /** @var Language_support\LanguageMain */  private $texts;  /** @var DbTable\Hlavne_menu_lang */  private $hlavne_menu_lang;  /** @var string $avatar_path  Cesta k titulnemu obrazku clanku */  private $avatar_path = "";  /**   * @param DbTable\Hlavne_menu_lang $hlavne_menu_lang   * @param Language_support\LanguageMain $texts */  public function __construct(DbTable\Hlavne_menu_lang $hlavne_menu_lang, Language_support\LanguageMain $texts) {    parent::__construct();    $this->hlavne_menu_lang = $hlavne_menu_lang;    $this->texts = $texts;  }    /**    * Nastavenie jazyka    * @param int|string $language_id jazyk    * @return \App\FrontModule\Components\Clanky\AktualnyProjektControl */  public function setLanguage($language) {    $this->texts->setLanguage($language);    return $this;  }  /**    * Nastavenie cesty k obrazku   * @param string $avatar_path Cesta k titulnemu obrazku clanku   * @return \App\FrontModule\Components\Clanky\AktualnyProjektControl */  public function setAvatarPath($avatar_path) {    $this->avatar_path = $avatar_path;    return $this;  }    /** Render funkcia pre vypisanie odkazu na clanok    * @see Nette\Application\Control#render()   */  public function render() {     $this->template->setFile(__DIR__ . '/AktualnyProjekt.latte');    $this->template->clanok = $this->hlavne_menu_lang->findBy(['hlavne_menu.aktualny_projekt'=>1, 'id_lang'=>$this->texts->language_id]);    $this->template->texts = $this->texts;    $this->template->avatar_path = $this->avatar_path;    $this->template->render();  }}interface IAktualnyProjektControl {  /** @return AktualnyProjektControl */  function create();}