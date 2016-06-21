<?php

namespace Kj187;

class Buffer {

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param callable $callback
     * @param int $size
     */
    public function __construct(callable $callback, $size = 500) {
        $this->callback = $callback;
        $this->size = $size;
    }

    /**
     * @param string $item
     */
    public function add($item) {
        $this->data[] = $item;
        if (count($this->data) >= $this->size) {
            $this->flush();
        }
    }

    public function reset() {
        $this->data = [];
    }

    public function flush() {
        if (count($this->data) > 0) {
            call_user_func($this->callback, $this->data);
            $this->reset();
        }
    }
}
