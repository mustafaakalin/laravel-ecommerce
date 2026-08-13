<?php

namespace App\Livewire;

use Livewire\Component;

class Toast extends Component
{
    public $show = false;
    public $message = '';
    public $type = 'success';
    public $queue = [];

    protected $listeners = ['showToast'];

    public function showToast($message, $type = 'success')  // Parametreleri ayrı ayrı alalım
    {
        // Yeni toast'ı kuyruğa ekle
        $this->queue[] = [
            'message' => $message,
            'type' => $type
        ];

        if (!$this->show) {
            $this->processQueue();
        }
    }

    public function processQueue()
    {
        if (!empty($this->queue)) {
            $toast = array_shift($this->queue);
            $this->message = $toast['message'];
            $this->type = $toast['type'];
            $this->show = true;
        }
    }

    public function render()
    {
        return view('livewire.toast');
    }
}
