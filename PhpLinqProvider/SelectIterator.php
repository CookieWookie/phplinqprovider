<?php

namespace linq;

class SelectIterator extends LinqProvider
{
    private $predicate;
    /**
     * @var LinqProvider
     */
    private $iterator;
    
    public function __construct(LinqProvider $iterator, Closure $predicate)
    {
        $this->predicate = $predicate;
        $this->iterator = $iterator;
    }
    
    /**
     * @return Generator
     */
    public function execute()
    {
        $i = 0;
        $predicate = $this->predicate;
        foreach ($this->iterator->execute() as $item)
        {
            yield $predicate($item, $i++);
        }
    }
}
