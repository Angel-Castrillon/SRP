<?php
namespace App\Services;

use App\Repositories\BookRepository;

class BookService{

    public function __construct(
        private BookRepository $bookRepository
    ) {}
    
    public function getAll(){
        return $this->bookRepository->getAll();
    }

    public function findById(int $id){
        return $this->bookRepository->findById($id);
    }

    public function create(array $data){
        if ($data['available_copies'] < 0){
            throw new \InvalidArgumentException('Las copias disponibles no pueden ser negativas');
        };
        return $this->bookRepository->create($data);
    }

    public function update(int $id, array $data){
        $this->bookRepository->findById($id);

        if (isset($data['available_copies']) && $data['available_copies'] < 0){
            throw new \InvalidArgumentException('Las copias disponibles no pueden ser negativas');
        };
        return $this->bookRepository->update($id, $data);

        }

        public function delete(int $id){
            return $this->bookRepository->delete($id);
        }

        // Reglas de negocio especificas



        // Verifica si hay copias disponibles
        public function isAvailable(int $id): bool{
            $book = $this->bookRepository->findById($id);
            return $book->available_copies > 0;
        }

        // Descuenta una copia al prestar un libro
        public function dreacreseCopies(int $id){
            $book = $this->bookRepository->findById($id);

            if($book->available_copies <= 0){
                throw new \Exception('No hay copias disponibles para este libro');
            }
            $this->bookRepository->update($id, [
                'available_copies' => $book->available_copies - 1
            ]);
        }

        // Suma una copia al devolver un libro
        public function increaseCopies(int $id){
            $book = $this->bookRepository->findById($id);

            $this->bookRepository->update($id, [
                'available_copies' => $book->available_copies + 1
            ]);
        }

    }