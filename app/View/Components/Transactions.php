<?php

namespace App\View\Components;

use App\Services\TransactionService;
use Illuminate\View\Component;

class Transactions extends Component
{
    public $transactions;

    public $user;

    public $userKeyToUsername;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($transactions, $user)
    {
        $this->transactions = $transactions;
        $this->user = $user;

        $this->userKeyToUsername = app(TransactionService::class)->transactionsUsernames($transactions);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.transactions');
    }
}
