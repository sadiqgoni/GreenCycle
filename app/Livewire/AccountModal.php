<?php 
namespace App\Livewire;

use Livewire\Component;

class AccountModal extends Component
{
    public $account;

    protected $listeners = ['showAccountModal'];

    public function showAccountModal($account)
    {
        $this->account = $account;
    }

    public function render()
    {
        return view('livewire.account-modal');
    }
}
