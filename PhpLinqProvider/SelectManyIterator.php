<?php

namespace linq;

class SelectManyIterator extends LinqProvider
{
    /**
     * @var LinqProvider
     */
    private $iterator;
    /**
     * @var Closure
     */
    private $predicate;
    
    public function __construct(LinqProvider $iterator, Closure $predicate)
    {
        $this->iterator = $iterator;
        $this->predicate = $predicate;
    }
    
    public function execute()
    {
        $i = 0;
        $predicate = $this->predicate;
        foreach ($this->iterator->execute() as $collection)
        {
            foreach ($predicate($collection, $i++) as $item)
            {
                yield $item;
            }
        }
    }
}
