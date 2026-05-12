<?php

namespace App\Repositories;

use App\Models\Book;

class BookRepository {

    public function getAll(){
        return Book::all();
    }

    public function findById(int $id){
        return Book::findOrFail($id);
    }

    public function create(array $data){
        return Book::create($data);
    }

    public function update(int $id, array $data){
        $book = Book::findOrFail($id);
        $book->update($data);
        return $book;
    }

    public function delete(int $id){
        Book::destroy($id);
    }

}