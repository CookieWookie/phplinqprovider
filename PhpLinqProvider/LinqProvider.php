<?php

namespace linq;  

abstract class LinqProvider implements \IteratorAggregate
{
    /**
     * Applies an accumulator function over a sequence. The specified seed value is used as the initial accumulator value.
     * @param mixed $seed The initial accumulator value.
     * @param Closure $func An accumulator function to be invoked on each element.
     * @param Closure $resultSelector A function to transform the final accumulator value into the result value.
     * @return mixed
     */
    public function aggregateSeed($seed, Closure $func, Closure $resultSelector = null)
    {
        $accumulate = $seed;
        foreach ($this as $item)
        {
            $accumulate = $func($accumulate, $this);
        }
        if ($resultSelector != null) return $resultSelector($accumulate);
        return $accumulate;
    }
    
    /**
     * Determines whether all elements of a sequence satisfy a condition. 
     * @param Closure $predicate 
     * @return bool
     */
    public function all(Closure $predicate)
    {
        foreach ($this->execute() as $item)
        {
            if (!$predicate($item)) return false;
        }
        return true;
    }
    
    /**
     * Determines whether any element of a sequence satisfies a condition. 
     * @param Closure $predicate 
     * @return bool
     */
    public function any(Closure $predicate = null)
    {
        if ($predicate == null) $predicate = function ($item) { return true; };
        foreach ($this->execute() as $item)
        {
            if ($predicate($item)) return true;
        }
        return false;
    }
    
    /**
     * Computes the average of a sequence.
     * @throws InvalidOperationException 
     * @return float
     */
    public function average()
    {
        $sum = 0;
        $count = 0;
        foreach ($this as $item)
        {
            if (!is_numeric($item)) throw new InvalidOperationException();
            $sum += $item;
            $count++;
        }
        return $sum / $count;
    }
    
    /**
     * Concatenates two sequences.
     * @param array $second The sequence to concatenate to the first sequence.
     * @return LinqProvider
     */
    public function concat(array $second)
    {
        $iterator = new ConcatIterator($this, $second);
        return $iterator;
    }
    
    /**
     * Determines whether a sequence contains a specified element by using the specified comparer.
     * @param mixed $value 
     * @param Closure $comparer 
     * @return bool
     */
    public function contains($value, Closure $comparer = null)
    {
        if ($comparer == null) $comparer = function($left, $right) { return $left == $right; };
        foreach ($this as $item)
        {
            if ($comparer($value, $item)) return true;
        }
        return false;
    }
    
    /**
     * Returns the number of elements in a sequence.
     * @return int
     */
    public function count()
    {
        $count = 0;
        foreach ($this->execute() as $item)
        {
            $count++;
        }
        return $count;
    }
    
    /**
     * Returns the element at a specified index in a sequence.
     * @param int $index 
     * @throws ArgumentOutOfRangeException 
     * @return mixed
     */
    public function elementAt($index)
    {
        $i = 0;
        foreach($this as $item)
        {
            if ($i++ == $index) return $item;            
        }
        throw new ArgumentOutOfRangeException();
    }
    
    /**
     * Returns the element at a specified index in a sequence or a default value if the index is out of range. 
     * @param int $index 
     * @return mixed
     */
    public function elementAtOrDefault($index)
    {
        $i = 0;
        foreach($this as $item)
        {
            if ($i++ == $index) return $item;            
        }
        return null;
    }
    
    /**
     * Returns the first element of a sequence. 
     * @return mixed
     */
    public function first()
    {
        foreach ($this as $item)
        {
            return $item;
        }
        return null;
    }
    
    /**
     * Creates new linq provider.
     * @param array $array 
     * @return LinqProvider
     */
    public static function from(array $array)
    {
        $linq = new FromIterator($array);
        return $linq;
    }
    
    /**
     * Returns the last element of sequence.
     * @return mixed
     */
    public function last()
    {
        $last = null;
        foreach ($this->execute() as $item)
        {
            $last = $item;
        }
        return $last;
    }
    
    /**
     * Projects each element of a sequence into a new form.
     * @param Closure $predicate A transform function to apply each element.
     * @return LinqProvider
     */
    public function select(Closure $predicate)
    {
        return $this->selectIndex(function($item, $i) use ($predicate) { return $predicate($item); });
    }
    
