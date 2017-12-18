<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class HomeController extends BaseController
{
	private $_table = 'shadow_url';
	
	public function index(string $code)
	{
		try {
			if (empty($code)) {
				var_dump(\request()->getHost());
				throw new Exception(\request()->getHost().'/set', 300);
			}
			if (mb_strlen($code, 'utf8') !== 6) {
				abort(404);
			}
			$code = htmlspecialchars($code);
			$path = DB::table($this->_table)->where('uni_code', $code)->orderByDesc('id')->value('url');
		} catch (Exception $e) {
			$path = $e->getMessage();
		}
		return redirect($path);
		
		
	}
	
	public function set()
	{
		return view('home.index');
	}
	
	public function create(Request $req)
	{
		$ret = [
			'code' => 400,
			'message' => 'error',
			'data' => []
		];
		try {
			if ($req->isMethod('post')) {
				
				$url = $req->post('url');
				if (empty($url)) {
					throw new Exception('请输入URL', 400);
				}
				if (!filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL)) {
					throw new Exception('非法的URL', 400);
				}
				
				
				DB::beginTransaction();
				
				
				$id = DB::table($this->_table)->insertGetId([
					'url' => $url,
					'uni_code' => '',
					'count' => 0,
					'is_delete' => 0,
					'created_at' => time(),
					'updated_at' => time(),
				]);
				if (empty($id)) {
					throw new Exception('创建失败', 400);
				}
				$shortUrl = new ShortUrlController();
				$uniCode = $shortUrl->decToBase63($id);
				$affectRows = DB::table($this->_table)->where('id', $id)->update([
					'uni_code' => $uniCode,
					'updated_at' => time(),
				]);
				if (empty($affectRows)) {
					throw new Exception('更新失败', 400);
				}
				DB::commit();
				$ret = [
					'code' => 200,
					'message' => 'success',
					'data' => $req->getHost() . '/' . $uniCode
				];
			} else {
				throw new Exception('请求非法', 400);
			}
		} catch (Exception $e) {
			DB::rollBack();
			$ret['message'] = $e->getMessage();
			$ret['code'] = $e->getCode();
		}
		return response()->json($ret);
	}
//
//	public function save($url)
//	{
//
//		$ret = DB::table('shadow_url')->insert([
//			'url' => $url,
//			'uni_code' => '',
//			'count' => 0,
//			'is_delete' => 0,
//			'create_at' => time(),
//			'update_at' => time(),
//		]);
//		return $ret;
//	}
//
//	public function update($code)
//	{
//
//	}
}