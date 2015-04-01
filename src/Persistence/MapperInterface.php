<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Persistence;

use Zend\Paginator\Paginator;

interface MapperInterface
{
    /**
     * @return string
     */
    public function getClassName();

    /**
     * Hydrate $object with the provided $data
     *
     * @param  array    $data
     * @param  object   $object
     * @return object
     */
    public function hydrate(array $data, $object);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @return Paginator
     */
    public function getPaginator(array $criteria = [], array $orderBy = null);
    
    /**
     * @param mixed $id
     * @return object|null
     */
    public function find($id);

    /**
     * @return object[]
     */
    public function findAll();

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param number $limit
     * @param number $offset
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param array $criteria
     * @return object
     */
    public function findOneBy(array $criteria);

    /**
     * @param object $object
     * @return self
     */
    public function add($object);

    /**
     * @param object $object
     * @return self
     */
    public function update($object);

    /**
     * @param object $object
     * @return self
     */
    public function remove($object);

    /**
     * @param object|null $object
     * @return self
     */
    public function save($object = null);
}
