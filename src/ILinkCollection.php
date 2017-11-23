<?php

namespace Grapesc\GrapeFluid\LinkCollector;


/**
 * @author Kulíšek Patrik <kulisek@grapesc.cz>
 */
interface ILinkCollection
{

	/**
	 * Metoda getLinks musi vratit pole viz:
	 * [ "Title" => ["Link", "JSON Args" ] ]
	 *
	 * @return array
	 */
	public function getLinks();
}
