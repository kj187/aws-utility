<?php

namespace Kj187;

class Buffer {

    protected $_callback;

    protected $_size;

    protected $_data = [];

    /**
     * @param callable $callback
     * @param int $size
     */
    public function __construct(callable $callback, $size = 500) {
        $this->_callback = $callback;
        $this->_size = $size;
    }

    /**
     * @param string $item
     */
    public function add($item) {
        $this->_data[] = $item;
        if (count($this->_data) >= $this->_size) {
            $this->flush();
        }
    }

    public function reset() {
        $this->_data = [];
    }

    public function flush() {
        if (count($this->_data) > 0) {
            call_user_func($this->_callback, $this->_data);
            $this->reset();
        }
    }
}
