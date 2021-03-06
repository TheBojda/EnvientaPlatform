<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use app\Http\Controllers\LoginController;
include_once './app/Http/Controllers/LoginController.php';


class loginTest extends TestCase
{
    protected $controller;
    protected $request;
    
    public function setup() {
        parent::setup();
        $this->controller = new LoginController();
        $this->request = new Request();
    }
    
    public function test_requestPin() {
        
        // create test rows into database
        DB::table('users')->where(['email' => 'test1@test.hu'])->delete();
        DB::table('users')->where(['email' => 'test2@test.hu'])->delete();
        DB::table('users')->insert(["email"=>'test1@test.hu', 'psw'=>'', 'pin'=> '123']);
        DB::table('users')->insert(["email"=>'test2@test.hu', 'psw'=> md5('123'), 'pin'=> '123']);
        $user = DB::table('users')->where(["email"=>"test2@test.hu"])->first();
        DB::table('users')->where(["email"=>"test2@test.hu"])->update(["psw" => md5('123'.$user->id)]);
        
        $this->request->merge(['email' => 'test@test.hu']);
        $res = $this->controller->requestPin($this->request);
        
        // $this->assertFalse($actual);
        // $this->assertTrue($actual);
        // $this->assertGreaterThan($expected, $actual);
        // $this->assertGreaterThanOrEqual()
        // $this->assertLessThan($excepted, $actual)
        // $this->assertLessThanOrEqual($excepted, $actual)
        // $this->assertEquals($excepted, $actual);
        // $this->assertRegExp($pattern, $actual)
        
        $this->assertEquals('sent',$res);
    }
    
    public function test_requestPin_ByPsw() {
        $this->request->merge(['email' => 'test2@test.hu']);
        $res = $this->controller->requestPin($this->request);
        $this->assertEquals('psw',$res);
    }
    
    public function test_login_byPin_ok() {
        $this->request->merge(['email' => 'test1@test.hu', 'pin' => '123']);
        $res = $this->controller->login($this->request);
        $this->assertEquals('success',$res);
    }
    
    public function test_login_byPsw_ok() {
        $this->request->merge(['email' => 'test2@test.hu', 'psw' => '123']);
        $res = $this->controller->login($this->request);
        $this->assertEquals('success',$res);
    }

    public function test_login_byPin_notok() {
        $this->request->merge(['email' => 'test1@test.hu', 'pin' => '56123']);
        $res = $this->controller->login($this->request);
        $this->assertEquals('fail',$res);
    }
    
    public function test_login_byPsw_notok() {
        $this->request->merge(['email' => 'test2@test.hu', 'psw' => '56123']);
        $res = $this->controller->login($this->request);
        $this->assertEquals('fail',$res);
    }
    
    public function test_end() {
        // delete test datas from database
        DB::table('users')->where(['email' => 'test1@test.hu'])->delete();
        DB::table('users')->where(['email' => 'test2@test.hu'])->delete();
        $this->assertEquals('','');
    }
}
