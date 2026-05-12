<?php

namespace App\Repositories;

use App\Models\Member;

class MemberRepository {

    public function getAll(){
        return Member::all();
    }

    public function findById(int $id){
        return Member::findOrFail($id);
    }

    public function create(array $data){
        return Member::create($data);
    }

    public function update(int $id, array $data){
        $member = Member::findOrFail($id);
        $member->update($data);
        return $member;
    }

    public function delete(int $id){
        Member::destroy($id);
    }

}