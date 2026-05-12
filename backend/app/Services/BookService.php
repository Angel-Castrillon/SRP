<?php
/* namespace App\Services;

use App\Repositories\BookRepository;

class BookService{

    public function __construct(
        private $bookRepository
    ) {}
    
    public function getAll(){
        return $this->bookRepository->getAll();
    }

    public function findById(int $id){
        return $this->bookRepository->findById($id);
    }

    public function create(array $data){

        $data['emal'] = strtolower($data['email']);
        return $this->bookRepository->create($data);
    }

    public function update(int $id, array $data){
        $this->bookRepository->findById($id);

        if (isset($data['email'])){
            $data['email'] = strtolower($data['email']);
        }
       
        return $this->bookRepository->update($id, $data);
       
    }

} */