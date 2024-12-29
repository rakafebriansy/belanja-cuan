<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderDetail;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Order Detail - Belanja Cuan')]
class MyOrderDetailPage extends Component
{
    public $order_id;
    public function mount($id)
    {
        $this->order_id = $id;
    }
    public function render()
    {
        $order_details = OrderDetail::with('product')->where('order_id',$this->order_id)->get();
        $address = Address::where('order_id',$this->order_id)->first();
        $order = Order::find($this->order_id);
        return view('livewire.my-order-detail-page',[
            'order_details' => $order_details,
            'order' => $order,
            'address' => $address,
        ]);
    }
}
