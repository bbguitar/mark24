<?php //-->

class AjaxGroupController extends BaseController
{
    public function lockGroup()
    {
        $groupId = Input::get('group_id');
        // find group
        $group = Group::find($groupId);
        $group->group_code = 'LOCKED';
        $group->save();

        return Response::json(array('error' => false));
    }

    public function changeGroupCode()
    {
        $groupId = Input::get('group_id');

        $newGroupCode = $this->_generateGroupCode();

        // find group
        $group = Group::find($groupId);
        $group->group_code = $newGroupCode;
        $group->save();

        return Response::json(array(
            'error'         => false,
            'group_code'    => $newGroupCode));
    }

    public function joinTheUser()
    {
        $input = Input::all();

        // add the user to the group
        $member = new GroupMember;
        $member->group_id = $input['group_id'];
        $member->group_member_id = $input['user_id'];
        $member->save();

        // remove the request notification
        Notification::where('notification_type', '=', 'request_join_group')
           ->where('sender_id', '=', $input['user_id'])
           ->where('involved_id', '=', $input['group_id'])
           ->delete();

        // unset the request
        $inquire = Inquire::where('type', '=', 'request_join_group')
            ->where('inquirer_id', '=', $input['user_id'])
            ->where('involved_id', '=', $input['group_id'])
            ->first();
        // update
        $inquire->status = 0;
        $inquire->save();
        // create notification
        Notification::setup('accepted_join_group', array(
            'user_id'   => $input['user_id'],
            'group_id'  => $input['group_id']));
        // get user details
        $user = User::find($input['user_id']);

        return Response::json(array('error' => false, 'name' => $user->name));
    }

    protected function _generateGroupCode() {
        $length = 6;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';

        $randomString = '';
        do {
           for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
        } while(Group::where('group_code', '=', $randomString)->first());

        return $randomString;
    }
}
