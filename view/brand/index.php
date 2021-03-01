{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <!--产品列表-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-btn-container">
                    <button type="button" class="layui-btn layui-btn-sm" onclick="$eb.createModalFrame(this.innerText,'{:Url('create')}',{h:300,w:400})">添加品牌</button>
                </div>

                <div class="layui-btn-container">
                    <a href="{:url('admin/brand.Lists/exports')}" class="layui-btn layui-btn-sm">导出</a>
                </div>

                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">品牌名称</label>
                                <div class="layui-input-block">
                                    <input type="text" name="name" class="layui-input" placeholder="请输入品牌名称">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                        <i class="layui-icon layui-icon-search"></i>搜索</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <table class="layui-hide" id="List" lay-filter="List"></table>
                <script type="text/html" id="act">
                    <button class="layui-btn layui-btn-xs" onclick="$eb.createModalFrame('编辑','{:Url('create')}?id={{d.id}}',{h:300,w:400})">
                        <i class="fa fa-paste"></i> 编辑
                    </button>
                    <button class="layui-btn layui-btn-xs" lay-event='delstor'>
                        <i class="fa fa-warning"></i> 删除
                    </button>
                </script>
            </div>
        </div>
    </div>
</div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList('List',"{:Url('get_article_list')}",function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'id',width:'20%',align: 'center'},
            {field: 'name', title: '品牌名称',edit:'cate_name',width:'20%',align: 'center'},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',width:'20%',align: 'center'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'40%'},
        ];
    });
    //自定义方法
    var action= {
        set_value: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_value',
                q: {field: field, id: id, value: value}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
    }
    //查询
    layList.search('search',function(where){
        layList.reload(where);
    });
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'name':
                action.set_value('name',id,value);
                break;
            case 'sort':
                action.set_value('sort',id,value);
                break;
        }
    });
    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delstor':
                var url=layList.U({a:'delete',q:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                            obj.del();
                            location.reload();
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                })
                break;
            case 'open_image':
                $eb.openImage(data.pic);
                break;
        }
    })
</script>
{/block}