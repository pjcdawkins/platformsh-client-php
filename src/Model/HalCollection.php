<?php

namespace Platformsh\Client\Model;

use GuzzleHttp\ClientInterface;

/**
 * A hypermedia collection.
 */
class HalCollection extends Collection
{
    protected $resources = [];

    public function __construct(array $data, $baseUrl, ClientInterface $client, $className)
    {
        parent::__construct($data, $baseUrl, $client, $className);

        foreach ($data as $key => $value) {
            if ($key[0] !== '_' && is_array($value)) {
                $resources = $value;
                break;
            }
        }
        if (!isset($resources)) {
            throw new \RuntimeException("Collection resources not found");
        }
        $this->resources = $resources;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->resources);
    }

    /**
     * Count all resources in the collection, ignoring pagination.
     *
     * @return int
     */
    public function countAll()
    {
        return isset($this->data['count']) ? $this->data['count'] : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $current = current($this->resources);

        return $current ? $this->wrap($current) : false;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        next($this->resources);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->resources);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return current($this->resources) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->resources);
    }
}
