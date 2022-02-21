<?php

namespace Grapesc\GrapeFluid\LinkCollector;

use Nette\DI\Container;


/**
 * @author Kulíšek Patrik <kulisek@grapesc.cz>
 */
class LinkCollector
{

	/** @var ILinkCollection[] */
	private $collections;

	/** @var Container */
	private $container;


	/**
	 * LinkCollector constructor.
	 * @param array $collections
	 * @param Container $container
	 */
	public function __construct(array $collections = [], Container $container = null)
	{
		$this->collections = $collections;
		$this->container   = $container;
		$this->generateCollections();
	}


	/**
	 * Vygeneruje seznam kolekci dle vsech nakonfigurovanich Collection
	 * Collection musi implementovat \Grapesc\GrapeFluid\LinkCollector\ILinkCollection
	 */
	public function generateCollections()
	{
		$collections = $this->collections;
		$this->collections = [];

		foreach ($collections as $category => $detail)
		{
			foreach ($detail['class'] as $class) {
				if (!in_array(ILinkCollection::class, class_implements($class))) {
					throw new \LogicException("Collection must implement 'Grapesc\GrapeFluid\LinkCollector\ILinkCollection' interface");
				}
				if (!isset($detail['name'])) {
					$detail['name'] = $category;
				}
				$detail['icon'] = isset($detail['icon']) ? $detail['icon'] : "";

				/** @var ILinkCollection $collection */
				$collection = new $class;
				$this->container->callInjects($collection);
				$this->addCollection($category, $detail['name'], $detail['icon'], $collection->getLinks());
			}
		}
	}


	/**
	 * @param $name
	 * @param $label
	 * @param $icon
	 * @param array $collection
	 */
	private function addCollection($name, $label, $icon, array $collection)
	{
		if (!isset($this->collections[$name])) {
			$this->collections[$name] = [
				"label"      => $label,
				"icon"       => $icon,
				"collection" => $collection,
			];
		} else {
			$this->collections[$name]['collection'] = array_merge($this->collections[$name]['collection'], $collection);
		}
	}


	/**
	 * Vrati pole vsech kolekci rozdelenych do kategorii
	 *
	 * @return array
	 */
	public function getAllCollections()
	{
		return $this->collections;
	}


	/**
	 * Vrati kolekci zadane kategorie
	 * Pokud kategorie neexistuje, vrati prazdne pole
	 *
	 * @param $category
	 * @return array
	 */
	public function getCollection($category)
	{
		return (isset($this->collections[$category]) ? $this->collections[$category] : []);
	}

}