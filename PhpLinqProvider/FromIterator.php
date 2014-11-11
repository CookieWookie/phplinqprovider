<?php

namespace linq;

class FromIterator extends LinqProvider
{
    /**
     * @var array
     */
    private $source;
    
    public function __construct(array $array)
    {
        $this->source = $array;
    }
    
    /**
     * Returns the number of elements in a sequence.
     * @return int
     */
    public function count()
    {
        return count($this->source);
    }
    
    /**
     * Returns the first element of a sequence. 
     * @return mixed
     */
    public function first()
    {
        $temp = $this->source;
        return reset($temp);
    }
    
    /**
     * Returns the last element of sequence.
     * @return mixed
     */
    public function last()
    {
        $temp = $this->source;
        return end($temp);
    }
    /**
     * Executes the query and stores the result in array.
     * @return array
     */

    public function toArray()
    {
        return $this->source;
    }
    
    /**
     * @return Generator
     */
    protected function execute()
    {
        foreach($this->source as $item)
        {
            yield $item;
        }
    }
}
