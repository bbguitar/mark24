@extends('templates.master')

@section('title')
Settings
@stop

@section('internalCss')
<link rel="stylesheet" type="text/css" href="/assets/css/site/settings.style.css">
@stop

@section('content')
<div class="row">
    <div class="settings-nav-wrapper col-md-3">
        <ul class="nav nav-stacked nav-pills">
            <li class="active"><a href="/settings">Account</a></li>
            <li><a href="/settings/password">Password</a></li>
            <li><a href="/settings/privacy">Privacy</a></li>
        </ul>
    </div>
    <div class="col-md-9">
        <div class="user-avatar-wrapper well">
            <h3>User Photo</h3>
            <div class="current-avatar-wrapper pull-left">
                <img src="/assets/images/loader_medium.gif" width="140" class="image-loader-gif">
                <img src="/assets/images/default_avatar.png" width="140" class="current-user-avatar">
                <span class="current-avatar-subtext subtext">Your Current Photo</span>
            </div>
            <div class="choose-avatar-wrapper pull-left">
                {{ Form::open(array('url' => 'ajax/users/upload-photo', 'files'=>true, 'class'=>'avatar-uploader-form')) }}
                    <input type="file" name="avatar-file" id="avatar_file" accept="image/*">
                {{ Form::close() }}

                <span class="subtext">Or select one of the shits.</span>

                <div class="predefined-avatar-wrapper">
                    Predefined shits here.
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="personal-information-wrapper well">
            <h3>Personal Information</h3>
            {{ Form::open(array('url'=>'ajax/users/update-personal-info', 'method'=>'put', 'class'=>'personal-information-form')) }}
                <div class="form-group">
                    <label for="email">Email</label>
                    <span class="help-block"></span>
                    <input type="email" name="email" id="email" class="form-control"
                    value="{{ Auth::user()->email }}">
                </div>

                <div class="row">
                    <div class="form-group col-md-2">
                        <label for="salutation">Title</label>
                        <select name="salutation" class="form-control">
                            <option value="Mr." <?php echo (Auth::user()->salutation == 'Mr.') ? 'selected' : null; ?>>Mr.</option>
                            <option value="Mrs." <?php echo (Auth::user()->salutation == 'Mrs.') ? 'selected' : null; ?>>Mrs.</option>
                            <option value="Ms." <?php echo (Auth::user()->salutation == 'Ms.') ? 'selected' : null; ?>>Ms.</option>
                            <option value="Dr." <?php echo (Auth::user()->salutation == 'Dr.') ? 'selected' : null; ?>>Dr.</option>
                        </select>
                    </div>

                    <div class="form-group col-md-5">
                        <label for="firstname">First Name</label>
                        <input type="text" name="firstname" class="form-control"
                        value="{{ Auth::user()->firstname }}">
                    </div>

                    <div class="form-group col-md-5">
                        <label for="lastname">Last Name</label>
                        <input type="text" name="lastname" class="form-control"
                        value="{{ Auth::user()->lastname }}">
                    </div>
                </div>

                <button type="submit" id="submit_personal_info" class="btn btn-primary">
                    Save Personal Info
                </button>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop

@section('js')
<script type="text/javascript" src="/assets/js/plugins/jquery.form.min.js"></script>
<script>
(function($) {
    $('#avatar_file').on('change', function() {
        // change the image to a rotating gif
        $('.image-loader-gif').show();
        $('.current-user-avatar').hide();

        $('.avatar-uploader-form').ajaxForm({
            url : $(this).attr('action'),
            dataType : 'json',
            success : function(response) {
                if(response.error) {
                    $('.image-loader-gif').hide();
                    $('.current-user-avatar').show();
                } else {
                    $('.image-loader-gif').hide();
                    $('.current-user-avatar').attr('src', response.userAvatar)
                        .show();
                }
            }
        }).submit();
    });

    $('#submit_personal_info').on('click', function(e) {
        var $this = $(this);

        $this.attr('disabled');

        $.ajax({
            type : 'put',
            data : $('.personal-information-form').serialize(),
            url : $('.personal-information-form').attr('action'),
            dataType : 'json'
        }).done(function(response) {
            if(response.error) {
                if(response.field == 'email') {
                    $this.removeAttr('disabled');

                    $('#email').parent().addClass('has-error').find('.help-block')
                        .text(response.message);
                }
            } else {
                $this.removeAttr('disabled');
                
                $('.personal-information-form .form-group').removeClass('has-error')
                    .find('.help-block').hide();
            }
        })

        e.preventDefault();
    })
})(jQuery);
</script>
@stop
