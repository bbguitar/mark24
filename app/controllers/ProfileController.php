<?php

class ProfileController extends BaseController
{
    protected $_user = null;

    public function __construct() {
        $this->beforeFilter('auth');
    }

    public function showIndex($user)
    {
        // check if the user var is an int
        if(is_int($user)) {
            // it's a ID of the user
            $this->_user = User::find($user);
        }

        // user is a string and it's the username of the user
        if(!is_int($user)) {
            $this->_user = User::where('username', '=', $user)
                ->first();
        }

        // check if the details is empty
        // most likely it's a false user
        if(empty($this->_user)) return View::make('templates.fourohfour'); // show or redirect to 404
        // parse the template
        return View::make('profile.index')
            ->with('user', $this->_user)
            ->with('details', $this->_details())
            ->with('people', $this->_people());
    }

    public function showActions($user, $action)
    {
        // check if the user var is an int
        if(is_int($user)) {
            // it's a ID of the user
            $this->_user = User::find($user);
        }

        // user is a string and it's the username of the user
        if(!is_int($user)) {
            $this->_user = User::where('username', '=', $user)
                ->first();
        }

        // check if the details is empty
        // most likely it's a false user
        if(empty($this->_user)) return View::make('templates.fourohfour'); // show or redirect to 404
        // check for the action
        if(!empty($action)) {
            switch($action) {
                // teacher actions
                case 'students' :
                    return $this->_students();
                    break;
                // student actions
                case 'teachers' :
                    return $this->_teachers();
                    break;
                case 'classmates' :
                    return $this->_classmates();
                    break;
                case 'activity' :
                    return $this->_activity();
                    break;
                default :
                    return View::make('templates.fourohfour');
                    break;
            }
        }

    }

    protected function _details()
    {
        $user = $this->_user;
        $details = new StdClass();
        $groups = Group::getMyGroupsId($this->_user->id);

        // count the groups of the user
        $details->group_count = User::find($user->id)->groupMember->count();
        // check first the user
        if($user->account_type == 1) {
            // teacher
            // count students
            if(empty($groups)) {
                $details->student_count = 0;
            }

            if(!empty($groups)) {
                $details->student_count = GroupMember::whereIn('group_id', Group::getMyGroupsId($user->id))
                    ->where('group_members.group_member_id', '!=', $user->id)
                    ->leftJoin('users', 'group_members.group_member_id', '=', 'users.id')
                    ->groupBy('group_member_id')
                    ->get()
                    ->count();
            }

            // count number of files in the library
            $details->file_count = FileLibrary::where('user_id', '=', $user->id)
                ->get()
                ->count();
        }

        if($user->account_type == 2) {
            // student
            // count post and replies
            $countPost = Post::where('user_id', '=', $user->id)->get()->count();
            $countComment = Comment::where('user_id', '=', $user->id)->get()->count();
            $details->post_replies = $countPost + $countComment;
        }

        return $details;
    }

    // actions
    protected function _students()
    {
        // this page is for teachers only
        if($this->_user->account_type != 1) return View::make('templates.fourohfour');
        $groups = Group::getMyGroupsId($this->_user->id);
        // get the students of the users
        if(empty($groups)) { $students = null; }
        if(!empty($groups)) {
            $students = GroupMember::whereIn('group_id', Group::getMyGroupsId($this->_user->id))
                ->where('group_members.group_member_id', '!=', $this->_user->id)
                ->where('users.account_type', '=', 2)
                ->leftJoin('users', 'group_members.group_member_id', '=', 'users.id')
                ->groupBy('group_member_id')
                ->get();
        }

        return View::make('profile.actions.students')
            ->with('user', $this->_user)
            ->with('details', $this->_details())
            ->with('students', $students);
    }

    protected function _teachers()
    {
        // this page is for students only
        if($this->_user->account_type != 2) return View::make('templates.fourohfour');
        // get the students of the users
        $teachers = GroupMember::whereIn('group_id', Group::getMyGroupsId($this->_user->id))
            ->where('group_members.group_member_id', '!=', $this->_user->id)
            ->where('users.account_type', '=', 1)
            ->leftJoin('users', 'group_members.group_member_id', '=', 'users.id')
            ->groupBy('group_member_id')
            ->get();

        return View::make('profile.actions.teachers')
            ->with('user', $this->_user)
            ->with('details', $this->_details())
            ->with('teachers', $teachers);
    }

    protected function _classmates()
    {
        // this page is for students only
        if($this->_user->account_type != 2) return View::make('templates.fourohfour');

        // get the students of the users
        $classmates = GroupMember::whereIn('group_id', Group::getMyGroupsId($this->_user->id))
            ->where('group_members.group_member_id', '!=', $this->_user->id)
            ->where('users.account_type', '=', 2)
            ->leftJoin('users', 'group_members.group_member_id', '=', 'users.id')
            ->groupBy('group_member_id')
            ->get();

        return View::make('profile.actions.classmates')
            ->with('user', $this->_user)
            ->with('details', $this->_details())
            ->with('classmates', $classmates);
    }

    protected function _activity()
    {
        // this page is for students only
        if($this->_user->account_type != 2) return View::make('templates.fourohfour');

        return View::make('profile.actions.activity')
            ->with('user', $this->_user)
            ->with('details', $this->_details());
    }

    protected function _people()
    {
        $people = new StdClass();
        $groups = Group::getMyGroupsId($this->_user->id);

        if(empty($groups)) return false;

        $people->students = GroupMember::whereIn('group_id', $groups)
            ->where('group_members.group_member_id', '!=', $this->_user->id)
            ->where('users.account_type', '=', 2)
            ->leftJoin('users', 'group_members.group_member_id', '=', 'users.id')
            ->groupBy('group_member_id')
            ->take(20)
            ->get();

        if($this->_user->account_type == 2) {
            $people->teachers = GroupMember::whereIn('group_id', Group::getMyGroupsId($this->_user->id))
                ->where('group_members.group_member_id', '!=', $this->_user->id)
                ->where('users.account_type', '=', 1)
                ->leftJoin('users', 'group_members.group_member_id', '=', 'users.id')
                ->groupBy('group_member_id')
                ->take(10)
                ->get();
        }

        return $people;
    }
}
