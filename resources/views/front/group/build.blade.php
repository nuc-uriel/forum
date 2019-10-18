@extends('front.layout')
@section('title')申请创建小组@stop
@section('css')
<link rel="stylesheet" href="{{ asset('css/build_group.css') }}">
<link rel="stylesheet" href="{{ asset('js/cropperjs/cropper.min.css') }}">
@stop
@section('js')
<script type="application/javascript" src="{{ asset('js/build_group.js') }}"></script>
<script type="application/javascript" src="{{ asset('js/cropperjs/cropper.min.js') }}"></script>
@stop
@section('main')
<div class="page-info">
    <div class="wrapper">
        <span>申请创建小组</span>
    </div>
</div>
<div class="main">
    <div class="wrapper">
        <div class="main-left">
            <div class="base-info">
                <form action="#" method="post" accept-charset="utf-8" id="group-form">
                    {{csrf_field()}}
                    <table>
                        <tr>
                            <th>小组名称</th>
                            <td colspan="2"><input type="text" name="name" value="" placeholder="" class="name">
                            </td>
                            <td class="error-tip" hidden=""></td>
                        </tr>
                        <tr>
                            <th>小组介绍</th>
                            <td colspan="2"><textarea name="introduce" class="introduce"></textarea></td>
                            <td class="error-tip" hidden=""></td>
                        </tr>
                        <tr>
                            <th>小组类别</th>
                            <td colspan="2"><select name="group_type" class="group_type">
                                    <option value="" selected>请选择</option>
                                    @foreach ($group_type as $gt)
                                    <option value="{{ $gt->id }}">{{ $gt->name }}</option>
                                    @endforeach
                                </select></td>
                            <td class="error-tip" hidden=""></td>
                        </tr>
                        <tr>
                            <th>小组图标</th>
                            <td colspan="2"><input type="hidden" name="path" class="path">
                                <input type="file" hidden="" class="icon" name="icon">
                                <a href="javascript:void(0);" title="" onclick="$('.icon').click();" style="margin-left: 0;" class="add_icon">选择图标</a>
                                <img src="" alt="" hidden="" class="icon_show">
                                <a href="javascript:void(0);" title="" onclick="$('.icon').click();" hidden="" class="update_icon">更新</a>
                            </td>
                            <td class="error-tip" hidden=""></td>
                        </tr>
                        <tr>
                            <th>标签</th>
                            <td colspan="2"><input type="text" name="group_label" value="" placeholder=""></td>
                            <td class="error-tip" hidden=""></td>
                        </tr>
                        <tr>
                            <th></th>
                            <td colspan="3">
                                <div>
                                    标签作为关键词可以被用户搜索到(最多5个标签，多个标签之间用空格隔开)
                                </div>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <th></th>
                            <td colspan="2"><input type="submit" name="" value="提交申请" class="set-info-go"></td>
                            <td></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
        <div class="main-right">
            <h4 class="">小组创建</h4>
            <p>小组可以有不超过5个的标签，用来描述小组的目的。标签作为关键词可以被用户搜索到。 多个标签之间用空格分隔开。
                <h4 class="">小组标签</h4>
                <p>小组可以有不超过5个的标签，用来描述小组的目的。标签作为关键词可以被用户搜索到。 多个标签之间用空格分隔开。 </p>
                <p>比如，"Philip K. Dick小组"可以用 "作者 作家 科幻 科学幻想 迪克"， "关中豆瓣" 可以用 "本地 同城 西北 陕西 西安"。小组名称本身可以被搜索，就不用再加在标签里了。
                    小组的名称、介绍、标签在创立后都可以随时更改。 </p>
        </div>
    </div>
</div>
@stop