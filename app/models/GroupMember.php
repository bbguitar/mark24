<?php //-->

class GroupMember extends Eloquent {
    protected $table = 'group_members';

    public function member() {
        return $this->belongsTo('User');
    }

    public static function getGroupMembers($groupId) {
        $members = GroupMember::where('group_id', '=', $groupId)
            ->join('users', 'group_members.group_member_id', '=', 'users.id')
            ->orderBy('users.USERname', 'ASC')
            ->get();

        return $members;
    }

    public static function getAllGroupMembers() {
        return DB::select('SELECT t3.id, t3.name, t3.firstname, t3.lastname
                            FROM group_members as t1,
                            (SELECT groups.group_id FROM groups
                            INNER JOIN group_members
                            ON groups.group_id = group_members.group_id
                            WHERE group_members.group_member_id = ?) as t2,
                            users as t3
                            WHERE t1.group_id = t2.group_id
                            AND t1.group_member_id = t3.id
                            AND t3.id != ?
                            GROUP BY t1.group_member_id',
                            array(Auth::user()->id, Auth::user()->id));
    }
    
    public static function getRecipientGroupMembers()
    {
        
    }
}
