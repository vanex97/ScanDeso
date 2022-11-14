<?php

namespace App\View\Components;

use App\Services\DesoService;
use Illuminate\View\Component;

class TransactionsPagination extends Component
{
    public $page;

    public $pages;

    public $address;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($transactionQuantity, $page, $address)
    {
        $this->page = $page;
        $this->address = $address;
        $this->pages = ceil((float) $transactionQuantity / DesoService::TRANSACTIONS_LIMIT);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.transactions-pagination');
    }
}
