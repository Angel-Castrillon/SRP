<?php

namespace App\Services;

use App\Repositories\LoanRepository;

class LoanService
{
    public function __construct(
        private LoanRepository $loanRepository,
        private BookService $bookService,
    ) {}

    // Procesar prestamo que descuenta copia de libro

    public function create(array $data){
        if (!$this->bookService->isAvailable($data['book_id'])){
            throw new \Exception('No hay copias disponibles para este libro');
        }

        $loan = $this->loanRepository->create($data);

        // Descontar copia tras crear el prestamo
        $this->bookService->dreacreseCopies($data['book_id']);

        return $loan;
    }

    // Devolver un libro y aumentar copias disponibles

    public function returnLoan(int $loanId): void{
        $loan = $this->loanRepository->findById($loanId);

        // Suma la copia de vuelta

        $this->bookService->increaseCopies($loan->book_id);

        // Eliminar o marca el prestamo como devuelto
        
        $this->loanRepository->delete($loanId);
    }
}