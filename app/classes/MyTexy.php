<?php
namespace TexylaExample;

use Nette\Utils\Strings;
use Latte;

/**
 * My Texy
 *
 * @author Jan Marek
 * @license MIT
 */
class MyTexy extends \Texy {
  /**
	 * @return Texy
	 */
	public static function createTexy() {
		$texy = new Texy();
		$texy->encoding = 'utf-8';
		$texy->setOutputMode(\Texy::HTML5);
		// from https://github.com/nette/web-content/blob/convertor/src/Wiki/Convertor.php
		$texy->linkModule->root = '';
		$texy->alignClasses['left'] = 'left';
		$texy->alignClasses['right'] = 'right';
		$texy->emoticonModule->class = 'smiley';
		$texy->headingModule->top = 1;
		$texy->headingModule->generateID = TRUE;
		$texy->tabWidth = 4;
		$texy->typographyModule->locale = 'cs'; //en ?
		$texy->tableModule->evenClass = 'alt';
		$texy->dtd['body'][1]['style'] = TRUE;
		$texy->allowed['longwords'] = FALSE;
		$texy->allowed['block/html'] = FALSE;
		$texy->phraseModule->tags['phrase/strong'] = 'b';
		$texy->phraseModule->tags['phrase/em'] = 'i';
		$texy->phraseModule->tags['phrase/em-alt'] = 'i';
		$texy->phraseModule->tags['phrase/acronym'] = 'abbr';
		$texy->phraseModule->tags['phrase/acronym-alt'] = 'abbr';
		$texy->addHandler('block', array('TexyFactory', 'blockHandler'));
		return $texy;
	}
  
	/**
	 * @param \Nette\Http\Request $httpRequest
	 */
	public function __construct(\Nette\Http\Request $httpRequest)
	{
		parent::__construct();

		// output
		$this->setOutputMode(self::HTML5);
		$this->htmlOutputModule->removeOptional = false;
		self::$advertisingNotice = false;

		// headings
        $this->headingModule->balancing = \TexyHeadingModule::FIXED;

		// phrases
		$this->allowed['phrase/ins'] = true;   // ++inserted++
		$this->allowed['phrase/del'] = true;   // --deleted--
		$this->allowed['phrase/sup'] = true;   // ^^superscript^^
		$this->allowed['phrase/sub'] = true;   // __subscript__
		$this->allowed['phrase/cite'] = true;   // ~~cite~~
		$this->allowed['deprecated/codeswitch'] = true; // `=code

		// images
		$this->imageModule->fileRoot = __DIR__ . "/../../www/files";
		$this->imageModule->root = $httpRequest->url->baseUrl . "www/files/";

		// flash, youtube.com, stream.cz, gravatar handlers
		$this->addHandler('image', array($this, 'youtubeHandler'));
		$this->addHandler('image', array($this, 'streamHandler'));
		$this->addHandler('image', array($this, 'flashHandler'));
		$this->addHandler("phrase", array($this, "netteLink"));
		$this->addHandler('image', array($this, 'gravatarHandler'));
	}



	/**
	 * Template factory
   * @param string $name
   * @param array $params
   * @return Template
   */
	private function createTemplate($name, $params) {
		$template = new Latte\Engine();
		return $template->renderToString($name, $params);
	}

	/**
	 * @param TexyHandlerInvocation  handler invocation
	 * @param string
	 * @param string
	 * @param TexyModifier
	 * @param TexyLink
	 * @return TexyHtml|string|FALSE
	 */
	public function netteLink($invocation, $phrase, $content, $modifier, $link) {
		// is there link?
		if (!$link) return $invocation->proceed();

		$url = $link->URL;

		if (Strings::startsWith($url, "plink://")) {
			$url = substr($url, 8);
			list($presenter, $params) = explode("?", $url, 2);

			$arr = array();

			if ($params) {
				parse_str($params, $arr);
			}

			$link->URL = $this->presenter->link($presenter, $arr);
		}

		return $invocation->proceed();
	}



	/**
	 * YouTube handler for images
	 *
	 * @example [* youtube:JG7I5IF6 *]
	 *
	 * @param TexyHandlerInvocation  handler invocation
	 * @param TexyImage
	 * @param TexyLink
	 * @return TexyHtml|string|FALSE
	 */
	public function youtubeHandler($invocation, $image, $link) {
		$parts = explode(':', $image->URL, 2);

		if (count($parts) !== 2 || $parts[0] !== "youtube") {
			return $invocation->proceed();
		}
    
		$params["id"] = $parts[1];
		if ($image->width) $params["width"] = $image->width;
		if ($image->height) $params["height"] = $image->height;

		return $this->protect($this->createTemplate(__DIR__ . "/../templates/inc/@youtube.latte", $params), \Texy::CONTENT_BLOCK);
	}



	/**
	 * Flash handler for images
	 *
	 * @example [* flash.swf 200x150 .(alternative content) *]
	 *
	 * @param TexyHandlerInvocation  handler invocation
	 * @param TexyImage
	 * @param TexyLink
	 * @return TexyHtml|string|FALSE
	 */
	public function flashHandler($invocation, $image, $link) {
		if (!Strings::endsWith($image->URL, ".swf")) {
			return $invocation->proceed();
		}

		$params = ["url"    => \Texy::prependRoot($image->URL, $this->imageModule->root),
               "width"  => $image->width,
               "height" => $image->height];
		if ($image->modifier->title) $params["title"] = $image->modifier->title;

		return $this->protect($this->createTemplate(__DIR__ . "/../templates/inc/@flash.latte", $params), \Texy::CONTENT_BLOCK);
	}



	/**
	 * User handler for images
	 *
	 * @example [* stream:98GDAS675G *]
	 *
	 * @param TexyHandlerInvocation  handler invocation
	 * @param TexyImage
	 * @param TexyLink
	 * @return TexyHtml|string|FALSE
	 */
	public function streamHandler($invocation, $image, $link) {
		$parts = explode(':', $image->URL, 2);

		if (count($parts) !== 2 || $parts[0] !== "stream") {
			return $invocation->proceed();
		}

		$params["id"] = $parts[1];
		if ($image->width) $params["width"] = $image->width;
		if ($image->height) $params["height"] = $image->height;

		return $this->protect($this->createTemplate(__DIR__ . "/../templates/inc/@stream.latte", $params), \Texy::CONTENT_BLOCK);
	}



	/**
	 * Gravatar handler for images
	 *
	 * @example [* gravatar:user@example.com *]
	 *
	 * @param TexyHandlerInvocation  handler invocation
	 * @param TexyImage
	 * @param TexyLink
	 * @return TexyHtml|string|FALSE
	 */
	public function gravatarHandler($invocation, $image, $link) {
		$parts = explode(':', $image->URL, 2);

		if (count($parts) !== 2 || $parts[0] !== "gravatar") {
			return $invocation->proceed();
		}

		$params["email"] = $parts[1];
		if ($image->width) $params["width"] = $image->width;
		if ($image->height) $params["height"] = $image->height;

		return $this->protect($this->createTemplate(__DIR__ . "/../templates/inc/@gravatar.latte", $params), \Texy::CONTENT_BLOCK);
	}

}