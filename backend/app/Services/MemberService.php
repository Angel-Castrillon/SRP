<?php
namespace App\Services;

use Illuminate\Support\Facades\Hash;

class MemberService{

    public function __construct(
        private $memberRepository
    ) {}
    
    public function getAll(){
        return $this->memberRepository->getAll();
    }

    public function findById(int $id){
        return $this->memberRepository->findById($id);
    }

    public function create(array $data){

        $data['emal'] = strtolower($data['email']);
        return $this->memberRepository->create($data);
    }

    public function update(int $id, array $data){
        $this->memberRepository->findById($id);

        if (isset($data['email'])){
            $data['email'] = strtolower($data['email']);
        }
       
        return $this->memberRepository->update($id, $data);
       
    }

}