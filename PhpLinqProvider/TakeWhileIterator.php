<?php

namespace linq;

class TakeWhileIterator extends LinqProvider
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
    
    protected function execute()
    {
        $i = 0;
        $predicate = $this->predicate;
        foreach ($this->iterator as $item)
        {
            if ($predicate($item, $i)) yield $item;
            else break;
            $i++;
        }
    }
}
