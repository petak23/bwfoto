<?php

namespace App\ApiModule\Components\Menu;

use Nette;

/**
 * Komponenta na vytvorenie menu
 * Posledna zmena 21.08.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.1.3
 */
class Menu extends Nette\Application\UI\Control
{
	var $rootNode; // = new MenuItem();
	var $separateMenuLevel;
	protected $_selected;
	var $allNodes = [];
	protected $_path = null;
	public $idCounter = 0;
	protected $nastav = [
		"level" => 0,
		"templateType" => "tree",
		"nadpis" => FALSE,
		"divClass" => FALSE,
		"avatar" => "",
		"anotacia" => FALSE,
		"text_title_image" => "Titulný obrázok",
		"article_avatar_view_in" => 0,
		"separator" => "|",
	];
	private $nastavenie;

	public function __construct(Nette\ComponentModel\IContainer $parent = NULL, $name = NULL)
	{
		$this->rootNode = new MenuNode();
		$this->rootNode->menu = $this;
		$this->rootNode->isRootNode = true;
	}

	public function setNastavenie($nastavenie)
	{
		$this->nastavenie = $nastavenie;
	}

	public function setSelected($node)
	{
		if (is_scalar($node)) {
			if (!isset($this->allNodes[$node])) {
				return;
			}
			$node = $this->allNodes[$node];
		};
		$this->_path = null;
		$this->_selected = $node;
	}

	public function getSelected()
	{
		return $this->_selected;
	}

	public function setTextTitleImage($text)
	{
		$this->nastav["text_title_image"] = $text;
	}

	public function getPath()
	{
		if (!$this->_path) {
			$this->_path = $this->makePath();
		}
		return $this->_path;
	}

	function makePath()
	{
		$node = $this->getSelected();
		$path = [];
		while ($node && ($node != $this->rootNode)) {
			$path[] = $node;
			$node = $node->parent;
		};
		$path = array_reverse($path);
		return $path;
	}

	public function fromTable($data, $setupNode)
	{
		$nodes = [];
		foreach ($data as $row) {
			$node = new MenuNode;
			$parentId = $setupNode($node, $row);
			$nodes[$parentId][] = $node;
		}
		$this->linkNodes(null, $nodes);
	}

	protected function linkNodes($parentId, &$nodes)
	{
		if (isset($nodes[$parentId])) {
			foreach ($nodes[$parentId] as $node) {
				if ($parentId) {
					$this->allNodes[$parentId]->add($node);
				} else {
					$this->rootNode->add($node);
				}
				$this->linkNodes($node->id, $nodes);
			}
		}
	}

	public function byId($id)
	{
		return $this->allNodes[$id];
	}

	public function selectByUrl($url)
	{
		foreach ($this->allNodes as $node) {
			if ($url == $node->link) {
				$this->setSelected($node);
			}
		}
	}

	public function getTreeMenu()
	{
		$out = [];
		foreach ($this->rootNode->nodes as $node) {
			$out[$node->name] = $this->_node($node);
		}
		return $out;
	}

	private function _node($node)
	{
		$out = [];
		if (count($node->nodes)) {
			foreach ($node->nodes as $no) {
				$out[$no->id] = $no->name;
			}
		}
		return $out;
	}

	public function getFullTreeMenu()
	{
		$out = [];
		foreach ($this->rootNode->nodes as $node) {
			$out[$node->name] = $this->_fnode($node, 0);
		}
		return $out;
	}

	private function _fnode($node, $level)
	{
		$out = [];
		$l = "";
		for ($i = 0; $i < $level; $i++) {
			$l .= "-";
		}
		if (count($node->nodes)) {
			foreach ($node->nodes as $no) {
				$out[$no->id] = (strlen($l) ? $l . " " : "") . $no->name;
				if (count($no->nodes)) {
					$level++;
					$out += $this->_fnode($no, $level);
					$level--;
				}
			}
		}
		return $out;
	}

	public function getApiMenu()
	{
		$out = [];
		$level = 0;
		foreach ($this->rootNode->nodes as $node) {
			$out[$node->id] = [
				'id'	 		=> $node->id,
				'name' 		=> $node->name,
				'tooltip' => $node->tooltip,
				'view_name' => $node->view_name,
				'anotacia'	=> $node->anotacia != null && strlen($node->anotacia) > 2 ? $node->anotacia : null,
				'datum_platnosti' => $node->datum_platnosti,
				'children' => $this->_anode($node, $level + 1),
				//'link' 		=> $node->link,
				'vue_link'=> $node->vue_link,
				'class' 	=> $node->node_class,
				'avatar'	=> $node->avatar,
			];
		}
		return $out;
	}

	private function _anode($node, $level)
	{
		$out = [];
		if (count($node->nodes)) {
			foreach ($node->nodes as $no) {
				$subn = null;
				if (count($no->nodes)) {
					$level++;
					$subn = $this->_anode($no, $level);
					$level--;
				}
				$out[$no->id] = [
					'id'	 		=> $no->id,
					'name' 		=> $no->name,
					'tooltip' => $no->tooltip,
					'view_name' => $no->view_name,
					'anotacia'	=> $no->anotacia != null && strlen($no->anotacia) > 2 ? $no->anotacia : null,
					'datum_platnosti' => $no->datum_platnosti,
					'children' => $subn,
					//'link' 		=> $no->link,
					'vue_link'=> $no->vue_link,
					'class' 	=> $no->node_class,
					'avatar'	=> $no->avatar,
				];
				unset($subn);
			}
		}
		return $out;
	}
}

class MenuNode
{
	use Nette\SmartObject;

	var $name;
	var $tooltip;
	var $view_name;
	var $avatar;
	var $anotacia;
	var $node_class;
	var $datum_platnosti;
	//var $link;	//Odkaz na polozku
	var $vue_link; // Odkaz pre vuerouter
	var $nodes = [];
	var $parent;
	var $id;
	var $menu;
	var $isRootNode = false;
	var $level;

	public function Add($node)
	{

		if (is_array($node)) {
			$newNode = new MenuNode;
			foreach ($node as $k => $v) {
				$newNode->{$k} = $v;
			}
			$node = $newNode;
		}
		$node->parent = $this;
		$node->menu = $this->menu;
		if (!$node->id) {
			$node->id = '__auto_id_' . $this->menu->idCounter++;
		}
		$this->nodes[] = $node;
		$this->menu->allNodes[$node->id] = $node;
		return $node;
	}

	public function getItemClass()
	{
		$out = "";
		if ($this == $this->menu->getSelected()) {
			$out .= ' selected';
		} else if (in_array($this, $this->menu->getPath())) {
			$out .= ' in-path';
		}
		return $out;
	}
}
