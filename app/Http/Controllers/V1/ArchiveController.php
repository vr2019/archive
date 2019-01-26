<?php

namespace App\Http\Controllers\V1;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Modal\V1\ArchiveModal;

use Storage;
use App\Transformers\V1\ArchiveTransformer;
use Dingo\Api\Routing\Helpers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ArchiveController extends BaseController{

    use Helpers;

    private $userId = '';
    
    public function AddArchive(Request $request){
        $this->userId = $this->checkAuthUser($request);

        $v = $this->validate($request, [
            'name'=>'required|string',
            //'category'=>'required|string',
            'sex'=>'required|in:nan,nv',
            'avatarid'=>'required|integer',
            'birth'=>'date'
        ]);

        $v['category'] = '';
        $v['userid'] = $this->userId;

        $file = $this->checkFileExist($v['avatarid']);
        
        $archive = ArchiveModal::create($v);

        return $this->response->item($archive, new ArchiveTransformer);
    }

    public function UpdateArchive(Request $request, $id){
        $this->userId = $this->checkAuthUser($request);

        $v = $this->validate($request, [
            'name'=>'required|string',
            //'category'=>'required|string',
            'sex'=>'required|in:nan,nv',
            'avatarid'=>'required|integer',
            'birth'=>'date'
        ]);

        $file = $this->checkFileExist($v['avatarid']);

        $archive = $this->checkIsMyArchive($this->userId, $id);
        $archive->name = $v['name'];
        $archive->sex = $v['sex'];
        $archive->avatarid = $v['avatarid'];
        $archive->birth = $v['birth'];
        $archive->save();

        return $this->response->item($archive, new ArchiveTransformer);
    }

    public function GetArchiveById(Request $request, $id){
        $this->userId = $this->checkAuthUser($request);
        //$archive = $this->checkIsMyArchive($this->userId, $id);
        //$archive->delete();
        $archive = $this->getArchive($id);
        return $this->response->item($archive, new ArchiveTransformer);
    }

    //////////////////////////////////
    private function getArchive($id){
        $archive = ArchiveModal::find($id);
        if(!$archive){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('没有资源');
        }
        return $archive;
    }
    private function checkIsMyArchive($userid, $id){
        $archive = ArchiveModal::find($id);
        if(!$archive || $archive->userid != $userid){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('没有资源');
        }
        return $archive;
    }
    private function checkAuthUser($request){
        $userid = $request->input('userid');
        if(!$userid){
            throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException('no auth');
        }
        return $userid;
    }
    private function getToken($request){
        $token = $request->input('usertoken');
        if(!$token){
            throw new UnauthorizedHttpException('no auth');
        }
        return $token;
    }
    private function checkFileExist($fileid){
        $token = $this->getToken($request);
        $baseurl = env('NETSPAVE_URL');
        $headers = ['Authorization'=>$token];
        $client = new Client(['base_uri' => $baseurl]);

        $file = null;
        try{
            if($fileid != ''){
                $response = $client->request('GET', 'file/'.$fileid, ['headers'=>$headers]);
                $code = $response->getStatusCode();
                if($code == 200){
                    $content = $response->getBody()->getContents();
                    $file = json_decode($content)->spacefile;
                }
            }
        }catch(RequestException $e){
            $file = null;
        }
        
        if(!$file){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('没有资源');
        }

        return $file;
    }

}