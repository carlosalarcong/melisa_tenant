<?php

namespace Rebsol\RecaudacionBundle\Repository;

use Doctrine\ORM\EntityManager;
use Rebsol\HermesBundle\Repository\DefaultRepository as DefaultRepositoryHermesBundle;

/**
 * @author wmunoz
 * @version 1.0.0
 * Fecha Creación: 30-09-2015
 */
class DefaultRepository extends DefaultRepositoryHermesBundle {

	/**
	 *
	 * @var EntityManager
	 */
	protected $_em;

	public function __construct(EntityManager $entityManager) {
		$this->_em = $entityManager;
	}

}
