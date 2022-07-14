<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BankTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_all_accounts()
    {
        Passport::actingAs(
            User::factory()->create()
        );
        $response=$this->get('/api/getAllAccounts');
        $accounts=Account::all();
        $arrayAccounts=[];
        foreach($accounts as $account)
        {
            $array=
            [
                'id' => $account->id,
                'amount' =>$account->amount,
                'customer_id' => $account->customer_id,
                'customer_name' => $account->customer->name,
            ];
            array_push($arrayAccounts,$array);
        }
        $response->assertStatus(200)->assertJson([
            'data' => $arrayAccounts
        ]);
    }

    public function test_get_all_customer()
    {
        Passport::actingAs(
            User::factory()->create()
        );
        $response=$this->get('/api/getAllCustomers');
        $customers=Customer::all();
        $arrayCustomers=[];
        foreach($customers as $customer)
        {
            $array=
            [
                'id' => $customer->id,
                'name' => $customer->name
            ];
            array_push($arrayCustomers,$array);
        }
        $response->assertStatus(200)->assertJson([
            'data' => $arrayCustomers
        ]);
    }
    public function test_create_account()
    {

        Passport::actingAs(
            User::factory()->create()
        );
        $response = $this->post('api/CreateAccount/1');
        
        $response->assertStatus(200)->assertJson(
            [
                'data'=>[
                    'customer_id' => 1,
                    'amount' => 10
                ]
            ]);

        // $lastRecord=Account::where('customer_id',1)->orderBy('created_at','desc')->first();
        // $this->assertModelExists($lastRecord);

        $lastRecord=Account::orderBy('created_at','desc')->first();
        $lastRecordCustomer=$lastRecord->customer_id;
        $this->assertEquals($lastRecordCustomer, 1);
    }

    public function test_create_account2()
    {
        Passport::actingAs(
            User::factory()->create()
        );
        $response = $this->post('api/CreateAccount/1');
        
        $response->assertStatus(200)->assertJson(
            [
                'data'=>[
                    'customer_id' => 1,
                    'amount' => 10
                ]
            ]);
        
        $lastRecord=Account::where('customer_id',1)->orderBy('created_at','desc')->first();
        $this->assertModelExists($lastRecord);
    }

    public function test_transferring()
    {
        Passport::actingAs(
            User::factory()->create()
        );
        $testAccount=Account::create([
            'customer_id' => 1,
            'amount' => 999999999999
        ]);
        $idFrom=$testAccount->id;
        $toAccountAmount=Account::find(2)->amount;
        $response=$this->post("api/Transfer/from/$idFrom/to/2",['amount'=>999999999999]);
        $newToAccountAmount=Account::find(2)->amount;
        $newFromAccount=Account::find($idFrom)->amount;
        $this->assertEquals($newToAccountAmount,$toAccountAmount+999999999999);
        $this->assertEquals($newFromAccount,0);
        $lastRecord=Transfer::where('account_id1',$idFrom)->where('account_id2',2)->where('amount',999999999999)->orderBy('created_at','desc')->first();
        $this->assertModelExists($lastRecord);
    }

    public function test_getAccountAmount()
    {
        Passport::actingAs(
            User::factory()->create()
        );
        $testAccount=Account::create(
            [
                'customer_id' =>1,
                'amount' => 3969369369
            ]);
        $IDtest=$testAccount->id;
            $response=$this->get("api/getAccountAmount/$IDtest");
            $response->assertStatus(200)->assertJson(
                [
                    'data'=>
                    [
                        'amount' => "3969369369$",
                        "customer_name"=>$testAccount->customer->name,
                        'customer_id' => $testAccount->customer->id
                    ]
                ]);
    }

    public function test_history()
    {
        Passport::actingAs(
            User::factory()->create()
        );
        $testAccount1=Account::create([
            'customer_id' =>1,
            'amount' => 10000,
        ]);
        $testAccount2=Account::create([
            'customer_id' =>1,
            'amount' => 10000,
        ]);
        $IDtest1=$testAccount1->id;
        $IDtest2=$testAccount2->id;
        $this->post("api/Transfer/from/$IDtest1/to/$IDtest2",['amount' => 10]);
        $this->post("api/Transfer/from/$IDtest2/to/$IDtest1",['amount' => 10]);
        

        $response=$this->get("api/TransferHistory/$IDtest1");
        $response->assertStatus(200)->assertJson(
            [
                'data'=>
                [
                    'this_account_transfered'=>
                    [
                        [
                            'transfererAccount_id' =>$testAccount1->transmitters[0]->accountTransferer->id,
                            'transferer_name' =>$testAccount1->transmitters[0]->accountTransferer->customer->name,
                            'recieverAccount_id'=> $testAccount1->transmitters[0]->accountReciever->id,
                            'reciever_name' => $testAccount1->transmitters[0]->accountReciever->customer->name,
                            'amount' => $testAccount1->transmitters[0]->amount,
                        ]
                    ],
                    'this_account_recieved'=>
                    [
                        [
                            'recievedFromAccount_id' =>$testAccount1->recievers[0]->accountTransferer->id,
                            'transferer_name' =>$testAccount1->recievers[0]->accountTransferer->customer->name,
                            'recieverAccount_id'=> $testAccount1->recievers[0]->accountReciever->id,
                            'reciever_name' => $testAccount1->recievers[0]->accountReciever->customer->name,
                            'amount' => $testAccount1->recievers[0]->amount,
                        ]
                    ]
                ]
            ]);
    }
}