    /**
     * Projects each element of a sequence into a new form by incorporating the element's index.
     * @param Closure $predicate A transform function to apply to each source element; the second parameter of the function represents the index of the source element.
     * @return LinqProvider
     */
    public function selectIndex(Closure $predicate)
    {
        $iterator = new SelectIterator($this, $predicate);
        return $iterator;
    }
    
    /**
     * Projects each element of a sequence to an array and flattens the resulting sequences into one sequence.
     * @param Closure $predicate A transform function to apply to each element.
     * @return LinqProvider
     */
    public function selectMany(Closure $predicate)
    {
        return $this->selectManyIndex(function ($item, $i) use ($predicate) { return $predicate($item); });
    }
    
    /**
     * Projects each element of a sequence to an array and flattens the resulting sequences into one sequence. The index of each source element is used in the projected form of that element.
     * @param Closure $predicate A transform function to apply to each source element; the second parameter of the function represents the index of the source element.
     * @return LinqProvider
     */
    public function selectManyIndex(Closure $predicate)
    {
        $iterator = new SelectManyIterator($this, $predicate);
        return $iterator;
    }
    
    /**
     * Bypasses a specified number of elements in a sequence and then returns the remaining elements.
     * @param int $count 
     * @return LinqProvider
     */
    public function skip($count)
    {
        $iterator = new SkipIterator($this, $count);
        return $iterator;
    }
    
    /**
     * Bypasses elements in a sequence as long as a specified condition is true and then returns the remaining elements. 
     * @param Closure $predicate 
     * @return LinqProvider
     */
    public function skipWhile(Closure $predicate)
    {
        return $this->skipWhileIndex(function ($item, $i) use ($predicate) { return $predicate($item); });
    }
    
    /**
     * Bypasses elements in a sequence as long as a specified condition is true and then returns the remaining elements. The element's index is used in the logic of the predicate function. 
     * @param Closure $predicate 
     * @return LinqProvider
     */
    public function skipWhileIndex(Closure $predicate)
    {
        return new SkipWhileIterator($this, $predicate);
    }
    
    /**
     * Computes the sum of a sequence.
     * @throws InvalidOperationException 
     * @return float
     */
    public function sum()
    {
        $sum = 0;
        foreach ($this as $item)
        {
            if (!is_numeric($item)) throw new InvalidOperationException();
            $sum += $item;
        }
        return $sum;
    }
    
    /**
     * Returns a specified number of contiguous elements from the start of a sequence. 
     * @param int $count 
     * @return LinqProvider
     */
    public function take($count)
    {
        $iterator = new TakeIterator($this, $count);
        return $iterator;
    }
    
    /**
     * Returns elements from a sequence as long as a specified condition is true. 
     * @param Closure $predicate 
     * @return LinqProvider
     */
    public function takeWhile(Closure $predicate)
    {
        return $this->takeWhileIndex(function ($item, $i) use ($predicate) { return $predicate($item); });
    }
    
    /**
     * Returns elements from a sequence as long as a specified condition is true. The element's index is used in the logic of the predicate function. 
     * @param Closure $predicate 
     * @return LinqProvider
     */
    public function takeWhileIndex(Closure $predicate)
    {
        return new TakeWhileIterator($this, $predicate);
    }
    
    /**
     * Executes the query and stores the result in array.
     * @return array
     */
    public function toArray()
    {
        $result = array();
        foreach ($this->execute() as $item)
        {
        	$result[] = $item;
        }
        return $result;
    }
    
    /**
     * Filters a sequence of values vased on a predicate.
     * @param Closure $predicate A function to test each element for a condition.
     * @return LinqProvider
     */
    public function where(Closure $predicate)
    {
        return $this->whereIndex(function($item, $i) use ($predicate) { return $predicate($item); });
    }
    
    /**
     * Filters a sequence of values vased on a predicate. Each element's index is used in the logic of the predicate function.
     * @param Closure $predicate A function to test each element for a condition; the second parameter of the function represents the index of the source element.
     * @return LinqProvider
     */
    public function whereIndex(Closure $predicate)
    {
        $iterator = new WhereIterator($this, $predicate);
        return $iterator;
    }
    
    /**
     * Summary of getIterator
     * @return mixed
     */
    public function getIterator()
    {
        return $this->execute();
    }
    
    /**
     * @return Generator
     */
    protected abstract function execute();
}
