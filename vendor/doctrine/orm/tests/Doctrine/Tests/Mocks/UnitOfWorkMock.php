<?php

namespace Doctrine\Tests\Mocks;

/**
 * Mock class for UnitOfWork.
 */
class UnitOfWorkMock extends \Doctrine\ORM\UnitOfWork
{
    /**
     * @var array
     */
    private $_mockDataChangeSets = array();

    /**
     * @var array|null
     */
    private $_persisterMock;

    /**
     * {@inheritdoc}
     */
    public function getEntityPersister($entityName)
    {
        return isset($this->_persisterMock[$entityName]) ?
                $this->_persisterMock[$entityName] : parent::getEntityPersister($entityName);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityChangeSet($entity)
    {
        $oid = spl_object_hash($entity);
        return isset($this->_mockDataChangeSets[$oid]) ?
                $this->_mockDataChangeSets[$oid] : parent::getEntityChangeSet($entity);
    }

    /* MOCK API */

    /**
     * Sets a (mock) persister for an entity class that will be returned when
     * getEntityPersister() is invoked for that class.
     *
     * @param string                                        $entityName
     * @param \Doctrine\ORM\Persisters\BasicEntityPersister $persister
     *
     * @return void
     */
    public function setEntityPersister($entityName, $persister)
    {
        $this->_persisterMock[$entityName] = $persister;
    }

    /**
     * @param object $entity
     * @param array  $mockChangeSet
     *
     * @return void
     */
    public function setDataChangeSet($entity, array $mockChangeSet)
    {
        $this->_mockDataChangeSets[spl_object_hash($entity)] = $mockChangeSet;
    }

    /**
     * @param object $entity
     * @param mixed  $state
     *
     * @return void
     */
    public function setEntityState($entity, $state)
    {
        $this->_entityStates[spl_object_hash($entity)] = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginalEntityData($entity, array $originalData)
    {
        $this->_originalEntityData[spl_object_hash($entity)] = $originalData;
    }
}
