<?php

namespace linq;

class ConcatIterator extends LinqProvider
{
    /**
     * @var LinqProvider
     */
    private $first;
    /**
     * @var LinqProvider
     */
    private $second;
    
    public function __construct(LinqProvider $first, array $second)
    {
        $this->first = $first;
        $this->second = LinqProvider::from($second);
    }   
    
    /**
     * Summary of getIterator
     * @return mixed
     */
    public function getIterator()
    {
        return $this->execute();
    }
    
    protected function execute()
    {
        foreach($this->first->execute() as $item)
        {
            yield $item;
        }
        foreach($this->second->execute() as $item)
        {
            yield $item;
        }
    }
}
