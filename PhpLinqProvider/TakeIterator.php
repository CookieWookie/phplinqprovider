<?php

namespace linq;

class TakeIterator extends LinqProvider
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
        $count = 0;
        foreach ($this->iterator->execute() as $item)
        {
            if ($count++ < $this->count)
            {
                yield $item;
                continue;
            }
            break;
        }
    }
}
