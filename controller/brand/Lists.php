<?php

namespace app\admin\controller\brand;

use app\admin\controller\AuthController;
use service\FormBuilder as Form;
use service\JsonService as Json;
use service\UtilService as Util;
use think\Image;
use think\Request;
use think\Session;
use think\Url;

class Lists extends AuthController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
//        查询数据
        $data = \app\admin\model\brand\Lists::select();
//        返回数据
        return view('brand/index',compact('data'));
    }

    public function get_article_list()
    {
        $where = Util::getMore  ([
            ['page', 1],
            ['limit', 20],
            ['cid', ''],
            ['name', ''],
        ]);
        return Json::successlayui(\app\admin\model\brand\Lists::getAllList($where));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create($id = 0)
    {
        if ($id) $admin = \app\admin\model\brand\Lists::get($id);
        $form = Form::create(Url::build('save', ['id' => $id]), [
            Form::input('name', '品牌名称', isset($admin) ? $admin->name : '')->required(),
            Form::input('sort', '排序', isset($admin) ? $admin->sort : '')->required(),
            Form::upload('img', '图标上传','upload')
        ]);
        $this->assign(compact('form'));
        return $this->fetch('public/form-builder');
    }

    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('img');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                $path = "./uploads/".$info->getSaveName();
                $img = Image::open($path);
                $img->text('online edu',ROOT_PATH.'public/FZSTK.TTF',20,'#ffffff')->save($path);
                Session::set('path',$path);
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
    }

    public function set_value($field = '', $id = '', $value = '')
    {
        $field == '' || $id == '' || $value == '' && Json::fail('缺少参数');
        if (\app\admin\model\brand\Lists::where(['id' => $id])->update([$field => $value]))
            return Json::successful('保存成功');
        else
            return Json::fail('保存失败');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save($id = 0)
    {
        $post = Util::postMore([
            ['name', ''],
            ['sort', ''],
            ['img',Session::get('path')],
        ]);

        if ($id) {
            \app\admin\model\brand\Lists::update($post, ['id' => $id]);
            return Json::successful('修改成功');
        } else {
            $post['add_time'] = time();
            if (\app\admin\model\brand\Lists::set($post))
                return Json::successful('添加成功');
            else
                return Json::fail('添加失败');
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id = 0)
    {
        if (!$id) return Json::fail('缺少参数');
        if (\app\admin\model\brand\Lists::del($id))
            return Json::successful('删除成功');
        else
            return Json::fail('删除失败');
    }

    public function exports()
    {
        $data = \app\admin\model\brand\Lists::all()->toArray();
        $PHPExcel = new \PHPExcel();
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称
        $PHPSheet->setCellValue("A1","id")->setCellValue("B1","title")->setCellValue("C1","author")->setCellValue("D1","synopsis");//表格数据
        for ($i=2;$i<=count($data)+1;$i++){
            $PHPSheet->setCellValue("A".$i,$data[$i-2]['id'])->setCellValue("B".$i,$data[$i-2]['title'])->setCellValue("C".$i,$data[$i-2]['author'])->setCellValue("D".$i,$data[$i-2]['synopsis']);
        }
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment;filename="123.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        $objWriter->save('php://output'); //文件通过浏览器下载
    }
}
