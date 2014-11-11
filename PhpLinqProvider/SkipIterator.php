<?php

namespace linq;

class SkipIterator extends LinqProvider
{
    /**
     * @var LinqProvider
     */
    private $iterator;
    /**
     * @var int
     */
    private $count;
    
    public function __construct(LinqProvider $iterator, $count)
    {
        $this->iterator = $iterator;
        $this->count = $count;
    }
    
    protected function execute()
    {
        $count = $this->count;
        foreach ($this->iterator->execute() as $item)
        {
            if ($count > 0)
            {
                $count--;
                continue;
            }
            yield $item;
        }
    }
}
