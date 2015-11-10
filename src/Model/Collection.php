<?php

namespace Platformsh\Client\Model;

use GuzzleHttp\ClientInterface;

/**
 * A class wrapping multiple resources.
 */
class Collection extends Resource implements \Iterator
{
    protected $resourceClass;

    /**
     * {@inheritdoc}
     *
     * @param string $className
     */
    public function __construct(array $data, $baseUrl, ClientInterface $client, $className)
    {
        parent::__construct($data, $baseUrl, $client);
        $this->setResourceClass($className);
    }

    /**
     * @param string $className
     *
     * @internal
     */
    public function setResourceClass($className)
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Class not found: $className");
        }

        $this->resourceClass = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $values)
    {
        throw new \BadMethodCallException("Cannot update() a Collection.");
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        throw new \BadMethodCallException("Cannot delete() a Collection.");
    }

    /**
     * Wrap an array of data in the correct resource class.
     *
     * @param array $data
     *
     * @return Resource
     */
    protected function wrap(array $data)
    {
        $className = $this->resourceClass;

        return new $className($data, $this->baseUrl, $this->client);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $current = current($this->data);

        return $current ? $this->wrap($current) : false;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        next($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return current($this->data) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->data);
    }
}
