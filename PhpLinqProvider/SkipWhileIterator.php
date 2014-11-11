<?php

namespace linq;

class SkipWhileIterator extends LinqProvider
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
        $yielding = false;
        $predicate = $this->predicate;
        foreach ($this->iterator->execute() as $item)
        {
            if (!$yielding && !$predicate($item, $i))
            {
                $yielding = true;
            }
            if ($yielding) yield $item;
            $i++;
        }
    }
}
