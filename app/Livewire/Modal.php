<?php

namespace App\Livewire;

use Livewire\Component;

class Modal extends Component
{
    public $show = false;
    public $message = '';
    public $type = 'success';
    public $queue = [];

    protected $listeners = ['showModal'];

    public function showModal($message, $type = 'success')  // Parametreleri ayrı ayrı alalım
    {
        // Yeni modal'ı kuyruğa ekle
        $this->queue[] = [
            'message' => $message,
            'type' => $type
        ];

        if (!$this->show) {
            $this->processQueue();
        }
    }


    public function closeModal()
    {
        $this->show = false;
    }

    public function processQueue()
    {
        if (!empty($this->queue)) {
            $modal = array_shift($this->queue);
            $this->message = $modal['message'];
            $this->type = $modal['type'];
            $this->show = true;
        }
    }
    public function render()
    {
        return view('livewire.modal');
    }
}