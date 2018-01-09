<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/30
 * Time: 16:05
 */

namespace App\Http\Traits;

use App\Models\Doc;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait UploadTrait
{
    protected $save_path; //用于moveAndSaveFiles方法的第二个参数, 指明在public文件夹下的文件夹名
    /**
     * 转移文件并返回新路径
     * @param $files
     * @return array|void
     */
    public function moveAndSaveFiles($files)
    {
        if(!is_array($files))
            return;

        $ids = "";
        foreach ($files as $k => $file){
            $temp_path = $file['path'];
            $path = new File(storage_path('app/' .$file['path']));
            $files[$k]['path'] = Storage::putFile('public/'.$this->save_path, $path);
            Storage::delete($temp_path);  //删除临时文件

            //保存文件
            $ids .= Doc::create([
                    'name'=>$files[$k]['name'],
                    'path'=>$files[$k]['path']
                ])['id'] . ",";
        }

        return rtrim($ids, ",");
    }

    /**
     * 比较新旧数组, 得出移除的和新增的值(以数组形式返回)
     * @param $old_files 旧数组
     * @param $new_files 新数组
     * @return array
     */
    public function getAddsAndRemoves($old_files, $new_files)
    {
        if(!empty($old_files)){
            $removes = empty($new_files) ? $old_files : myGetMulDiff($old_files, $new_files);
            $adds = empty($new_files) ? [] : myGetMulDiff($new_files, $old_files);
        }else{
            $removes = [];
            $adds = empty($new_files) ? [] : $new_files;
        }
        return [$removes, $adds];
    }

    /**
     * @param $doc_id 文件id数组 { "document" => '1,2,3,4,...'}
     */
    public function getOldAndNew($request, $doc_id)
    {

        $old = $doc_id['document'] == null ? [] : DB::select("select `name`, `path` from docs where id in ({$doc_id['document']})");
        $old = json_decode(json_encode($old),TRUE);  //否则 Object of class stdClass could not be converted to string

        $new = [];
        foreach ($request->fileList as $file){
            $new[] = [
                'name' => $file['name'],
                'path' => $file['path']
            ];
        }

        return [$old, $new];
    }

    //todo 删除确定被移除的文件
    public function deleteRemoveFiles($removes)
    {
        $remove_ids = [];
        if(!empty($removes)){
            foreach ($removes as $remove){
                Storage::delete($remove['path']);
                $doc = Doc::where('path', $remove['path']);
                $remove_ids[] = $doc->first()->id;
                $doc->delete();
            }
        }
        return $remove_ids;
    }

    //todo 将新增文件从temp文件夹转移到contracts文件夹中
    public function insertAddFiles($adds)
    {
        $add_ids = [];
        if(!empty($adds)){
            $ids = $this->moveAndSaveFiles($adds);
            $add_ids = explode(",", $ids);
        }
        return $add_ids;
    }

    /**
     * 针对本项目, 得到最终可以保存的文件$final_ids
     * @return string 字符串
     */
    public function getFinalIds($request, $doc_id)
    {
        list($old, $new ) = $this->getOldAndNew($request, $doc_id);
        list($removes, $adds ) = $this->getAddsAndRemoves($old, $new);
        list($remove_ids, $add_ids) = [$this->deleteRemoveFiles($removes), $this->insertAddFiles($adds)];

        $origin_ids = explode(",", $doc_id['document']);
        $final_ids = collect($origin_ids)->diff($remove_ids)->merge($add_ids)->toArray();

        $final_ids = implode(",", $final_ids);
        if(strpos($final_ids, ',') == 0){
            $final_ids = ltrim($final_ids, ",");
        }
        return $final_ids;
    }

    /**
     * 配合delete主体顺便删除文件的方法
     */
    public function deleteFilesForDestroy($ids)
    {
        if(strlen($ids) > 0){
            $files = DB::select("select `path` from docs where id in ({$ids})");
            foreach ($files as $file){
                Storage::delete($file->path);
            }
            DB::delete("delete from docs where id in ({$ids})");
        }
    }

    /**
     * 文件上传, 先上传至临时文件夹（本文件夹每天清空一次）
     */
    public function uploadFileToTemp(Request $request)
    {
        if($request->hasFile('uploadFiles')){
            $files = $request->uploadFiles;
            $file = $files[0];
            $name = $file->getClientOriginalName();
            $tempPath = Storage::putFile('public/temp', $file);
            $array = [
                'name' => $name,
                'path' => $tempPath
            ];
            return $array;
        }
    }

    /**
     * 根据前端post传入的路径删除文件
     * @param Request $request
     * @return string
     */
    public function deleteFile(Request $request)
    {
        $path = $request->filePath;
        $re = Storage::delete($path);
        if($re){
            return 'success';
        }
    }
}