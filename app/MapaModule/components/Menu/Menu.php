<?php
namespace App\MapaModule\Components\Menu;
use Nette;

/**
 * Komponenta pre vytvorenie menu
 * Posledna zmena(last change): 22.03.2021
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.5
 */
class Menu extends Nette\Application\UI\Control {
	var $rootNode;
	var $allNodes = [];
	protected $_path = null;
	public $idCounter = 0;

	public function __construct() {
		$this->rootNode = new MenuNode();
		$this->rootNode->menu = $this;
		$this->rootNode->isRootNode = true;
	}

	public function render($templateFile) {
		$this->template->startNode = $this->allNodes;
		$this->template->setFile(dirname(__FILE__) . "/" . $templateFile);
		$this->template->render();
	}
  
  /**
   * Render pre urllist.txt */
  public function renderUrllist() {	
    $this->render('Urllist.latte');	
  }
  
  /**
   * Render pre sitemap.xml   */
	public function renderSitemap() {	
    $this->render('Sitemap.latte');	
  }

	public function fromTable($data, $setupNode) {
		$nodes = [];
		foreach($data as $row) {
			$node = new MenuNode;
			$parentId = $setupNode($node, $row);
			$nodes[$parentId][] = $node;
		}
		$this->linkNodes(null, $nodes);
	}

	protected function linkNodes($parentId, &$nodes) {
		if (isset($nodes[$parentId])) {
			foreach($nodes[$parentId] as $node) {
				if ($parentId) {
					$this->allNodes[$parentId]->add($node);
				} else {
					$this->rootNode->add($node);
				}
				$this->linkNodes($node->id, $nodes);
			}
		}
	}
}

class MenuNode {
  use Nette\SmartObject;
  
	var $link;	//Odkaz na polozku
	var $nodes = [];
	var $parent;
	var $id;
	var $menu;
	var $isRootNode = false;

	public function Add($node) {
		if (is_array($node)) {
			$newNode = new MenuNode;
			foreach($node as $k => $v) {
				$newNode->{$k} = $v;
			}
			$node = $newNode;
		}
		$node->parent = $this;
		$node->menu = $this->menu;
		if (!$node->id) {
			$node->id = '__auto_id_'.$this->menu->idCounter++;
		}
		$this->nodes[] = $node;
		$this->menu->allNodes[$node->id] = $node;
		return $node;
	}
}