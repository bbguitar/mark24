<div class="post-stream-holder well">
    <div class="stream-title">
        @if(isset($groupDetails))
        <h4>Group Posts</h4>
        @else
        <h4>Latest Posts</h4>
        @endif
    </div>

    <ul class="post-stream">
        <?php $properties = array_filter(get_object_vars($posts)); ?>
        @if(!empty($properties))
        @foreach($posts as $post)
        <?php $postTimestamp = Helper::timestamp($post->post_timestamp); ?>
        <li class="post-holder" data-post-id="{{ $post->post_id }}">
            <a href="/profile/{{ $post->user->username }}" class="writer-profile">
                {{ Helper::avatar(50, "small", "img-rounded pull-left", $post->user->id) }}
            </a>
            <div class="post-content pull-left">

                <div class="dropdown dropdown-post-options pull-right">
                    <a data-toggle="dropdown" href="#"><i class="fa fa-gear"></i></a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        @if(Auth::user()->id === $post->user->id)
                        <li><a href="#" class="delete-post"
                        data-post-id="{{ $post->post_id }}">Delete Post</a></li>
                        @if($post->post_type != 'quiz')
                        <li><a href="#" class="edit-post"
                        data-post-id="{{ $post->post_id }}">Edit Post</a></li>
                        @endif
                        @endif
                        <li><a href="#" class="link-post"
                        data-post-id="{{ $post->post_id }}">Link to this Post</a></li>
                    </ul>
                </div>

                <div class="post-content-header">
                    <a href="/profile/{{ $post->user->username }}" class="post-sender-name">
                        @if($post->user->id == Auth::user()->id)
                        Me
                        @else
                        @if($post->account_type == '1')
                        {{ $post->user->salutation.' '.$post->user->name }}
                        @else
                        {{ $post->user->name }}
                        @endif
                        @endif
                    </a>
                    <span class="sender-to-receiver">to</span>
                    <?php $groupCount = (!empty($post->recipients->groups)) ? count($post->recipients->groups) : null; ?>
                    <?php $userCount = (!empty($post->recipients->users)) ? count($post->recipients->users) : null; ?>

                    @if(!empty($post->recipients->groups))
                    @foreach($post->recipients->groups as $key => $groupRecipient)
                    @if($key != $groupCount -1 || $userCount != 0)
                    <a href="#" class="post-receiver-name">{{ $groupRecipient->group_name }}</a><span class="post-receiver-comma">,</span>
                    @else
                    <a href="#" class="post-receiver-name">{{ $groupRecipient->group_name }}</a>
                    @endif
                    @endforeach
                    @endif

                    @if(!empty($post->recipients->users))
                    @foreach($post->recipients->users as $key => $userRecipient)
                    @if($key != $userCount -1)
                    <a href="#" class="post-receiver-name">
                        <?php if($userRecipient->account_type == 1) { echo $userRecipient->salutation.'. '; } ?>
                        {{ $userRecipient->name }}
                    </a><span class="post-receiver-comma">,</span>
                    @else
                    <a href="#" class="post-receiver-name">
                        <?php if($userRecipient->account_type == 1) { echo $userRecipient->salutation.'. '; } ?>
                        {{ $userRecipient->name }}
                    </a>
                    @endif
                    @endforeach
                    @endif
                </div>

                <div class="post-content-container">
                    <div class="post {{ $post->post_type }}">
                    <?php
                    switch($post->post_type) {
                        case 'note' :
                    ?>
                            {{{ $post->note_content }}}
                    <?php
                            $content = $post->note_content;
                            break;
                        case 'alert' :
                    ?>
                            {{{ $post->alert_content }}}
                    <?php
                            $content = $post->alert_content;
                            break;
                        case 'assignment' :
                    ?>
                            <strong>{{{ $post->assignment->title }}}</strong>
                            <div class="assignment-details">
                                @if(Auth::user()->account_type == 1)
                                <a href="/assignment-manager/{{ $post->assignment->assignment_id }}"
                                class="btn btn-default">
                                    Turned In ({{ $post->assignments_submitted }})
                                </a>
                                <span class="due-date">
                                    Due {{ date('M d, Y', strtotime($post->assignment_due_date)) }}
                                </span>
                                @endif
                                @if(Auth::user()->account_type == 2)
                                @if(isset($post->assignment_submitted))
                                <a href="/assignment-sheet/{{ $post->assignment->assignment_id }}"
                                class="btn btn-default">Turned In</a>
                                <span class="due-date">
                                    {{ ucfirst(strtolower($post->assignment_submitted->status)) }}
                                </span>
                                @endif
                                @if(!isset($post->assignment_submitted))
                                <a href="/assignment-sheet/{{ $post->assignment->assignment_id }}"
                                class="btn btn-default">Turn In</a>
                                <span class="due-date">
                                    Due {{ date('M d, Y', strtotime($post->assignment_due_date)) }}
                                </span>
                                @endif
                                @endif
                            </div>
                            <div class="assignment-description">
                                {{{ $post->assignment->description }}}
                            </div>
                    <?php
                            $content = null;
                            break;
                        case 'quiz' :
                            $quizDetails = Helper::getQuizDetails($post->quiz_id);
                    ?>
                        <strong class="quiz-title">{{ $quizDetails['title'] }}</strong>
                        <div class="quiz-button-wrapper">
                            <?php $turnedIn = Helper::getTakenDetails($post->quiz_id); ?>
                            @if(Auth::user()->account_type == 1)
                            <a href="/quiz-manager/{{ $post->quiz_id }}" class="btn btn-default">
                                Turned In ({{ $turnedIn['takers'] }})
                            </a>
                            <span class="due-date">
                                Due {{ date('M d, Y', strtotime($post->quiz_due_date)) }}
                            </span>
                            @endif

                            @if(Auth::user()->account_type == 2)
                            <?php $taken = Helper::checkQuizTaken($post->quiz_id); ?>
                            @if(empty($taken))
                            <a href="/quiz-sheet/{{ $post->quiz_id }}" class="btn btn-default">
                                Take Quiz
                            </a>
                            <span class="due-date">Due {{ date('M d, Y', strtotime($post->
                            quiz_due_date)) }}</span>
                            @endif
                            @if(!empty($taken))
                            <a href="/quiz-result/{{ $post->quiz_id }}" class="btn btn-default">
                                Quiz Result
                            </a>
                            @endif

                            @endif
                        </div>
                        <div class="question-count-wrapper">
                            <strong class="count-text">{{ $turnedIn['count'] }}</strong>
                        </div>
                    <?php
                            break;
                        default :
                            break;
                    }
                    ?>
                    </div>
                    @if($post->post_type != 'quiz' && $post->post_type != 'assignment')
                    {{ Form::open(array(
                        'url' => '/ajax/post_creator/update-post',
                        'class' => 'edit-post-form',
                        'data-post-id' => $post->post_id))
                    }}
                        <div class="form-group">
                            <textarea name="message-post" class="form-control message-post"
                            data-post-id="{{ $post->post_id }}">{{ $content }}</textarea>
                        </div>
                        <input type="hidden" name='post-id' value="{{ $post->post_id }}">

                        <button class="btn btn-primary save-edit-post"
                        data-post-id="{{ $post->post_id }}">Save</button>
                        <button class="btn btn-default cancel-edit-post"
                        data-post-id="{{ $post->post_id }}">Cancel</button>
                    {{ Form::close() }}
                    @endif
                </div>

                @if(!empty($post->files))
                <ul class="files-attached">
                    @foreach($post->files as $file)
                    <li class="file-holder clearfix">
                        <div class="file-thumbnail pull-left">
                            <a href="/file/{{ $file->file_library_id }}">
                                @if(substr($file->mime_type, 0, 5) === 'image')
                                <img src="/assets/thelibrary/{{ $file->file_thumbnail }}">
                                @endif
                                @if(substr($file->mime_type, 0, 5) !== 'image')
                                <img src="/assets/defaults/icons/{{ $file->file_thumbnail }}">
                                @endif
                            </a>
                        </div>
                        <div class="file-details pull-left">
                            <a href="/file/{{ $file->file_library_id }}">{{ $file->file_name }}</a>
                            <span class="file-type">
                                {{ strtoupper($file->file_extension) }} File
                            </span>
                            <div class="file-attached-controls">
                                <a href="#" data-toggle="tooltip" title="Add to The Library">
                                    <i class="fa fa-archive"></i>
                                </a>
                                <a href="/file/{{ $file->file_library_id }}" data-toggle="tooltip"
                                title="Download File">
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
            <div class="clearfix"></div>
            <div class="user-post-likes"></div>
            <div class="post-etcs">
                <ul class="post-etcs-holder">
                    <li>
                        <a href="#" class="like-post"
                        data-post-id="{{ $post->post_id }}"><i class="fa fa-thumbs-up"></i> Like it</a>
                    </li>
                    <li>
                        <a href="#" class="show-comment-form" data-post-id="{{ $post->post_id }}">
                            <i class="fa fa-comment"></i> Reply
                        </a>
                    </li>
                    <li><a href="#"><i class="fa fa-clock-o"></i> {{ $postTimestamp }}</a></li>
                </ul>
            </div>
            @include('plugins.comments')
        </li>
        @endforeach
        @else
        <li class="post-holder no-post-found">
            No post found :(
        </li>
        @endif
    </ul>
</div>
